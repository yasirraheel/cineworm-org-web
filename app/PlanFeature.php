<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

        if ($features->isEmpty()) {
            return [
                'watch_content' => ['title' => 'Watch Content', 'url' => URL('/'), 'icon' => 'fa fa-play-circle'],
                'donate_to_projects' => ['title' => 'Donate to Projects', 'url' => getcong('donation_link') ? stripslashes(getcong('donation_link')) : 'javascript:void(0);', 'icon' => 'fa fa-heart'],
                'play_games' => ['title' => 'Play Games', 'url' => URL('game/remote-control'), 'icon' => 'fa fa-gamepad'],
                'basic_user_account' => ['title' => 'Basic User Account', 'url' => URL('dashboard'), 'icon' => 'fa fa-user-circle'],
                'personal_profile_page' => ['title' => 'Personal Profile Page', 'url' => URL('profile'), 'icon' => 'fa fa-id-card'],
                'film_uploads' => ['title' => 'Film Uploads', 'url' => URL('user/films'), 'icon' => 'fa fa-upload'],
                'promotion_services' => ['title' => 'Promotion Services', 'url' => URL('promotions'), 'icon' => 'fa fa-bullhorn'],
                'deal_plus_access' => ['title' => 'Deal Plus Access', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-handshake'],
                'crowdfunding_link_sharing' => ['title' => 'Crowdfunding Link Sharing', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-link'],
                'website_link_sharing' => ['title' => 'Website Link Sharing', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-globe'],
                'photo_gallery' => ['title' => 'Photo Gallery', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-image'],
                'project_showcase_page' => ['title' => 'Project Showcase Page', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-film'],
                'film_project_space' => ['title' => 'Film Project Space', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-folder-open'],
                'film_editing_access' => ['title' => 'Film Editing Access', 'url' => URL('reel2reel/'), 'icon' => 'fa fa-cut'],
                'colour_grading_access' => ['title' => 'Colour Grading Access', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-adjust'],
                'advanced_film_showcase' => ['title' => 'Advanced Film Showcase', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-star'],
                'pro_creator_tools' => ['title' => 'Pro Creator Tools', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-wrench'],
                'extended_media_uploads' => ['title' => 'Extended Media Uploads', 'url' => URL('user/films/upload'), 'icon' => 'fa fa-cloud-upload-alt'],
                'priority_promotion' => ['title' => 'Priority Promotion', 'url' => URL('promotions'), 'icon' => 'fa fa-rocket'],
                'job_listing' => ['title' => 'Job Listing', 'url' => URL('user/jobs'), 'icon' => 'fa fa-briefcase'],
                'news_ticker' => ['title' => 'News Ticker', 'url' => URL('user/news_tickers'), 'icon' => 'fa fa-newspaper'],
                'live_broadcast' => ['title' => 'Live Broadcast', 'url' => URL('user/live_broadcasts'), 'icon' => 'fa fa-podcast'],
            ];
        }

        $config = [];
        foreach ($features as $feature) {
            $rawUrl = trim($feature->url ?? '');
            if (empty($rawUrl) || $rawUrl === '#' || $rawUrl === 'javascript:void(0);') {
                $finalUrl = 'javascript:void(0);';
            } elseif (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
                $finalUrl = $rawUrl;
            } else {
                $finalUrl = url(ltrim($rawUrl, '/'));
            }

            $config[$feature->feature_key] = [
                'title' => $feature->feature_name,
                'url' => $finalUrl,
                'raw_url' => $feature->url,
                'icon' => $feature->icon ?: 'fa fa-check-circle',
            ];
        }

        return $config;
    }
}
