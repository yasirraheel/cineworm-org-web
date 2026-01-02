<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PwaSettings extends Model
{
    protected $table = 'pwa_settings';

    protected $fillable = [
        'pwa_enabled',
        'app_name',
        'app_short_name',
        'app_description',
        'start_url',
        'scope',
        'theme_color',
        'background_color',
        'display_mode',
        'orientation',
        'icon_192',
        'icon_512',
        'maskable_icon_192',
        'maskable_icon_512',
        'apple_touch_icon',
        'screenshots',
        'offline_page_enabled',
        'offline_page_title',
        'offline_page_message',
        'cache_strategy',
        'cache_version',
        'push_notification_enabled',
        'notification_icon',
        'notification_badge',
        'vapid_public_key',
        'vapid_private_key',
        'shortcuts_enabled',
        'custom_shortcuts',
        'categories',
        'related_applications',
        'prefer_related_apps',
        'lang',
        'dir',
    ];

    protected $casts = [
        'pwa_enabled' => 'boolean',
        'offline_page_enabled' => 'boolean',
        'push_notification_enabled' => 'boolean',
        'shortcuts_enabled' => 'boolean',
        'prefer_related_apps' => 'boolean',
        'screenshots' => 'array',
        'custom_shortcuts' => 'array',
        'categories' => 'array',
        'related_applications' => 'array',
    ];

    /**
     * Get the PWA settings instance
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'app_name' => 'CineWorm',
            'app_short_name' => 'CineWorm',
        ]);
    }

    /**
     * Get manifest array
     */
    public function getManifestArray()
    {
        $manifest = [
            'name' => $this->app_name,
            'short_name' => $this->app_short_name,
            'description' => $this->app_description,
            'start_url' => $this->start_url,
            'scope' => $this->scope,
            'display' => $this->display_mode,
            'orientation' => $this->orientation,
            'theme_color' => $this->theme_color,
            'background_color' => $this->background_color,
            'lang' => $this->lang,
            'dir' => $this->dir,
            'icons' => [],
        ];

        // Add icons
        if ($this->icon_192) {
            $manifest['icons'][] = [
                'src' => asset($this->icon_192),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any',
            ];
        }

        if ($this->icon_512) {
            $manifest['icons'][] = [
                'src' => asset($this->icon_512),
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any',
            ];
        }

        if ($this->maskable_icon_192) {
            $manifest['icons'][] = [
                'src' => asset($this->maskable_icon_192),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ];
        }

        if ($this->maskable_icon_512) {
            $manifest['icons'][] = [
                'src' => asset($this->maskable_icon_512),
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ];
        }

        // Add screenshots
        if ($this->screenshots && count($this->screenshots) > 0) {
            $manifest['screenshots'] = array_map(function($screenshot) {
                return [
                    'src' => asset($screenshot),
                    'type' => 'image/png',
                    'sizes' => '540x720',
                ];
            }, $this->screenshots);
        }

        // Add shortcuts
        if ($this->shortcuts_enabled && $this->custom_shortcuts && count($this->custom_shortcuts) > 0) {
            $manifest['shortcuts'] = array_map(function($shortcut) {
                return [
                    'name' => $shortcut['name'] ?? '',
                    'short_name' => $shortcut['short_name'] ?? '',
                    'description' => $shortcut['description'] ?? '',
                    'url' => $shortcut['url'] ?? '/',
                    'icons' => isset($shortcut['icon']) ? [[
                        'src' => asset($shortcut['icon']),
                        'sizes' => '96x96',
                        'type' => 'image/png',
                    ]] : [],
                ];
            }, $this->custom_shortcuts);
        }

        // Add categories
        if ($this->categories && count($this->categories) > 0) {
            $manifest['categories'] = $this->categories;
        }

        // Add related applications
        if ($this->related_applications && count($this->related_applications) > 0) {
            $manifest['related_applications'] = $this->related_applications;
            $manifest['prefer_related_applications'] = $this->prefer_related_apps;
        }

        return $manifest;
    }
}
