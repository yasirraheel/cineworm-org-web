<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CinemeetController extends MainAdminController
{
    protected $apiUrl;
    protected $adminToken;

    public function __construct()
    {
        $this->middleware('auth');
        $this->apiUrl     = rtrim(env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org'), '/');
        $this->adminToken = env('CINEMEET_ADMIN_TOKEN', '');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function apiGet(string $endpoint)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['x-admin-token' => $this->adminToken])
                ->get($this->apiUrl . '/admin-api/' . $endpoint);
            return $response->json();
        } catch (\Exception $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    private function apiPost(string $endpoint, array $data = [])
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['x-admin-token' => $this->adminToken])
                ->post($this->apiUrl . '/admin-api/' . $endpoint, $data);
            return $response->json();
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getSettings(): array
    {
        $result = $this->apiGet('settings');
        return $result['settings'] ?? [];
    }

    // ─── Pages ───────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $page_title = 'CineMeet — Dashboard';
        $status     = $this->apiGet('status');
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.dashboard', compact('page_title', 'status', 'settings'));
    }

    public function branding()
    {
        $page_title = 'CineMeet — Branding';
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.branding', compact('page_title', 'settings'));
    }

    public function updateBranding(Request $request)
    {
        $keys = [
            'APP_NAME', 'APP_TITLE', 'APP_DESCRIPTION',
            'APP_ICON', 'APP_APPLE_TOUCH_ICON',
        ];
        $updates = array_intersect_key($request->all(), array_flip($keys));
        $result  = $this->apiPost('settings', ['settings' => $updates, 'restart' => false]);

        if (!empty($result['success'])) {
            session()->flash('flash_message', 'Branding updated dynamically!');
        } else {
            session()->flash('flash_error', 'Error: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->back();
    }

    public function homepage()
    {
        $page_title = 'CineMeet — Homepage Content';
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.homepage', compact('page_title', 'settings'));
    }

    public function updateHomepage(Request $request)
    {
        $keys = [
            'APP_HERO_TITLE', 'APP_HERO_DESCRIPTION', 'APP_JOIN_DESCRIPTION',
            'JOIN_BUTTON_LABEL', 'CUSTOMIZE_BUTTON_LABEL',
            'APP_FEATURES_HEADING', 'APP_FEATURES_DESCRIPTION',
            'APP_NEW_ROOM_TITLE', 'APP_NEW_ROOM_DESCRIPTION',
        ];
        $updates = array_intersect_key($request->all(), array_flip($keys));
        $result  = $this->apiPost('settings', ['settings' => $updates, 'restart' => false]);

        if (!empty($result['success'])) {
            session()->flash('flash_message', 'Homepage content updated dynamically!');
        } else {
            session()->flash('flash_error', 'Error: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->back();
    }

    public function social()
    {
        $page_title = 'CineMeet — Social & Links';
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.social', compact('page_title', 'settings'));
    }

    public function updateSocial(Request $request)
    {
        $keys = [
            'SOCIAL_DISCORD_URL', 'SOCIAL_GITHUB_URL',
            'SOCIAL_TWITTER_URL', 'SOCIAL_FACEBOOK_URL',
            'SOCIAL_LINKEDIN_URL', 'SOCIAL_YOUTUBE_URL',
            'CONTACT_EMAIL', 'CONTACT_WEBSITE_URL',
        ];
        $updates = array_intersect_key($request->all(), array_flip($keys));
        $result  = $this->apiPost('settings', ['settings' => $updates, 'restart' => false]);

        if (!empty($result['success'])) {
            session()->flash('flash_message', 'Social links updated dynamically!');
        } else {
            session()->flash('flash_error', 'Error: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->back();
    }

    public function visibility()
    {
        $page_title = 'CineMeet — Section Visibility';
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.visibility', compact('page_title', 'settings'));
    }

    public function updateVisibility(Request $request)
    {
        $toggleKeys = [
            'SHOW_TOP_SPONSORS', 'SHOW_SPONSORS', 'SHOW_PAST_SPONSORS',
            'SHOW_ADVERTISERS', 'SHOW_SUPPORT_US', 'SHOW_FOOTER',
            'SHOW_FEATURES', 'SHOW_TEAMS', 'SHOW_TRY_EASIER',
            'SHOW_POWERED_BY', 'SHOW_ACTIVE_ROOMS',
        ];

        $updates = [];
        foreach ($toggleKeys as $key) {
            $updates[$key] = $request->has($key) ? 'true' : 'false';
        }

        $result = $this->apiPost('settings', ['settings' => $updates, 'restart' => false]);

        if (!empty($result['success'])) {
            session()->flash('flash_message', 'Visibility settings updated dynamically!');
        } else {
            session()->flash('flash_error', 'Error: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->back();
    }

    public function seo()
    {
        $page_title = 'CineMeet — SEO & Meta';
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.seo', compact('page_title', 'settings'));
    }

    public function updateSeo(Request $request)
    {
        $keys = [
            'SEO_TITLE', 'SEO_DESCRIPTION', 'SEO_KEYWORDS',
            'OG_TYPE', 'OG_SITE_NAME', 'OG_TITLE',
            'OG_DESCRIPTION', 'OG_IMAGE', 'OG_URL',
        ];
        $updates = array_intersect_key($request->all(), array_flip($keys));
        $result  = $this->apiPost('settings', ['settings' => $updates, 'restart' => false]);

        if (!empty($result['success'])) {
            session()->flash('flash_message', 'SEO settings updated dynamically!');
        } else {
            session()->flash('flash_error', 'Error: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->back();
    }

    public function serverSettings()
    {
        $page_title = 'CineMeet — Server Settings';
        $settings   = $this->getSettings();
        return view('admin.pages.cinemeet.server_settings', compact('page_title', 'settings'));
    }

    public function updateServerSettings(Request $request)
    {
        $keys = [
            'DOMAIN', 'SERVER_LISTEN_IP', 'SERVER_LISTEN_PORT',
            'ANNOUNCED_IP', 'CORS_ORIGIN', 'TRUST_PROXY',
        ];
        $updates = array_intersect_key($request->all(), array_flip($keys));
        $result  = $this->apiPost('settings', ['settings' => $updates, 'restart' => true]);

        if (!empty($result['success'])) {
            session()->flash('flash_message', 'Server settings updated and restart triggered!');
        } else {
            session()->flash('flash_error', 'Error: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->back();
    }

    public function apiDocs()
    {
        $page_title = 'CineMeet — API Documentation';
        $apiUrl     = $this->apiUrl;
        return view('admin.pages.cinemeet.api_docs', compact('page_title', 'apiUrl'));
    }

    // ─── AJAX Actions ─────────────────────────────────────────────────────────

    public function getStatus()
    {
        $status = $this->apiGet('status');
        return response()->json($status);
    }

    public function restart()
    {
        $result = $this->apiPost('restart');
        if (!empty($result['success'])) {
            session()->flash('flash_message', 'CineMeet restart signal sent successfully!');
        } else {
            session()->flash('flash_error', 'Restart failed: ' . ($result['error'] ?? 'Unknown error'));
        }
        return redirect()->route('admin.cinemeet.dashboard');
    }
}
