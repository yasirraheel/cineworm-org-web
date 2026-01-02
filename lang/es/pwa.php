<?php

return [
    // PWA Settings
    'pwa_settings' => 'Configuración PWA',
    'pwa_enable' => 'Activar PWA',
    'pwa_disabled' => 'Desactivado',
    'pwa_enabled' => 'Activado',
    
    // Tabs
    'general_tab' => 'General',
    'appearance_tab' => 'Apariencia',
    'icons_images_tab' => 'Iconos e Imágenes',
    'offline_tab' => 'Experiencia Sin Conexión',
    'notifications_tab' => 'Notificaciones Push',
    'shortcuts_tab' => 'Accesos Directos',
    'advanced_tab' => 'Avanzado',
    
    // General Settings
    'app_name' => 'Nombre de la Aplicación',
    'app_short_name' => 'Nombre Corto',
    'app_description' => 'Descripción de la Aplicación',
    'start_url' => 'URL de Inicio',
    'scope' => 'Alcance',
    'app_name_help' => 'Nombre completo de su aplicación',
    'app_short_name_help' => 'Nombre corto (12 caracteres o menos)',
    'app_description_help' => 'Breve descripción de su aplicación',
    'start_url_help' => 'URL que se carga cuando se inicia la aplicación',
    'scope_help' => 'Alcance de navegación para la aplicación',
    
    // Appearance
    'theme_color' => 'Color del Tema',
    'background_color' => 'Color de Fondo',
    'display_mode' => 'Modo de Visualización',
    'orientation' => 'Orientación',
    'theme_color_help' => 'Color de la interfaz del navegador',
    'background_color_help' => 'Color de fondo cuando se inicia la aplicación',
    'display_mode_help' => 'Cómo debe mostrarse la aplicación',
    'orientation_help' => 'Orientación de pantalla predeterminada',
    
    // Display Modes
    'standalone' => 'Independiente',
    'fullscreen' => 'Pantalla Completa',
    'minimal_ui' => 'UI Mínima',
    'browser' => 'Navegador',
    
    // Orientations
    'any' => 'Cualquiera',
    'portrait' => 'Vertical',
    'landscape' => 'Horizontal',
    'portrait_primary' => 'Vertical Principal',
    'portrait_secondary' => 'Vertical Secundario',
    'landscape_primary' => 'Horizontal Principal',
    'landscape_secondary' => 'Horizontal Secundario',
    
    // Icons & Images
    'icon_192' => 'Icono 192x192',
    'icon_512' => 'Icono 512x512',
    'maskable_icon_192' => 'Icono Enmascarable 192x192',
    'maskable_icon_512' => 'Icono Enmascarable 512x512',
    'apple_touch_icon' => 'Icono Apple Touch',
    'screenshots' => 'Capturas de Pantalla',
    'auto_generate' => 'Generar Iconos Automáticamente',
    'upload_source' => 'Subir Imagen de Origen',
    'current_icon' => 'Icono Actual',
    'no_icon' => 'No hay icono subido',
    'icon_192_help' => 'Icono estándar 192x192 píxeles',
    'icon_512_help' => 'Icono estándar 512x512 píxeles',
    'maskable_icon_help' => 'Los iconos enmascarables se adaptan a diferentes formas de dispositivo',
    'apple_touch_icon_help' => 'Icono para pantalla de inicio de iOS (180x180)',
    'screenshots_help' => 'Capturas de pantalla de la aplicación (540x720 recomendado)',
    'auto_generate_help' => 'Sube una imagen para generar todos los tamaños de iconos',
    
    // Offline
    'offline_page_enable' => 'Activar Página Sin Conexión',
    'offline_page_title' => 'Título de Página Sin Conexión',
    'offline_page_message' => 'Mensaje de Página Sin Conexión',
    'cache_strategy' => 'Estrategia de Caché',
    'cache_version' => 'Versión de Caché',
    'clear_cache' => 'Limpiar Caché',
    'offline_page_title_help' => 'Título mostrado cuando el usuario está sin conexión',
    'offline_page_message_help' => 'Mensaje mostrado en la página sin conexión',
    'cache_strategy_help' => 'Cómo debe almacenarse en caché el contenido',
    'cache_version_help' => 'Incrementar para forzar actualización de caché',
    
    // Cache Strategies
    'cache_first' => 'Caché Primero',
    'network_first' => 'Red Primero',
    'stale_while_revalidate' => 'Antiguo Mientras Revalida',
    
    // Push Notifications
    'push_enable' => 'Activar Notificaciones Push',
    'notification_icon' => 'Icono de Notificación',
    'notification_badge' => 'Insignia de Notificación',
    'vapid_public_key' => 'Clave Pública VAPID',
    'vapid_private_key' => 'Clave Privada VAPID',
    'generate_vapid_keys' => 'Generar Claves VAPID',
    'notification_icon_help' => 'Icono mostrado en notificaciones (96x96)',
    'notification_badge_help' => 'Icono de insignia para notificaciones (96x96)',
    'vapid_keys_help' => 'Requerido para notificaciones push web',
    
    // Shortcuts
    'shortcuts_enable' => 'Activar Accesos Directos',
    'add_shortcut' => 'Agregar Acceso Directo',
    'remove_shortcut' => 'Eliminar',
    'shortcut_name' => 'Nombre',
    'shortcut_short_name' => 'Nombre Corto',
    'shortcut_description' => 'Descripción',
    'shortcut_url' => 'URL',
    'shortcut_icon' => 'Icono',
    'shortcuts_help' => 'Accesos directos de acceso rápido en el lanzador de aplicaciones',
    
    // Advanced
    'categories' => 'Categorías',
    'related_apps' => 'Aplicaciones Relacionadas',
    'prefer_related_apps' => 'Preferir Aplicaciones Relacionadas',
    'language' => 'Idioma',
    'text_direction' => 'Dirección del Texto',
    'ltr' => 'Izquierda a Derecha',
    'rtl' => 'Derecha a Izquierda',
    'categories_help' => 'Categorías PWA para tiendas de aplicaciones',
    'related_apps_help' => 'Aplicaciones nativas relacionadas con esta PWA (formato JSON)',
    'prefer_related_apps_help' => 'Sugerir aplicación nativa en lugar de PWA',
    
    // Categories Options
    'books' => 'Libros',
    'business' => 'Negocios',
    'education' => 'Educación',
    'entertainment' => 'Entretenimiento',
    'finance' => 'Finanzas',
    'fitness' => 'Fitness',
    'food' => 'Comida',
    'games' => 'Juegos',
    'government' => 'Gobierno',
    'health' => 'Salud',
    'kids' => 'Niños',
    'lifestyle' => 'Estilo de Vida',
    'magazines' => 'Revistas',
    'medical' => 'Médico',
    'music' => 'Música',
    'navigation' => 'Navegación',
    'news' => 'Noticias',
    'personalization' => 'Personalización',
    'photo' => 'Foto',
    'politics' => 'Política',
    'productivity' => 'Productividad',
    'security' => 'Seguridad',
    'shopping' => 'Compras',
    'social' => 'Social',
    'sports' => 'Deportes',
    'travel' => 'Viajes',
    'utilities' => 'Utilidades',
    'weather' => 'Clima',
    
    // Actions
    'save_settings' => 'Guardar Configuración',
    'test_install' => 'Probar Instalación PWA',
    'preview' => 'Vista Previa',
    'upload' => 'Subir',
    'browse' => 'Examinar',
    'generate' => 'Generar',
    
    // Messages
    'settings_saved' => 'Configuración PWA guardada exitosamente',
    'icons_generated' => 'Iconos generados exitosamente',
    'cache_cleared' => 'Caché limpiado exitosamente',
    'error_occurred' => 'Ocurrió un error',
    'file_size_error' => 'Tamaño de archivo demasiado grande',
    'file_type_error' => 'Tipo de archivo inválido',
    'fill_required_fields' => 'Por favor complete todos los campos requeridos',
    
    // Info
    'pwa_info' => 'Progressive Web App (PWA) permite a los usuarios instalar su sitio web como una aplicación en sus dispositivos.',
    'manifest_url' => 'URL del Manifiesto',
    'service_worker_url' => 'URL del Service Worker',
    'pwa_status' => 'Estado PWA',
    'installable' => 'Instalable',
    'not_installable' => 'No Instalable',
    
];
