<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\PwaSettings;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class PwaController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        check_verify_purchase();
    }

    public function index()
    {
        if (Auth::User()->usertype != "Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $page_title = trans('words.pwa_settings');
        $pwa_settings = PwaSettings::getSettings();

        return view('admin.pages.pwa_settings', compact('page_title', 'pwa_settings'));
    }

    public function update(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $pwa_settings = PwaSettings::getSettings();

        // Handle icon uploads
        $icon_path = 'upload/pwa_icons/';
        if (!File::exists($icon_path)) {
            File::makeDirectory($icon_path, 0777, true);
        }

        // Upload icon 192x192
        if ($request->hasFile('icon_192')) {
            $icon_192 = $request->file('icon_192');
            if ($icon_192->isValid()) {
                $icon_192_name = 'icon-192x192.' . $icon_192->getClientOriginalExtension();
                $icon_192_path = $icon_path . $icon_192_name;
                
                $img = Image::make($icon_192);
                $img->resize(192, 192);
                $img->save($icon_192_path);
                
                $pwa_settings->icon_192 = $icon_192_path;
            }
        }

        // Upload icon 512x512
        if ($request->hasFile('icon_512')) {
            $icon_512 = $request->file('icon_512');
            if ($icon_512->isValid()) {
                $icon_512_name = 'icon-512x512.' . $icon_512->getClientOriginalExtension();
                $icon_512_path = $icon_path . $icon_512_name;
                
                $img = Image::make($icon_512);
                $img->resize(512, 512);
                $img->save($icon_512_path);
                
                $pwa_settings->icon_512 = $icon_512_path;
            }
        }

        // Upload maskable icon 192x192
        if ($request->hasFile('maskable_icon_192')) {
            $maskable_192 = $request->file('maskable_icon_192');
            if ($maskable_192->isValid()) {
                $maskable_192_name = 'maskable-192x192.' . $maskable_192->getClientOriginalExtension();
                $maskable_192_path = $icon_path . $maskable_192_name;
                
                $img = Image::make($maskable_192);
                $img->resize(192, 192);
                $img->save($maskable_192_path);
                
                $pwa_settings->maskable_icon_192 = $maskable_192_path;
            }
        }

        // Upload maskable icon 512x512
        if ($request->hasFile('maskable_icon_512')) {
            $maskable_512 = $request->file('maskable_icon_512');
            if ($maskable_512->isValid()) {
                $maskable_512_name = 'maskable-512x512.' . $maskable_512->getClientOriginalExtension();
                $maskable_512_path = $icon_path . $maskable_512_name;
                
                $img = Image::make($maskable_512);
                $img->resize(512, 512);
                $img->save($maskable_512_path);
                
                $pwa_settings->maskable_icon_512 = $maskable_512_path;
            }
        }

        // Upload Apple Touch Icon
        if ($request->hasFile('apple_touch_icon')) {
            $apple_icon = $request->file('apple_touch_icon');
            if ($apple_icon->isValid()) {
                $apple_icon_name = 'apple-touch-icon.' . $apple_icon->getClientOriginalExtension();
                $apple_icon_path = $icon_path . $apple_icon_name;
                
                $img = Image::make($apple_icon);
                $img->resize(180, 180);
                $img->save($apple_icon_path);
                
                $pwa_settings->apple_touch_icon = $apple_icon_path;
            }
        }

        // Upload Notification Icon
        if ($request->hasFile('notification_icon')) {
            $notification_icon = $request->file('notification_icon');
            if ($notification_icon->isValid()) {
                $notification_icon_name = 'notification-icon.' . $notification_icon->getClientOriginalExtension();
                $notification_icon_path = $icon_path . $notification_icon_name;
                
                $img = Image::make($notification_icon);
                $img->resize(96, 96);
                $img->save($notification_icon_path);
                
                $pwa_settings->notification_icon = $notification_icon_path;
            }
        }

        // Upload Notification Badge
        if ($request->hasFile('notification_badge')) {
            $notification_badge = $request->file('notification_badge');
            if ($notification_badge->isValid()) {
                $notification_badge_name = 'notification-badge.' . $notification_badge->getClientOriginalExtension();
                $notification_badge_path = $icon_path . $notification_badge_name;
                
                $img = Image::make($notification_badge);
                $img->resize(96, 96);
                $img->save($notification_badge_path);
                
                $pwa_settings->notification_badge = $notification_badge_path;
            }
        }

        // Handle screenshots upload
        if ($request->hasFile('screenshots')) {
            $screenshots = [];
            foreach ($request->file('screenshots') as $screenshot) {
                if ($screenshot->isValid()) {
                    $screenshot_name = 'screenshot-' . time() . '-' . rand(1000, 9999) . '.' . $screenshot->getClientOriginalExtension();
                    $screenshot_path = $icon_path . $screenshot_name;
                    
                    $img = Image::make($screenshot);
                    $img->resize(540, 720);
                    $img->save($screenshot_path);
                    
                    $screenshots[] = $screenshot_path;
                }
            }
            if (count($screenshots) > 0) {
                $pwa_settings->screenshots = $screenshots;
            }
        }

        // Handle shortcuts
        if ($request->has('shortcuts_enabled') && $request->shortcuts_enabled == 1) {
            $shortcuts = [];
            if ($request->has('shortcut_names')) {
                foreach ($request->shortcut_names as $index => $name) {
                    if (!empty($name)) {
                        $shortcut = [
                            'name' => $name,
                            'short_name' => $request->shortcut_short_names[$index] ?? '',
                            'description' => $request->shortcut_descriptions[$index] ?? '',
                            'url' => $request->shortcut_urls[$index] ?? '/',
                        ];
                        
                        // Handle shortcut icon upload
                        if ($request->hasFile('shortcut_icons.' . $index)) {
                            $shortcut_icon = $request->file('shortcut_icons.' . $index);
                            if ($shortcut_icon->isValid()) {
                                $shortcut_icon_name = 'shortcut-' . $index . '-' . time() . '.' . $shortcut_icon->getClientOriginalExtension();
                                $shortcut_icon_path = $icon_path . $shortcut_icon_name;
                                
                                $img = Image::make($shortcut_icon);
                                $img->resize(96, 96);
                                $img->save($shortcut_icon_path);
                                
                                $shortcut['icon'] = $shortcut_icon_path;
                            }
                        }
                        
                        $shortcuts[] = $shortcut;
                    }
                }
            }
            $pwa_settings->custom_shortcuts = $shortcuts;
        }

        // Handle categories
        $categories = [];
        if ($request->has('categories')) {
            $categories = $request->categories;
        }
        $pwa_settings->categories = $categories;

        // Update general fields
        $pwa_settings->pwa_enabled = $request->pwa_enabled ? 1 : 0;
        $pwa_settings->app_name = $request->app_name;
        $pwa_settings->app_short_name = $request->app_short_name;
        $pwa_settings->app_description = $request->app_description;
        $pwa_settings->start_url = $request->start_url;
        $pwa_settings->scope = $request->scope;
        $pwa_settings->theme_color = $request->theme_color;
        $pwa_settings->background_color = $request->background_color;
        $pwa_settings->display_mode = $request->display_mode;
        $pwa_settings->orientation = $request->orientation;
        $pwa_settings->offline_page_enabled = $request->offline_page_enabled ? 1 : 0;
        $pwa_settings->offline_page_title = $request->offline_page_title;
        $pwa_settings->offline_page_message = $request->offline_page_message;
        $pwa_settings->cache_strategy = $request->cache_strategy;
        $pwa_settings->cache_version = $request->cache_version;
        $pwa_settings->push_notification_enabled = $request->push_notification_enabled ? 1 : 0;
        $pwa_settings->vapid_public_key = $request->vapid_public_key;
        $pwa_settings->vapid_private_key = $request->vapid_private_key;
        $pwa_settings->shortcuts_enabled = $request->shortcuts_enabled ? 1 : 0;
        $pwa_settings->prefer_related_apps = $request->prefer_related_apps ? 1 : 0;
        $pwa_settings->lang = $request->lang;
        $pwa_settings->dir = $request->dir;

        $pwa_settings->save();

        \Session::flash('flash_message', trans('words.success_text'));

        return redirect()->back();
    }

    public function generateIcons(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['error' => trans('words.access_denied')], 403);
        }

        if (!$request->hasFile('source_icon')) {
            return response()->json(['error' => 'No icon file provided'], 400);
        }

        $source_icon = $request->file('source_icon');
        if (!$source_icon->isValid()) {
            return response()->json(['error' => 'Invalid icon file'], 400);
        }

        $icon_path = 'upload/pwa_icons/';
        if (!File::exists($icon_path)) {
            File::makeDirectory($icon_path, 0777, true);
        }

        $pwa_settings = PwaSettings::getSettings();
        $icons_generated = [];

        try {
            // Generate 192x192
            $icon_192_path = $icon_path . 'icon-192x192.png';
            $img = Image::make($source_icon);
            $img->resize(192, 192);
            $img->save($icon_192_path);
            $pwa_settings->icon_192 = $icon_192_path;
            $icons_generated[] = '192x192';

            // Generate 512x512
            $icon_512_path = $icon_path . 'icon-512x512.png';
            $img = Image::make($source_icon);
            $img->resize(512, 512);
            $img->save($icon_512_path);
            $pwa_settings->icon_512 = $icon_512_path;
            $icons_generated[] = '512x512';

            // Generate maskable 192x192
            $maskable_192_path = $icon_path . 'maskable-192x192.png';
            $img = Image::make($source_icon);
            $img->resize(192, 192);
            $img->save($maskable_192_path);
            $pwa_settings->maskable_icon_192 = $maskable_192_path;
            $icons_generated[] = 'maskable 192x192';

            // Generate maskable 512x512
            $maskable_512_path = $icon_path . 'maskable-512x512.png';
            $img = Image::make($source_icon);
            $img->resize(512, 512);
            $img->save($maskable_512_path);
            $pwa_settings->maskable_icon_512 = $maskable_512_path;
            $icons_generated[] = 'maskable 512x512';

            // Generate Apple Touch Icon
            $apple_icon_path = $icon_path . 'apple-touch-icon.png';
            $img = Image::make($source_icon);
            $img->resize(180, 180);
            $img->save($apple_icon_path);
            $pwa_settings->apple_touch_icon = $apple_icon_path;
            $icons_generated[] = 'Apple Touch Icon';

            $pwa_settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Icons generated successfully',
                'icons' => $icons_generated
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function clearCache()
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['error' => trans('words.access_denied')], 403);
        }

        $pwa_settings = PwaSettings::getSettings();
        
        // Increment cache version
        $version_parts = explode('.', str_replace('v', '', $pwa_settings->cache_version));
        $version_parts[count($version_parts) - 1] = (int)$version_parts[count($version_parts) - 1] + 1;
        $new_version = 'v' . implode('.', $version_parts);
        
        $pwa_settings->cache_version = $new_version;
        $pwa_settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully',
            'new_version' => $new_version
        ]);
    }
}
