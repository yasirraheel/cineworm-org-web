<?php

namespace App\Services;

use App\PromotionalCampaign;
use App\PromotionalCampaignSend;
use App\PromotionalContact;
use App\PromotionalSmtpServer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PromotionalCampaignService
{
    public function launchCampaign(PromotionalCampaign $campaign)
    {
        DB::transaction(function () use ($campaign) {
            if ($campaign->sends()->count() === 0) {
                $contacts = PromotionalContact::where('contact_list_id', $campaign->contact_list_id)
                    ->where('user_id', $campaign->user_id)
                    ->where('status', 1)
                    ->orderBy('id')
                    ->get();

                $rows = [];
                foreach ($contacts as $contact) {
                    $rows[] = [
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'email' => $contact->email,
                        'subject' => $campaign->subject,
                        'status' => PromotionalCampaignSend::STATUS_PENDING,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach (array_chunk($rows, 200) as $chunk) {
                    PromotionalCampaignSend::insert($chunk);
                }

                $campaign->total_contacts = count($rows);
            }

            if ($campaign->scheduled_at && $campaign->scheduled_at->gt(Carbon::now())) {
                $campaign->status = PromotionalCampaign::STATUS_SCHEDULED;
            } else {
                $campaign->status = PromotionalCampaign::STATUS_RUNNING;
                $campaign->started_at = $campaign->started_at ?: now();
            }

            $campaign->save();
        });

        if ($campaign->status === PromotionalCampaign::STATUS_RUNNING) {
            $this->processCampaignBatch($campaign->fresh(), 10);
        }

        return $campaign->fresh();
    }

    public function processDueCampaigns($limitCampaigns = 5, $batchSize = 25)
    {
        PromotionalCampaign::where('status', PromotionalCampaign::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get()
            ->each(function (PromotionalCampaign $campaign) {
                $campaign->status = PromotionalCampaign::STATUS_RUNNING;
                $campaign->started_at = $campaign->started_at ?: now();
                $campaign->save();
            });

        PromotionalCampaign::where('status', PromotionalCampaign::STATUS_RUNNING)
            ->orderBy('id')
            ->limit($limitCampaigns)
            ->get()
            ->each(function (PromotionalCampaign $campaign) use ($batchSize) {
                $this->processCampaignBatch($campaign, $batchSize);
            });
    }

    public function processCampaignBatch(PromotionalCampaign $campaign, $batchSize = 25)
    {
        $server = PromotionalSmtpServer::find($campaign->smtp_server_id);
        if (!$server || !$server->status || empty($server->decrypted_password)) {
            $campaign->status = PromotionalCampaign::STATUS_FAILED;
            $campaign->last_error = 'Promotional SMTP server is missing or inactive.';
            $campaign->save();
            return;
        }

        $pendingSends = PromotionalCampaignSend::where('campaign_id', $campaign->id)
            ->where('status', PromotionalCampaignSend::STATUS_PENDING)
            ->orderBy('id')
            ->limit($batchSize)
            ->get();

        if ($pendingSends->isEmpty()) {
            $this->completeCampaignIfDone($campaign->fresh());
            return;
        }

        $this->configureMailer($server, $campaign);

        foreach ($pendingSends as $send) {
            try {
                Mail::mailer('smtp')->send([], [], function ($message) use ($campaign, $server, $send) {
                    $message->to($send->email)
                        ->from($campaign->from_email ?: $server->sender_email, $campaign->from_name ?: $server->from_name ?: $server->server_name)
                        ->subject($campaign->subject)
                        ->setBody($campaign->html_content, 'text/html');

                    if (!empty($campaign->reply_to_email)) {
                        $message->replyTo($campaign->reply_to_email);
                    } elseif (!empty($server->reply_to_email)) {
                        $message->replyTo($server->reply_to_email);
                    }
                });

                $send->status = PromotionalCampaignSend::STATUS_SENT;
                $send->sent_at = now();
                $send->error_message = null;
                $send->save();

                $campaign->processed_contacts = (int) $campaign->processed_contacts + 1;
                $campaign->success_count = (int) $campaign->success_count + 1;
                $campaign->last_error = null;
                $campaign->save();
            } catch (\Throwable $exception) {
                $send->status = PromotionalCampaignSend::STATUS_FAILED;
                $send->error_message = $exception->getMessage();
                $send->save();

                $campaign->processed_contacts = (int) $campaign->processed_contacts + 1;
                $campaign->failed_count = (int) $campaign->failed_count + 1;
                $campaign->last_error = $exception->getMessage();
                $campaign->save();
            }
        }

        $this->completeCampaignIfDone($campaign->fresh());
    }

    protected function completeCampaignIfDone(PromotionalCampaign $campaign)
    {
        $pendingCount = PromotionalCampaignSend::where('campaign_id', $campaign->id)
            ->where('status', PromotionalCampaignSend::STATUS_PENDING)
            ->count();

        if ($pendingCount === 0 && $campaign->status === PromotionalCampaign::STATUS_RUNNING) {
            $campaign->status = PromotionalCampaign::STATUS_COMPLETED;
            $campaign->completed_at = now();
            $campaign->save();
        }
    }

    protected function configureMailer(PromotionalSmtpServer $server, PromotionalCampaign $campaign)
    {
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $server->host);
        Config::set('mail.mailers.smtp.port', $server->port);
        Config::set('mail.mailers.smtp.encryption', $server->encryption ?: null);
        Config::set('mail.mailers.smtp.username', $server->username);
        Config::set('mail.mailers.smtp.password', $server->decrypted_password);
        Config::set('mail.mailers.smtp.local_domain', $server->ehlo_domain ?: null);
        Config::set('mail.from.address', $campaign->from_email ?: $server->sender_email);
        Config::set('mail.from.name', $campaign->from_name ?: $server->from_name ?: $server->server_name);

        if (app()->bound('mail.manager') && method_exists(app('mail.manager'), 'forgetMailers')) {
            app('mail.manager')->forgetMailers();
        }
    }
}
