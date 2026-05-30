<?php

namespace App\Services;

use App\WhatsappCampaign;
use App\WhatsappCampaignSend;
use App\WhatsappContact;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WhatsappCampaignService
{
    public function launchCampaign(WhatsappCampaign $campaign)
    {
        DB::transaction(function () use ($campaign) {
            if ($campaign->sends()->count() === 0) {
                $contacts = WhatsappContact::where('contact_list_id', $campaign->contact_list_id)
                    ->where('status', 1)
                    ->whereNull('opt_out_at')
                    ->orderBy('id')
                    ->get();

                $rows = [];
                foreach ($contacts as $contact) {
                    $rows[] = [
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'phone' => $contact->phone,
                        'message' => $this->renderMessage($campaign->message, $contact),
                        'status' => WhatsappCampaignSend::STATUS_PENDING,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach (array_chunk($rows, 200) as $chunk) {
                    WhatsappCampaignSend::insert($chunk);
                }

                $campaign->total_contacts = count($rows);
                $campaign->processed_contacts = 0;
                $campaign->success_count = 0;
                $campaign->failed_count = 0;
                $campaign->skipped_count = 0;
            }

            if ($campaign->scheduled_at && $campaign->scheduled_at->gt(Carbon::now())) {
                $campaign->status = WhatsappCampaign::STATUS_SCHEDULED;
            } else {
                $campaign->status = WhatsappCampaign::STATUS_RUNNING;
                $campaign->started_at = $campaign->started_at ?: now();
            }

            $campaign->last_error = null;
            $campaign->save();
        });

        if ($campaign->status === WhatsappCampaign::STATUS_RUNNING) {
            $this->processCampaignBatch($campaign->fresh(), min(3, (int) $campaign->batch_size));
        }

        return $campaign->fresh();
    }

    public function processDueCampaigns($limitCampaigns = 3, $batchSize = 10)
    {
        WhatsappCampaign::where('status', WhatsappCampaign::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get()
            ->each(function (WhatsappCampaign $campaign) {
                $campaign->status = WhatsappCampaign::STATUS_RUNNING;
                $campaign->started_at = $campaign->started_at ?: now();
                $campaign->save();
            });

        WhatsappCampaign::where('status', WhatsappCampaign::STATUS_RUNNING)
            ->orderBy('id')
            ->limit($limitCampaigns)
            ->get()
            ->each(function (WhatsappCampaign $campaign) use ($batchSize) {
                $this->processCampaignBatch($campaign, min((int) $campaign->batch_size, $batchSize));
            });
    }

    public function processCampaignBatch(WhatsappCampaign $campaign, $batchSize = 10)
    {
        if ($this->isInsideQuietHours($campaign)) {
            return;
        }

        $status = $this->requestServer('get', '/status');
        if (!($status['connected'] ?? false)) {
            $campaign->last_error = 'WhatsApp is not connected. Connect WhatsApp Web before running campaigns.';
            $campaign->save();
            return;
        }

        $allowedBatchSize = $this->getAllowedBatchSize($campaign, $batchSize);
        if ($allowedBatchSize <= 0) {
            return;
        }

        $pendingSends = WhatsappCampaignSend::where('campaign_id', $campaign->id)
            ->where('status', WhatsappCampaignSend::STATUS_PENDING)
            ->orderBy('id')
            ->limit($allowedBatchSize)
            ->get();

        if ($pendingSends->isEmpty()) {
            $this->completeCampaignIfDone($campaign->fresh());
            return;
        }

        foreach ($pendingSends as $index => $send) {
            try {
                if ($index > 0) {
                    $this->waitBeforeNextSend($campaign);
                }

                $response = $this->requestServer('post', '/send', [
                    'number' => $send->phone,
                    'message' => $send->message,
                    'validateNumber' => true,
                    'typingPresence' => true,
                ]);

                if (!($response['ok'] ?? false)) {
                    throw new \RuntimeException($response['error'] ?? 'WhatsApp server rejected the message.');
                }

                $send->status = WhatsappCampaignSend::STATUS_SENT;
                $send->attempts = (int) $send->attempts + 1;
                $send->sent_at = now();
                $send->error_message = null;
                $send->provider_response = $this->compactProviderResponse($response);
                $send->save();

                if ($send->contact) {
                    $send->contact->last_sent_at = now();
                    $send->contact->save();
                }

                $campaign->processed_contacts = (int) $campaign->processed_contacts + 1;
                $campaign->success_count = (int) $campaign->success_count + 1;
                $campaign->last_error = null;
                $campaign->save();
            } catch (\Throwable $exception) {
                $send->status = WhatsappCampaignSend::STATUS_FAILED;
                $send->attempts = (int) $send->attempts + 1;
                $send->error_message = $this->truncateError($exception->getMessage());
                $send->save();

                $campaign->processed_contacts = (int) $campaign->processed_contacts + 1;
                $campaign->failed_count = (int) $campaign->failed_count + 1;
                $campaign->last_error = $this->truncateError($exception->getMessage());
                $campaign->save();

                \Log::warning('WhatsApp campaign send failed.', [
                    'campaign_id' => $campaign->id,
                    'send_id' => $send->id,
                    'phone' => $send->phone,
                    'error' => $this->truncateError($exception->getMessage()),
                ]);
            }
        }

        $this->completeCampaignIfDone($campaign->fresh());
    }

    protected function getAllowedBatchSize(WhatsappCampaign $campaign, $requestedBatchSize): int
    {
        $requestedBatchSize = max(1, (int) $requestedBatchSize);
        $now = Carbon::now();

        $sentQuery = WhatsappCampaignSend::where('status', WhatsappCampaignSend::STATUS_SENT)
            ->whereHas('campaign', function ($query) use ($campaign) {
                $query->where('id', $campaign->id);
            });

        $sentToday = (clone $sentQuery)
            ->where('sent_at', '>=', $now->copy()->startOfDay())
            ->count();

        if ((int) $campaign->daily_limit > 0) {
            $remainingToday = (int) $campaign->daily_limit - $sentToday;
            if ($remainingToday <= 0) {
                return 0;
            }
            $requestedBatchSize = min($requestedBatchSize, $remainingToday);
        }

        $lastSentAt = (clone $sentQuery)->max('sent_at');
        $lastSentAt = $lastSentAt ? Carbon::parse($lastSentAt) : null;
        $pauseAfter = (int) $campaign->pause_after_messages;
        $pauseDuration = (int) $campaign->pause_duration_seconds;

        if ($pauseAfter > 0 && $pauseDuration > 0 && $lastSentAt && $sentToday > 0 && $sentToday % $pauseAfter === 0) {
            if ($lastSentAt->copy()->addSeconds($pauseDuration)->gt($now)) {
                return 0;
            }
        }

        if ($pauseAfter > 0) {
            $messagesUntilPause = $pauseAfter - ($sentToday % $pauseAfter);
            if ($messagesUntilPause > 0) {
                $requestedBatchSize = min($requestedBatchSize, $messagesUntilPause);
            }
        }

        return $requestedBatchSize;
    }

    protected function waitBeforeNextSend(WhatsappCampaign $campaign): void
    {
        $minDelay = max(0, (int) $campaign->min_delay_seconds);
        $maxDelay = max($minDelay, (int) $campaign->max_delay_seconds);

        if ($maxDelay <= 0) {
            return;
        }

        sleep($maxDelay > $minDelay ? random_int($minDelay, $maxDelay) : $maxDelay);
    }

    protected function isInsideQuietHours(WhatsappCampaign $campaign): bool
    {
        if (empty($campaign->quiet_hours_start) || empty($campaign->quiet_hours_end)) {
            return false;
        }

        $now = Carbon::now()->format('H:i:s');
        $start = $campaign->quiet_hours_start;
        $end = $campaign->quiet_hours_end;

        if ($start < $end) {
            return $now >= $start && $now <= $end;
        }

        return $now >= $start || $now <= $end;
    }

    protected function renderMessage(string $message, WhatsappContact $contact): string
    {
        return strtr($message, [
            '{{name}}' => $contact->name ?: '',
            '{{phone}}' => $contact->phone,
            '{{company}}' => $contact->company ?: '',
            '{{tags}}' => $contact->tags ?: '',
        ]);
    }

    protected function completeCampaignIfDone(WhatsappCampaign $campaign): void
    {
        $pendingCount = WhatsappCampaignSend::where('campaign_id', $campaign->id)
            ->where('status', WhatsappCampaignSend::STATUS_PENDING)
            ->count();

        if ($pendingCount > 0) {
            return;
        }

        $campaign->status = WhatsappCampaign::STATUS_COMPLETED;
        $campaign->completed_at = now();
        $campaign->save();
    }

    protected function requestServer($method, $path, array $payload = [])
    {
        try {
            $url = rtrim(config('whatsapp.server_url'), '/') . $path;
            $client = Http::timeout(max(15, (int) config('whatsapp.timeout')))
                ->acceptJson()
                ->withHeaders([
                    'x-api-key' => config('whatsapp.api_key'),
                ]);

            $response = $method === 'post'
                ? $client->post($url, $payload)
                : $client->get($url);

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'error' => $response->json('error') ?: 'WhatsApp server returned HTTP ' . $response->status(),
                ];
            }

            return $response->json() ?: ['ok' => false, 'error' => 'Empty WhatsApp server response.'];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'error' => $exception->getMessage(),
            ];
        }
    }

    protected function compactProviderResponse(array $response): array
    {
        unset($response['response']['message']);

        return $response;
    }

    protected function truncateError($message): string
    {
        return mb_substr((string) $message, 0, 1000);
    }
}
