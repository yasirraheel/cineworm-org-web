<?php

namespace App\Http\Controllers;

use App\PwaSettings;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function generate()
    {
        $pwa_settings = PwaSettings::getSettings();

        if (!$pwa_settings->pwa_enabled) {
            return response()->json([
                'name' => config('app.name'),
                'short_name' => config('app.name'),
            ], 200, ['Content-Type' => 'application/json']);
        }

        $manifest = $pwa_settings->getManifestArray();

        return response()->json($manifest, 200, ['Content-Type' => 'application/json']);
    }

    public function serviceWorker()
    {
        $pwa_settings = PwaSettings::getSettings();

        $sw_content = view('sw', compact('pwa_settings'))->render();

        return response($sw_content, 200, [
            'Content-Type' => 'application/javascript',
            'Service-Worker-Allowed' => '/'
        ]);
    }
}
