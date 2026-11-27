<?php

namespace App\Services;

use App\FilmPurchase;
use App\Models\GoogleDriveApi;
use App\Movies;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CreatorFilmService
{
    public function isPaidFilm(Movies $movie): bool
    {
        return $movie->isCreatorPaidFilm();
    }

    public function canWatch(Movies $movie, ?int $userId): bool
    {
        if (!$this->isPaidFilm($movie) || !$userId) {
            return !$this->isPaidFilm($movie);
        }

        $user = \App\Models\User::find($userId);
        if ($user && in_array($user->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            return true;
        }

        if ((int) $movie->added_by === $userId) {
            return true;
        }

        return FilmPurchase::paid()
            ->where('movie_id', $movie->id)
            ->where('buyer_id', $userId)
            ->exists();
    }

    public function totals(Movies $movie): array
    {
        $settings = Settings::find(1);
        $currency = strtoupper((string) ($movie->creator_currency ?: getcong('currency_code') ?: 'USD'));
        $filmAmount = round((float) $movie->creator_price, 2);
        $percent = round((float) ($settings->creator_sale_fee_percent ?? 0), 2);
        $fixed = round((float) ($settings->creator_sale_fee_fixed ?? 0), 2);
        $fee = round($fixed + (($filmAmount * $percent) / 100), 2);
        $included = (bool) ($settings->creator_sale_fee_included ?? true);

        return [
            'currency' => $currency,
            'film_amount' => $filmAmount,
            'platform_fee' => $included ? min($filmAmount, $fee) : $fee,
            'total_amount' => $included ? $filmAmount : round($filmAmount + $fee, 2),
            'fee_included' => $included,
        ];
    }

    public function createPurchase(Movies $movie, int $buyerId, string $gateway): FilmPurchase
    {
        $totals = $this->totals($movie);

        return FilmPurchase::create([
            'movie_id' => $movie->id,
            'buyer_id' => $buyerId,
            'seller_id' => $movie->added_by,
            'gateway' => $gateway,
            'currency' => $totals['currency'],
            'film_amount' => $totals['film_amount'],
            'platform_fee' => $totals['platform_fee'],
            'total_amount' => $totals['total_amount'],
            'status' => 'pending',
        ]);
    }

    public function markPaid(FilmPurchase $purchase, ?string $providerPaymentId = null, array $metadata = []): FilmPurchase
    {
        return \DB::transaction(function () use ($purchase, $providerPaymentId, $metadata) {
            $purchase = FilmPurchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            if ($purchase->status !== 'paid') {
                $purchase->status = 'paid';
                $purchase->provider_payment_id = $providerPaymentId ?: $purchase->provider_payment_id;
                $purchase->paid_at = now();
                $purchase->metadata = array_merge((array) $purchase->metadata, $metadata);
                $purchase->save();
            }

            return $purchase;
        });
    }

    public function markFailed(FilmPurchase $purchase, string $reason = ''): void
    {
        if ($purchase->status === 'paid') {
            return;
        }

        $purchase->status = 'failed';
        $purchase->metadata = array_merge((array) $purchase->metadata, ['failure_reason' => $reason]);
        $purchase->save();
    }

    public function driveFileId(Movies $movie): ?string
    {
        if (!empty($movie->file_id)) {
            return trim((string) $movie->file_id);
        }

        $url = (string) $movie->video_url;
        $patterns = [
            '/drive\.google\.com\/file\/d\/([^\/]+)/i',
            '/drive\.google\.com\/open\?id=([^&]+)/i',
            '/drive\.google\.com\/uc\?(?:[^#]*&)?id=([^&]+)/i',
            '/googleapis\.com\/drive\/v3\/files\/([^?]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches) && !empty($matches[1])) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    public function streamFromDrive(Movies $movie, Request $request)
    {
        $fileId = $this->driveFileId($movie);
        $api = GoogleDriveApi::orderBy('calls')->first();

        if (!$fileId || !$api || empty($api->api_key)) {
            abort(503, 'Protected video source is not configured.');
        }

        $api->increment('calls');
        $key = $api->api_key;
        $metadataUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?fields=size,mimeType&key=" . rawurlencode($key);
        $metadataResponse = Http::timeout(15)->get($metadataUrl);
        $metadata = $metadataResponse->successful() ? $metadataResponse->json() : [];
        $totalSize = isset($metadata['size']) ? (int) $metadata['size'] : null;
        $mime = $metadata['mimeType'] ?? 'video/mp4';

        $range = $request->header('Range');
        $status = 200;
        $headers = [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($range && preg_match('/bytes=(\d+)-(\d*)/i', $range, $matches)) {
            $start = (int) $matches[1];
            $end = ($matches[2] !== '' ? (int) $matches[2] : ($totalSize ? $totalSize - 1 : $start + 5 * 1024 * 1024));
            if ($totalSize) {
                $end = min($end, $totalSize - 1);
                $headers['Content-Range'] = "bytes {$start}-{$end}/{$totalSize}";
                $headers['Content-Length'] = (string) ($end - $start + 1);
            }
            $headers['Content-Disposition'] = 'inline';
            $status = 206;
        } elseif ($totalSize) {
            $headers['Content-Length'] = (string) $totalSize;
        }

        $mediaUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key=" . rawurlencode($key);

        return response()->stream(function () use ($mediaUrl, $range) {
            $curl = curl_init($mediaUrl);
            $options = [
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_HEADER => false,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_WRITEFUNCTION => static function ($handle, $chunk) {
                    echo $chunk;
                    flush();
                    return strlen($chunk);
                },
            ];
            if ($range) {
                $options[CURLOPT_HTTPHEADER] = ['Range: ' . $range];
            }
            curl_setopt_array($curl, $options);
            curl_exec($curl);
            curl_close($curl);
        }, $status, $headers);
    }
}
