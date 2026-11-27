<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plan_features')) {
            return;
        }

        if (!Schema::hasColumn('plan_features', 'show_in_workstation')) {
            Schema::table('plan_features', function (Blueprint $table) {
                $table->boolean('show_in_workstation')->default(false)->after('status');
            });
        }

        $defaults = [
            'film_editing_access' => ['Film Editing Access', 'reel2reel/', 'fa fa-scissors', 14],
            'live_broadcast' => ['CineMeet', 'user/live_broadcasts', 'fa fa-video-camera', 22],
            'film_uploads' => ['Film Uploads', 'user/films', 'fa fa-upload', 6],
            'cinegrab' => ['CineGrab', 'cinegrab/', 'fa fa-download', 23],
            'cinestock' => ['CineStock', 'https://stock.cineworm.org/', 'fa fa-th-large', 30],
            'my_profile' => ['My Profile', 'profile', 'fa fa-user', 31],
            'subscription_plans' => ['Subscription Plans', 'membership_plan', 'fa fa-credit-card', 32],
            'affiliate_program' => ['Affiliate Program', 'affiliate', 'fa fa-share-alt', 33],
            'add_live_tv' => ['Add Live TV', 'user/live-tv/create', 'fa fa-television', 34],
            'live_tv' => ['Live TV', 'livetv/', 'fa fa-play', 35],
            'games' => ['Games', 'game/remote-control', 'fa fa-gamepad', 36],
        ];

        foreach ($defaults as $key => [$name, $url, $icon, $sort]) {
            $feature = DB::table('plan_features')->where('feature_key', $key)->first();
            if ($feature) {
                DB::table('plan_features')->where('feature_key', $key)->update(['show_in_workstation' => true]);
                continue;
            }

            DB::table('plan_features')->insert([
                'feature_key' => $key,
                'feature_name' => $name,
                'url' => $url,
                'icon' => $icon,
                'sort_order' => $sort,
                'status' => 1,
                'show_in_workstation' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('plan_features', 'show_in_workstation')) {
            Schema::table('plan_features', function (Blueprint $table) {
                $table->dropColumn('show_in_workstation');
            });
        }
    }
};
