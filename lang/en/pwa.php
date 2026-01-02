<?php

return [
    // PWA Settings
    'pwa_settings' => 'PWA Settings',
    'pwa_enable' => 'Enable PWA',
    'pwa_disabled' => 'Disabled',
    'pwa_enabled' => 'Enabled',
    
    // Tabs
    'general_tab' => 'General',
    'appearance_tab' => 'Appearance',
    'icons_images_tab' => 'Icons & Images',
    'offline_tab' => 'Offline Experience',
    'notifications_tab' => 'Push Notifications',
    'shortcuts_tab' => 'Shortcuts',
    'advanced_tab' => 'Advanced',
    
    // General Settings
    'app_name' => 'App Name',
    'app_short_name' => 'Short Name',
    'app_description' => 'App Description',
    'start_url' => 'Start URL',
    'scope' => 'Scope',
    'app_name_help' => 'Full name of your application',
    'app_short_name_help' => 'Short name (12 characters or less)',
    'app_description_help' => 'Brief description of your app',
    'start_url_help' => 'URL that loads when app is launched',
    'scope_help' => 'Navigation scope for the app',
    
    // Appearance
    'theme_color' => 'Theme Color',
    'background_color' => 'Background Color',
    'display_mode' => 'Display Mode',
    'orientation' => 'Orientation',
    'theme_color_help' => 'Color of the browser UI',
    'background_color_help' => 'Background color when app launches',
    'display_mode_help' => 'How the app should be displayed',
    'orientation_help' => 'Default screen orientation',
    
    // Display Modes
    'standalone' => 'Standalone',
    'fullscreen' => 'Fullscreen',
    'minimal_ui' => 'Minimal UI',
    'browser' => 'Browser',
    
    // Orientations
    'any' => 'Any',
    'portrait' => 'Portrait',
    'landscape' => 'Landscape',
    'portrait_primary' => 'Portrait Primary',
    'portrait_secondary' => 'Portrait Secondary',
    'landscape_primary' => 'Landscape Primary',
    'landscape_secondary' => 'Landscape Secondary',
    
    // Icons & Images
    'icon_192' => 'Icon 192x192',
    'icon_512' => 'Icon 512x512',
    'maskable_icon_192' => 'Maskable Icon 192x192',
    'maskable_icon_512' => 'Maskable Icon 512x512',
    'apple_touch_icon' => 'Apple Touch Icon',
    'screenshots' => 'Screenshots',
    'auto_generate' => 'Auto Generate Icons',
    'upload_source' => 'Upload Source Image',
    'current_icon' => 'Current Icon',
    'no_icon' => 'No icon uploaded',
    'icon_192_help' => 'Standard icon 192x192 pixels',
    'icon_512_help' => 'Standard icon 512x512 pixels',
    'maskable_icon_help' => 'Maskable icons adapt to different device shapes',
    'apple_touch_icon_help' => 'Icon for iOS home screen (180x180)',
    'screenshots_help' => 'App screenshots (540x720 recommended)',
    'auto_generate_help' => 'Upload one image to generate all icon sizes',
    
    // Offline
    'offline_page_enable' => 'Enable Offline Page',
    'offline_page_title' => 'Offline Page Title',
    'offline_page_message' => 'Offline Page Message',
    'cache_strategy' => 'Cache Strategy',
    'cache_version' => 'Cache Version',
    'clear_cache' => 'Clear Cache',
    'offline_page_title_help' => 'Title shown when user is offline',
    'offline_page_message_help' => 'Message displayed on offline page',
    'cache_strategy_help' => 'How content should be cached',
    'cache_version_help' => 'Increment to force cache refresh',
    
    // Cache Strategies
    'cache_first' => 'Cache First',
    'network_first' => 'Network First',
    'stale_while_revalidate' => 'Stale While Revalidate',
    
    // Push Notifications
    'push_enable' => 'Enable Push Notifications',
    'notification_icon' => 'Notification Icon',
    'notification_badge' => 'Notification Badge',
    'vapid_public_key' => 'VAPID Public Key',
    'vapid_private_key' => 'VAPID Private Key',
    'generate_vapid_keys' => 'Generate VAPID Keys',
    'notification_icon_help' => 'Icon shown in notifications (96x96)',
    'notification_badge_help' => 'Badge icon for notifications (96x96)',
    'vapid_keys_help' => 'Required for web push notifications',
    
    // Shortcuts
    'shortcuts_enable' => 'Enable Shortcuts',
    'add_shortcut' => 'Add Shortcut',
    'remove_shortcut' => 'Remove',
    'shortcut_name' => 'Name',
    'shortcut_short_name' => 'Short Name',
    'shortcut_description' => 'Description',
    'shortcut_url' => 'URL',
    'shortcut_icon' => 'Icon',
    'shortcuts_help' => 'Quick access shortcuts in app launcher',
    
    // Advanced
    'categories' => 'Categories',
    'related_apps' => 'Related Applications',
    'prefer_related_apps' => 'Prefer Related Apps',
    'language' => 'Language',
    'text_direction' => 'Text Direction',
    'ltr' => 'Left to Right',
    'rtl' => 'Right to Left',
    'categories_help' => 'PWA categories for app stores',
    'related_apps_help' => 'Native apps related to this PWA (JSON format)',
    'prefer_related_apps_help' => 'Suggest native app instead of PWA',
    
    // Categories Options
    'books' => 'Books',
    'business' => 'Business',
    'education' => 'Education',
    'entertainment' => 'Entertainment',
    'finance' => 'Finance',
    'fitness' => 'Fitness',
    'food' => 'Food',
    'games' => 'Games',
    'government' => 'Government',
    'health' => 'Health',
    'kids' => 'Kids',
    'lifestyle' => 'Lifestyle',
    'magazines' => 'Magazines',
    'medical' => 'Medical',
    'music' => 'Music',
    'navigation' => 'Navigation',
    'news' => 'News',
    'personalization' => 'Personalization',
    'photo' => 'Photo',
    'politics' => 'Politics',
    'productivity' => 'Productivity',
    'security' => 'Security',
    'shopping' => 'Shopping',
    'social' => 'Social',
    'sports' => 'Sports',
    'travel' => 'Travel',
    'utilities' => 'Utilities',
    'weather' => 'Weather',
    
    // Actions
    'save_settings' => 'Save Settings',
    'test_install' => 'Test PWA Install',
    'preview' => 'Preview',
    'upload' => 'Upload',
    'browse' => 'Browse',
    'generate' => 'Generate',
    
    // Messages
    'settings_saved' => 'PWA settings saved successfully',
    'icons_generated' => 'Icons generated successfully',
    'cache_cleared' => 'Cache cleared successfully',
    'error_occurred' => 'An error occurred',
    'file_size_error' => 'File size too large',
    'file_type_error' => 'Invalid file type',
    'fill_required_fields' => 'Please fill all required fields',
    
    // Info
    'pwa_info' => 'Progressive Web App (PWA) allows users to install your website as an app on their devices.',
    'manifest_url' => 'Manifest URL',
    'service_worker_url' => 'Service Worker URL',
    'pwa_status' => 'PWA Status',
    'installable' => 'Installable',
    'not_installable' => 'Not Installable',
    
];
