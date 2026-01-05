<?php

return [
    // PWA Settings
    'pwa_settings' => 'Paramètres PWA',
    'pwa_enable' => 'Activer PWA',
    'pwa_disabled' => 'Désactivé',
    'pwa_enabled' => 'Activé',

    // Tabs
    'general_tab' => 'Général',
    'appearance_tab' => 'Apparence',
    'icons_images_tab' => 'Icônes et Images',
    'offline_tab' => 'Expérience Hors Ligne',
    'notifications_tab' => 'Notifications Push',
    'shortcuts_tab' => 'Raccourcis',
    'advanced_tab' => 'Avancé',

    // General Settings
    'app_name' => 'Nom de l\'Application',
    'app_short_name' => 'Nom Court',
    'app_description' => 'Description de l\'Application',
    'start_url' => 'URL de Démarrage',
    'scope' => 'Portée',
    'app_name_help' => 'Nom complet de votre application',
    'app_short_name_help' => 'Nom court (12 caractères ou moins)',
    'app_description_help' => 'Brève description de votre application',
    'start_url_help' => 'URL qui se charge au lancement de l\'application',
    'scope_help' => 'Portée de navigation pour l\'application',

    // Appearance
    'theme_color' => 'Couleur du Thème',
    'background_color' => 'Couleur de Fond',
    'display_mode' => 'Mode d\'Affichage',
    'orientation' => 'Orientation',
    'theme_color_help' => 'Couleur de l\'interface du navigateur',
    'background_color_help' => 'Couleur de fond au lancement de l\'application',
    'display_mode_help' => 'Comment l\'application doit être affichée',
    'orientation_help' => 'Orientation d\'écran par défaut',

    // Display Modes
    'standalone' => 'Autonome',
    'fullscreen' => 'Plein Écran',
    'minimal_ui' => 'UI Minimale',
    'browser' => 'Navigateur',

    // Orientations
    'any' => 'Toutes',
    'portrait' => 'Portrait',
    'landscape' => 'Paysage',
    'portrait_primary' => 'Portrait Principal',
    'portrait_secondary' => 'Portrait Secondaire',
    'landscape_primary' => 'Paysage Principal',
    'landscape_secondary' => 'Paysage Secondaire',

    // Icons & Images
    'icon_192' => 'Icône 192x192',
    'icon_512' => 'Icône 512x512',
    'maskable_icon_192' => 'Icône Masquable 192x192',
    'maskable_icon_512' => 'Icône Masquable 512x512',
    'apple_touch_icon' => 'Icône Apple Touch',
    'screenshots' => 'Captures d\'Écran',
    'auto_generate' => 'Générer Automatiquement les Icônes',
    'upload_source' => 'Télécharger l\'Image Source',
    'current_icon' => 'Icône Actuelle',
    'no_icon' => 'Aucune icône téléchargée',
    'icon_192_help' => 'Icône standard 192x192 pixels',
    'icon_512_help' => 'Icône standard 512x512 pixels',
    'maskable_icon_help' => 'Les icônes masquables s\'adaptent aux différentes formes d\'appareils',
    'apple_touch_icon_help' => 'Icône pour l\'écran d\'accueil iOS (180x180)',
    'screenshots_help' => 'Captures d\'écran de l\'application (540x720 recommandé)',
    'auto_generate_help' => 'Téléchargez une image pour générer toutes les tailles d\'icônes',

    // Offline
    'offline_page_enable' => 'Activer la Page Hors Ligne',
    'offline_page_title' => 'Titre de la Page Hors Ligne',
    'offline_page_message' => 'Message de la Page Hors Ligne',
    'cache_strategy' => 'Stratégie de Cache',
    'cache_version' => 'Version du Cache',
    'clear_cache' => 'Vider le Cache',
    'offline_page_title_help' => 'Titre affiché lorsque l\'utilisateur est hors ligne',
    'offline_page_message_help' => 'Message affiché sur la page hors ligne',
    'cache_strategy_help' => 'Comment le contenu doit être mis en cache',
    'cache_version_help' => 'Incrémenter pour forcer l\'actualisation du cache',

    // Cache Strategies
    'cache_first' => 'Cache d\'Abord',
    'network_first' => 'Réseau d\'Abord',
    'stale_while_revalidate' => 'Périmé Pendant Revalidation',

    // Push Notifications
    'push_enable' => 'Activer les Notifications Push',
    'notification_icon' => 'Icône de Notification',
    'notification_badge' => 'Badge de Notification',
    'vapid_public_key' => 'Clé Publique VAPID',
    'vapid_private_key' => 'Clé Privée VAPID',
    'generate_vapid_keys' => 'Générer les Clés VAPID',
    'notification_icon_help' => 'Icône affichée dans les notifications (96x96)',
    'notification_badge_help' => 'Icône de badge pour les notifications (96x96)',
    'vapid_keys_help' => 'Requis pour les notifications push web',

    // Shortcuts
    'shortcuts_enable' => 'Activer les Raccourcis',
    'add_shortcut' => 'Ajouter un Raccourci',
    'remove_shortcut' => 'Supprimer',
    'shortcut_name' => 'Nom',
    'shortcut_short_name' => 'Nom Court',
    'shortcut_description' => 'Description',
    'shortcut_url' => 'URL',
    'shortcut_icon' => 'Icône',
    'shortcuts_help' => 'Raccourcis d\'accès rapide dans le lanceur d\'applications',

    // Advanced
    'categories' => 'Catégories',
    'related_apps' => 'Applications Associées',
    'prefer_related_apps' => 'Préférer les Applications Associées',
    'language' => 'Langue',
    'text_direction' => 'Direction du Texte',
    'ltr' => 'Gauche à Droite',
    'rtl' => 'Droite à Gauche',
    'categories_help' => 'Catégories PWA pour les magasins d\'applications',
    'related_apps_help' => 'Applications natives liées à cette PWA (format JSON)',
    'prefer_related_apps_help' => 'Suggérer l\'application native au lieu de la PWA',

    // Categories Options
    'books' => 'Livres',
    'business' => 'Affaires',
    'education' => 'Éducation',
    'entertainment' => 'Divertissement',
    'finance' => 'Finance',
    'fitness' => 'Fitness',
    'food' => 'Nourriture',
    'games' => 'Jeux',
    'government' => 'Gouvernement',
    'health' => 'Santé',
    'kids' => 'Enfants',
    'lifestyle' => 'Style de Vie',
    'magazines' => 'Magazines',
    'medical' => 'Médical',
    'music' => 'Musique',
    'navigation' => 'Navigation',
    'news' => 'Actualités',
    'personalization' => 'Personnalisation',
    'photo' => 'Photo',
    'politics' => 'Politique',
    'productivity' => 'Productivité',
    'security' => 'Sécurité',
    'shopping' => 'Shopping',
    'social' => 'Social',
    'sports' => 'Sports',
    'travel' => 'Voyage',
    'utilities' => 'Utilitaires',
    'weather' => 'Météo',

    // Actions
    'save_settings' => 'Enregistrer les Paramètres',
    'test_install' => 'Tester l\'Installation PWA',
    'preview' => 'Aperçu',
    'upload' => 'Télécharger',
    'browse' => 'Parcourir',
    'generate' => 'Générer',

    // Messages
    'settings_saved' => 'Paramètres PWA enregistrés avec succès',
    'icons_generated' => 'Icônes générées avec succès',
    'cache_cleared' => 'Cache vidé avec succès',
    'error_occurred' => 'Une erreur s\'est produite',
    'file_size_error' => 'Taille de fichier trop grande',
    'file_type_error' => 'Type de fichier invalide',
    'fill_required_fields' => 'Veuillez remplir tous les champs requis',

    // Info
    'pwa_info' => 'Progressive Web App (PWA) permet aux utilisateurs d\'installer votre site Web en tant qu\'application sur leurs appareils.',
    'manifest_url' => 'URL du Manifeste',
    'service_worker_url' => 'URL du Service Worker',
    'pwa_status' => 'Statut PWA',
    'installable' => 'Installable',
    'not_installable' => 'Non Installable',

];
