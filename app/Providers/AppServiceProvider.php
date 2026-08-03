<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */


    public function register(): void
    {
        $fwdevPlayerFile = "FWDEVPlayer2.js";
        $fwdevPlayerPath = public_path('site_assets/player/java/' . $fwdevPlayerFile);
        $fwdevPlayerVersion = file_exists($fwdevPlayerPath) ? filemtime($fwdevPlayerPath) : null;

        $FWDEVPlayer = $fwdevPlayerVersion
            ? $fwdevPlayerFile . '?v=' . $fwdevPlayerVersion
            : $fwdevPlayerFile;

        // Share the variable with all views
        View::share('FWDEVPlayer', $FWDEVPlayer);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || str_contains(request()->getHost(), 'cineworm.org')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            \Illuminate\Support\Facades\URL::forceRootUrl('https://cineworm.org');
        }
    }
}
