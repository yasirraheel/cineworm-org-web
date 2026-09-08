<?php

namespace App\Services;

use App\User;
use App\UserPromotionalCampaign;
use App\UserPromotionalQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserPromotionalEmailService
{
    /**
     * Queue a promotional campaign for users in database.
     */
    public function queueCampaign($title, $subject, $content, $audience = 'all', $adminId = null, array $specificUserIds = [])
    {
        $campaign = DB::transaction(function () use ($title, $subject, $content, $audience, $adminId, $specificUserIds) {
            $query = User::whereNotNull('email')->where('email', '!=', '');

            if ($audience === 'specific_users' || !empty($specificUserIds)) {
                $query->whereIn('id', $specificUserIds);
            } elseif ($audience === 'active_only') {
                $query->where('status', 1);
                $query->where(function ($q) {
                    $q->where('usertype', 'User')
                      ->orWhereNull('usertype')
                      ->orWhere('usertype', '');
                });
            } else {
                // Target general users (exclude other Admins/Sub_Admins from promotional spam)
                $query->where(function ($q) {
                    $q->where('usertype', 'User')
                      ->orWhereNull('usertype')
                      ->orWhere('usertype', '');
                });
            }

            $users = $query->select('id', 'name', 'email')->get();

            $campaign = UserPromotionalCampaign::create([
                'admin_id' => $adminId,
                'title' => $title ?: $subject,
                'subject' => $subject,
                'content' => $content,
                'audience' => $audience,
                'total_recipients' => $users->count(),
                'sent_count' => 0,
                'failed_count' => 0,
                'status' => $users->count() > 0 ? 'queued' : 'completed',
            ]);

            if ($users->count() > 0) {
                $now = now();
                $rows = [];

                foreach ($users as $u) {
                    $rows[] = [
                        'campaign_id' => $campaign->id,
                        'user_id' => $u->id,
                        'email' => strtolower(trim($u->email)),
                        'name' => $u->name ?: null,
                        'status' => UserPromotionalQueue::STATUS_PENDING,
                        'error_message' => null,
                        'sent_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                foreach (array_chunk($rows, 200) as $chunk) {
                    UserPromotionalQueue::insert($chunk);
                }
            }

            return $campaign;
        });

        // For specific targeted users or small batches (<= 10 recipients), process immediately
        if ($campaign && $campaign->total_recipients > 0 && ($audience === 'specific_users' || $campaign->total_recipients <= 10)) {
            try {
                $this->processBatch(10);
            } catch (\Throwable $e) {
                Log::warning('[UserPromotionalEmailService] Immediate batch process error: ' . $e->getMessage());
            }
        }

        return $campaign;
    }

    /**
     * Process next batch of queued emails (called by TaskCron every minute).
     */
    public function processBatch($batchSize = 30)
    {
        $queueItems = UserPromotionalQueue::with('campaign')
            ->where('status', UserPromotionalQueue::STATUS_PENDING)
            ->orderBy('id', 'asc')
            ->limit($batchSize)
            ->get();

        if ($queueItems->isEmpty()) {
            return 0;
        }

        $processed = 0;
        $campaignIds = [];

        foreach ($queueItems as $item) {
            $campaign = $item->campaign;
            if (!$campaign) {
                $item->status = UserPromotionalQueue::STATUS_FAILED;
                $item->error_message = 'Campaign record missing';
                $item->save();
                continue;
            }

            $campaignIds[$campaign->id] = $campaign;

            if ($campaign->status === 'queued') {
                $campaign->status = 'processing';
                $campaign->save();
            }

            $subject = $campaign->subject;
            $recipientEmail = $item->email;
            $recipientName = $item->name ?: 'Valued Member';
            $bodyContent = $campaign->content;

            try {
                Mail::send('emails.newsletter', [
                    'subject' => $subject,
                    'name' => $recipientName,
                    'body_content' => $bodyContent,
                    'unsubscribe_url' => url('/'),
                ], function ($message) use ($recipientEmail, $recipientName, $subject) {
                    $message->to($recipientEmail, $recipientName)
                            ->from(getcong('site_email'), getcong('site_name'))
                            ->subject($subject);
                });

                $item->status = UserPromotionalQueue::STATUS_SENT;
                $item->sent_at = now();
                $item->save();

                $campaign->increment('sent_count');
                $processed++;
            } catch (\Throwable $e) {
                Log::warning("User promo email failed for {$recipientEmail}: " . $e->getMessage());

                $item->status = UserPromotionalQueue::STATUS_FAILED;
                $item->error_message = substr($e->getMessage(), 0, 500);
                $item->save();

                $campaign->increment('failed_count');
            }
        }

        // Check if affected campaigns are now fully processed
        foreach ($campaignIds as $campId => $camp) {
            $hasRemaining = UserPromotionalQueue::where('campaign_id', $campId)
                ->where('status', UserPromotionalQueue::STATUS_PENDING)
                ->exists();

            if (!$hasRemaining) {
                $camp->status = 'completed';
                $camp->save();
            }
        }

        return $processed;
    }
}
