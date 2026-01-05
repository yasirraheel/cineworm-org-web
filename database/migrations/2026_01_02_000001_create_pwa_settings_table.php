<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePwaSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pwa_settings', function (Blueprint $table) {
            $table->id();

            // General Settings
            $table->boolean('pwa_enabled')->default(false);
            $table->string('app_name')->default('CineWorm');
            $table->string('app_short_name')->default('CineWorm');
            $table->text('app_description')->nullable();
            $table->string('start_url')->default('/');
            $table->string('scope')->default('/');

            // Appearance Settings
            $table->string('theme_color')->default('#000000');
            $table->string('background_color')->default('#ffffff');
            $table->enum('display_mode', ['standalone', 'fullscreen', 'minimal-ui', 'browser'])->default('standalone');
            $table->enum('orientation', ['any', 'portrait', 'landscape', 'portrait-primary', 'portrait-secondary', 'landscape-primary', 'landscape-secondary'])->default('any');

            // Icons & Images
            $table->string('icon_192')->nullable();
            $table->string('icon_512')->nullable();
            $table->string('maskable_icon_192')->nullable();
            $table->string('maskable_icon_512')->nullable();
            $table->string('apple_touch_icon')->nullable();
            $table->json('screenshots')->nullable();

            // Offline Settings
            $table->boolean('offline_page_enabled')->default(true);
            $table->string('offline_page_title')->default('Offline');
            $table->text('offline_page_message')->nullable();
            $table->enum('cache_strategy', ['cache-first', 'network-first', 'stale-while-revalidate'])->default('network-first');
            $table->string('cache_version')->default('v1.0.0');

            // Push Notifications
            $table->boolean('push_notification_enabled')->default(false);
            $table->string('notification_icon')->nullable();
            $table->string('notification_badge')->nullable();
            $table->text('vapid_public_key')->nullable();
            $table->text('vapid_private_key')->nullable();

            // Shortcuts
            $table->boolean('shortcuts_enabled')->default(false);
            $table->json('custom_shortcuts')->nullable();

            // Advanced Settings
            $table->json('categories')->nullable();
            $table->json('related_applications')->nullable();
            $table->boolean('prefer_related_apps')->default(false);
            $table->string('lang')->default('en');
            $table->string('dir')->default('ltr');

            $table->timestamps();
        });

        // Insert default settings
        DB::table('pwa_settings')->insert([
            'pwa_enabled' => false,
            'app_name' => 'CineWorm',
            'app_short_name' => 'CineWorm',
            'app_description' => 'Watch movies, series, and live TV',
            'theme_color' => '#000000',
            'background_color' => '#ffffff',
            'display_mode' => 'standalone',
            'orientation' => 'any',
            'start_url' => '/',
            'scope' => '/',
            'offline_page_enabled' => true,
            'offline_page_title' => 'You are offline',
            'offline_page_message' => 'Please check your internet connection and try again.',
            'cache_strategy' => 'network-first',
            'cache_version' => 'v1.0.0',
            'push_notification_enabled' => false,
            'shortcuts_enabled' => false,
            'prefer_related_apps' => false,
            'lang' => 'en',
            'dir' => 'ltr',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pwa_settings');
    }
}
