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

        $allowedBatchSize = $this->getAllowedBatchSize($server, $batchSize);

        if ($allowedBatchSize <= 0) {
            return;
        }

        $pendingSends = PromotionalCampaignSend::where('campaign_id', $campaign->id)
            ->where('status', PromotionalCampaignSend::STATUS_PENDING)
            ->orderBy('id')
            ->limit($allowedBatchSize)
            ->get();

        if ($pendingSends->isEmpty()) {
            $this->completeCampaignIfDone($campaign->fresh());
            return;
        }

        $this->configureMailer($server, $campaign);

        $preferredFromEmail = strtolower(trim((string) ($campaign->from_email ?: '')));
        $preferredFromName = trim((string) ($campaign->from_name ?: ''));
        $serverFromEmail = strtolower(trim((string) ($server->sender_email ?: '')));
        $serverFromName = trim((string) ($server->from_name ?: $server->server_name ?: ''));

        if (empty($preferredFromEmail) && empty($serverFromEmail)) {
            $campaign->status = PromotionalCampaign::STATUS_FAILED;
            $campaign->last_error = 'No sender email is configured for this campaign or SMTP server.';
            $campaign->save();

            return;
        }

        foreach ($pendingSends as $send) {
            try {
                $fromEmail = $preferredFromEmail ?: $serverFromEmail;
                $fromName = $preferredFromName ?: $serverFromName;

                try {
                    $this->sendCampaignEmail($campaign, $server, $send, $fromEmail, $fromName);
                } catch (\Throwable $firstException) {
                    $canRetryWithServerSender = !empty($preferredFromEmail)
                        && !empty($serverFromEmail)
                        && strcasecmp($preferredFromEmail, $serverFromEmail) !== 0;

                    if (!$canRetryWithServerSender) {
                        throw $firstException;
                    }

                    try {
                        $this->sendCampaignEmail($campaign, $server, $send, $serverFromEmail, $serverFromName);
                        \Log::warning('Campaign send retried with SMTP sender address.', [
                            'campaign_id' => $campaign->id,
                            'send_id' => $send->id,
                            'preferred_from' => $preferredFromEmail,
                            'fallback_from' => $serverFromEmail,
                            'initial_error' => $this->truncateError($firstException->getMessage()),
                        ]);
                    } catch (\Throwable $secondException) {
                        throw new \RuntimeException(
                            'Initial send failed: '.$this->truncateError($firstException->getMessage())
                            .' | Fallback failed: '.$this->truncateError($secondException->getMessage())
                        );
                    }
                }

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
                $send->error_message = $this->truncateError($exception->getMessage());
                $send->save();

                $campaign->processed_contacts = (int) $campaign->processed_contacts + 1;
                $campaign->failed_count = (int) $campaign->failed_count + 1;
                $campaign->last_error = $this->truncateError($exception->getMessage());
                $campaign->save();

                \Log::error('Campaign send failed.', [
                    'campaign_id' => $campaign->id,
                    'send_id' => $send->id,
                    'recipient' => $send->email,
                    'error' => $this->truncateError($exception->getMessage()),
                ]);
            }
        }

        $this->completeCampaignIfDone($campaign->fresh());
    }

    protected function getAllowedBatchSize(PromotionalSmtpServer $server, $requestedBatchSize): int
    {
        $requestedBatchSize = max(1, (int) $requestedBatchSize);
        $now = Carbon::now();

        $sentQuery = PromotionalCampaignSend::where('status', PromotionalCampaignSend::STATUS_SENT)
            ->whereHas('campaign', function ($query) use ($server) {
                $query->where('smtp_server_id', $server->id);
            });

        $sentToday = (clone $sentQuery)
            ->where('sent_at', '>=', $now->copy()->startOfDay())
            ->count();

        if ((int) $server->max_messages_per_day > 0) {
            $remainingToday = (int) $server->max_messages_per_day - $sentToday;

            if ($remainingToday <= 0) {
                return 0;
            }

            $requestedBatchSize = min($requestedBatchSize, $remainingToday);
        }

        $lastSentAt = (clone $sentQuery)->max('sent_at');
        $lastSentAt = $lastSentAt ? Carbon::parse($lastSentAt) : null;

        $minDelay = max(0, (int) $server->min_delay_per_message);
        $maxDelay = max($minDelay, (int) $server->max_delay_per_message);

        if ($maxDelay > 0) {
            $delaySeconds = $minDelay > 0 ? $minDelay : $maxDelay;

            if ($lastSentAt) {
                if ($lastSentAt->copy()->addSeconds($delaySeconds)->gt($now)) {
                    return 0;
                }
            }

            return min($requestedBatchSize, 1);
        }

        $pauseAfterMessages = (int) $server->pause_after_messages;
        $pauseDuration = (int) $server->pause_duration;

        if ($pauseAfterMessages > 0 && $pauseDuration > 0) {
            $counterMessages = $sentToday;

            if ((int) $server->reset_counter_after_messages > 0 && $sentToday > 0) {
                $counterMessages = $sentToday % (int) $server->reset_counter_after_messages;
                $counterMessages = $counterMessages === 0 ? (int) $server->reset_counter_after_messages : $counterMessages;
            }

            if ($lastSentAt && $counterMessages > 0 && $counterMessages % $pauseAfterMessages === 0) {
                if ($lastSentAt->copy()->addSeconds($pauseDuration)->gt($now)) {
                    return 0;
                }
            }

            $messagesUntilPause = $pauseAfterMessages - ($counterMessages % $pauseAfterMessages);

            if ($messagesUntilPause > 0) {
                $requestedBatchSize = min($requestedBatchSize, $messagesUntilPause);
            }
        }

        return $requestedBatchSize;
    }

    protected function completeCampaignIfDone(PromotionalCampaign $campaign)
    {
        $pendingCount = PromotionalCampaignSend::where('campaign_id', $campaign->id)
            ->where('status', PromotionalCampaignSend::STATUS_PENDING)
            ->count();

        if ($pendingCount === 0 && $campaign->status === PromotionalCampaign::STATUS_RUNNING) {
            $campaign->status = ((int) $campaign->success_count === 0 && (int) $campaign->failed_count > 0)
                ? PromotionalCampaign::STATUS_FAILED
                : PromotionalCampaign::STATUS_COMPLETED;
            $campaign->completed_at = now();
            $campaign->save();
        }
    }

    protected function sendCampaignEmail(PromotionalCampaign $campaign, PromotionalSmtpServer $server, PromotionalCampaignSend $send, $fromEmail, $fromName)
    {
        Mail::mailer('smtp')->send([], [], function ($message) use ($campaign, $server, $send, $fromEmail, $fromName) {
            $message->to($send->email)
                ->from($fromEmail, $fromName)
                ->subject($campaign->subject);

            $message->html((string) $campaign->html_content);

            if (!empty($campaign->plain_text)) {
                $message->text((string) $campaign->plain_text);
            }

            if (!empty($campaign->reply_to_email)) {
                $message->replyTo($campaign->reply_to_email);
            } elseif (!empty($server->reply_to_email)) {
                $message->replyTo($server->reply_to_email);
            }
        });
    }

    protected function truncateError($message, $limit = 1000)
    {
        $cleanMessage = trim((string) $message);

        return strlen($cleanMessage) > $limit
            ? substr($cleanMessage, 0, $limit - 3).'...'
            : $cleanMessage;
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
