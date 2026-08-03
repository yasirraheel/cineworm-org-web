<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PlanFeature extends Model
{
    protected $table = 'plan_features';

    protected $fillable = [
        'feature_key',
        'feature_name',
        'url',
        'icon',
        'sort_order',
        'status',
    ];

    public static function getSystemUrlMap(): array
    {
        return [
            'film_editing_access'    => '/reel2reel/',
            'extended_media_uploads' => URL::to('user/films/upload'),
            'priority_promotion'     => URL::to('promotions'),
            'job_listing'            => URL::to('user/jobs'),
            'news_ticker'            => URL::to('user/news_tickers'),
            'live_broadcast'         => URL::to('user/live_broadcasts'),
        ];
    }

    public static function getActiveFeaturesMap(): array
    {
        $features = self::where('status', 1)->orderBy('sort_order')->orderBy('id')->get();

        if ($features->isEmpty()) {
            return SubscriptionPlan::AVAILABLE_FEATURES;
        }

        $map = [];
        foreach ($features as $feature) {
            $map[$feature->feature_key] = $feature->feature_name;
        }

        return $map;
    }

    public static function getActiveFeaturesConfig(): array
    {
        $features = self::where('status', 1)->orderBy('sort_order')->orderBy('id')->get();
        $systemMap = self::getSystemUrlMap();

        if ($features->isEmpty()) {
            return [
                'film_editing_access'    => ['title' => 'Film Editing Access', 'url' => '/reel2reel/', 'icon' => 'fa fa-cut'],
                'extended_media_uploads' => ['title' => 'Extended Media Uploads', 'url' => URL::to('user/films/upload'), 'icon' => 'fa fa-cloud-upload-alt'],
                'priority_promotion'     => ['title' => 'Priority Promotion', 'url' => URL::to('promotions'), 'icon' => 'fa fa-rocket'],
                'job_listing'            => ['title' => 'Job Listing', 'url' => URL::to('user/jobs'), 'icon' => 'fa fa-briefcase'],
                'news_ticker'            => ['title' => 'News Ticker', 'url' => URL::to('user/news_tickers'), 'icon' => 'fa fa-newspaper'],
                'live_broadcast'         => ['title' => 'Live Broadcast', 'url' => URL::to('user/live_broadcasts'), 'icon' => 'fa fa-podcast'],
            ];
        }

        $config = [];
        foreach ($features as $feature) {
            $key = $feature->feature_key;
            
            if (isset($systemMap[$key])) {
                $finalUrl = $systemMap[$key];
            } else {
                $rawUrl = trim($feature->url ?? '');
                if (!empty($rawUrl) && $rawUrl !== '#' && $rawUrl !== 'javascript:void(0);') {
                    $finalUrl = str_starts_with($rawUrl, 'http') ? $rawUrl : URL::to($rawUrl);
                } else {
                    $finalUrl = 'javascript:void(0);';
                }
            }

            $config[$key] = [
                'title' => $feature->feature_name,
                'url'   => $finalUrl,
                'icon'  => $feature->icon ?: 'fa fa-check-circle',
            ];
        }

        return $config;
    }
}
