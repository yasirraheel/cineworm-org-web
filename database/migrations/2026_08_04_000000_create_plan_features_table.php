<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlanFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('plan_features')) {
            Schema::create('plan_features', function (Blueprint $table) {
                $table->id();
                $table->string('feature_key', 100)->unique();
                $table->string('feature_name', 255);
                $table->string('url', 255)->nullable();
                $table->string('icon', 100)->nullable()->default('fa fa-check-circle');
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

            // Seed default system features
            $defaultFeatures = [
                ['feature_key' => 'watch_content', 'feature_name' => 'Watch Content', 'url' => '/', 'icon' => 'fa fa-play-circle', 'sort_order' => 1],
                ['feature_key' => 'donate_to_projects', 'feature_name' => 'Donate to Projects', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-heart', 'sort_order' => 2],
                ['feature_key' => 'play_games', 'feature_name' => 'Play Games', 'url' => 'game/remote-control', 'icon' => 'fa fa-gamepad', 'sort_order' => 3],
                ['feature_key' => 'basic_user_account', 'feature_name' => 'Basic User Account', 'url' => 'dashboard', 'icon' => 'fa fa-user-circle', 'sort_order' => 4],
                ['feature_key' => 'personal_profile_page', 'feature_name' => 'Personal Profile Page', 'url' => 'profile', 'icon' => 'fa fa-id-card', 'sort_order' => 5],
                ['feature_key' => 'film_uploads', 'feature_name' => 'Film Uploads', 'url' => 'user/films', 'icon' => 'fa fa-upload', 'sort_order' => 6],
                ['feature_key' => 'promotion_services', 'feature_name' => 'Promotion Services', 'url' => 'promotions', 'icon' => 'fa fa-bullhorn', 'sort_order' => 7],
                ['feature_key' => 'deal_plus_access', 'feature_name' => 'Deal Plus Access', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-handshake', 'sort_order' => 8],
                ['feature_key' => 'crowdfunding_link_sharing', 'feature_name' => 'Crowdfunding Link Sharing', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-link', 'sort_order' => 9],
                ['feature_key' => 'website_link_sharing', 'feature_name' => 'Website Link Sharing', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-globe', 'sort_order' => 10],
                ['feature_key' => 'photo_gallery', 'feature_name' => 'Photo Gallery', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-image', 'sort_order' => 11],
                ['feature_key' => 'project_showcase_page', 'feature_name' => 'Project Showcase Page', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-film', 'sort_order' => 12],
                ['feature_key' => 'film_project_space', 'feature_name' => 'Film Project Space', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-folder-open', 'sort_order' => 13],
                ['feature_key' => 'film_editing_access', 'feature_name' => 'Film Editing Access', 'url' => 'reel2reel/', 'icon' => 'fa fa-cut', 'sort_order' => 14],
                ['feature_key' => 'colour_grading_access', 'feature_name' => 'Colour Grading Access', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-adjust', 'sort_order' => 15],
                ['feature_key' => 'advanced_film_showcase', 'feature_name' => 'Advanced Film Showcase', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-star', 'sort_order' => 16],
                ['feature_key' => 'pro_creator_tools', 'feature_name' => 'Pro Creator Tools', 'url' => 'javascript:void(0);', 'icon' => 'fa fa-wrench', 'sort_order' => 17],
                ['feature_key' => 'extended_media_uploads', 'feature_name' => 'Extended Media Uploads', 'url' => 'user/films/upload', 'icon' => 'fa fa-cloud-upload-alt', 'sort_order' => 18],
                ['feature_key' => 'priority_promotion', 'feature_name' => 'Priority Promotion', 'url' => 'promotions', 'icon' => 'fa fa-rocket', 'sort_order' => 19],
                ['feature_key' => 'job_listing', 'feature_name' => 'Job Listing', 'url' => 'user/jobs', 'icon' => 'fa fa-briefcase', 'sort_order' => 20],
                ['feature_key' => 'news_ticker', 'feature_name' => 'News Ticker', 'url' => 'user/news_tickers', 'icon' => 'fa fa-newspaper', 'sort_order' => 21],
                ['feature_key' => 'live_broadcast', 'feature_name' => 'Live Broadcast', 'url' => 'user/live_broadcasts', 'icon' => 'fa fa-podcast', 'sort_order' => 22],
            ];

            $now = date('Y-m-d H:i:s');
            foreach ($defaultFeatures as &$item) {
                $item['status'] = 1;
                $item['created_at'] = $now;
                $item['updated_at'] = $now;
            }

            DB::table('plan_features')->insert($defaultFeatures);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_features');
    }
}
