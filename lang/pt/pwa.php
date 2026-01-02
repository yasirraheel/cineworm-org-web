<?php

return [
    // PWA Settings
    'pwa_settings' => 'Configurações PWA',
    'pwa_enable' => 'Ativar PWA',
    'pwa_disabled' => 'Desativado',
    'pwa_enabled' => 'Ativado',
    
    // Tabs
    'general_tab' => 'Geral',
    'appearance_tab' => 'Aparência',
    'icons_images_tab' => 'Ícones e Imagens',
    'offline_tab' => 'Experiência Offline',
    'notifications_tab' => 'Notificações Push',
    'shortcuts_tab' => 'Atalhos',
    'advanced_tab' => 'Avançado',
    
    // General Settings
    'app_name' => 'Nome do Aplicativo',
    'app_short_name' => 'Nome Curto',
    'app_description' => 'Descrição do Aplicativo',
    'start_url' => 'URL Inicial',
    'scope' => 'Escopo',
    'app_name_help' => 'Nome completo do seu aplicativo',
    'app_short_name_help' => 'Nome curto (12 caracteres ou menos)',
    'app_description_help' => 'Breve descrição do seu aplicativo',
    'start_url_help' => 'URL que carrega quando o aplicativo é iniciado',
    'scope_help' => 'Escopo de navegação para o aplicativo',
    
    // Appearance
    'theme_color' => 'Cor do Tema',
    'background_color' => 'Cor de Fundo',
    'display_mode' => 'Modo de Exibição',
    'orientation' => 'Orientação',
    'theme_color_help' => 'Cor da interface do navegador',
    'background_color_help' => 'Cor de fundo quando o aplicativo é iniciado',
    'display_mode_help' => 'Como o aplicativo deve ser exibido',
    'orientation_help' => 'Orientação de tela padrão',
    
    // Display Modes
    'standalone' => 'Autônomo',
    'fullscreen' => 'Tela Cheia',
    'minimal_ui' => 'UI Mínima',
    'browser' => 'Navegador',
    
    // Orientations
    'any' => 'Qualquer',
    'portrait' => 'Retrato',
    'landscape' => 'Paisagem',
    'portrait_primary' => 'Retrato Principal',
    'portrait_secondary' => 'Retrato Secundário',
    'landscape_primary' => 'Paisagem Principal',
    'landscape_secondary' => 'Paisagem Secundário',
    
    // Icons & Images
    'icon_192' => 'Ícone 192x192',
    'icon_512' => 'Ícone 512x512',
    'maskable_icon_192' => 'Ícone Mascarável 192x192',
    'maskable_icon_512' => 'Ícone Mascarável 512x512',
    'apple_touch_icon' => 'Ícone Apple Touch',
    'screenshots' => 'Capturas de Tela',
    'auto_generate' => 'Gerar Ícones Automaticamente',
    'upload_source' => 'Carregar Imagem de Origem',
    'current_icon' => 'Ícone Atual',
    'no_icon' => 'Nenhum ícone carregado',
    'icon_192_help' => 'Ícone padrão 192x192 pixels',
    'icon_512_help' => 'Ícone padrão 512x512 pixels',
    'maskable_icon_help' => 'Ícones mascaráveis se adaptam a diferentes formatos de dispositivos',
    'apple_touch_icon_help' => 'Ícone para tela inicial do iOS (180x180)',
    'screenshots_help' => 'Capturas de tela do aplicativo (540x720 recomendado)',
    'auto_generate_help' => 'Carregue uma imagem para gerar todos os tamanhos de ícones',
    
    // Offline
    'offline_page_enable' => 'Ativar Página Offline',
    'offline_page_title' => 'Título da Página Offline',
    'offline_page_message' => 'Mensagem da Página Offline',
    'cache_strategy' => 'Estratégia de Cache',
    'cache_version' => 'Versão do Cache',
    'clear_cache' => 'Limpar Cache',
    'offline_page_title_help' => 'Título exibido quando o usuário está offline',
    'offline_page_message_help' => 'Mensagem exibida na página offline',
    'cache_strategy_help' => 'Como o conteúdo deve ser armazenado em cache',
    'cache_version_help' => 'Incrementar para forçar atualização do cache',
    
    // Cache Strategies
    'cache_first' => 'Cache Primeiro',
    'network_first' => 'Rede Primeiro',
    'stale_while_revalidate' => 'Antigo Enquanto Revalida',
    
    // Push Notifications
    'push_enable' => 'Ativar Notificações Push',
    'notification_icon' => 'Ícone de Notificação',
    'notification_badge' => 'Distintivo de Notificação',
    'vapid_public_key' => 'Chave Pública VAPID',
    'vapid_private_key' => 'Chave Privada VAPID',
    'generate_vapid_keys' => 'Gerar Chaves VAPID',
    'notification_icon_help' => 'Ícone exibido em notificações (96x96)',
    'notification_badge_help' => 'Ícone de distintivo para notificações (96x96)',
    'vapid_keys_help' => 'Necessário para notificações push web',
    
    // Shortcuts
    'shortcuts_enable' => 'Ativar Atalhos',
    'add_shortcut' => 'Adicionar Atalho',
    'remove_shortcut' => 'Remover',
    'shortcut_name' => 'Nome',
    'shortcut_short_name' => 'Nome Curto',
    'shortcut_description' => 'Descrição',
    'shortcut_url' => 'URL',
    'shortcut_icon' => 'Ícone',
    'shortcuts_help' => 'Atalhos de acesso rápido no iniciador de aplicativos',
    
    // Advanced
    'categories' => 'Categorias',
    'related_apps' => 'Aplicativos Relacionados',
    'prefer_related_apps' => 'Preferir Aplicativos Relacionados',
    'language' => 'Idioma',
    'text_direction' => 'Direção do Texto',
    'ltr' => 'Esquerda para Direita',
    'rtl' => 'Direita para Esquerda',
    'categories_help' => 'Categorias PWA para lojas de aplicativos',
    'related_apps_help' => 'Aplicativos nativos relacionados a esta PWA (formato JSON)',
    'prefer_related_apps_help' => 'Sugerir aplicativo nativo em vez de PWA',
    
    // Categories Options
    'books' => 'Livros',
    'business' => 'Negócios',
    'education' => 'Educação',
    'entertainment' => 'Entretenimento',
    'finance' => 'Finanças',
    'fitness' => 'Fitness',
    'food' => 'Comida',
    'games' => 'Jogos',
    'government' => 'Governo',
    'health' => 'Saúde',
    'kids' => 'Crianças',
    'lifestyle' => 'Estilo de Vida',
    'magazines' => 'Revistas',
    'medical' => 'Médico',
    'music' => 'Música',
    'navigation' => 'Navegação',
    'news' => 'Notícias',
    'personalization' => 'Personalização',
    'photo' => 'Foto',
    'politics' => 'Política',
    'productivity' => 'Produtividade',
    'security' => 'Segurança',
    'shopping' => 'Compras',
    'social' => 'Social',
    'sports' => 'Esportes',
    'travel' => 'Viagem',
    'utilities' => 'Utilitários',
    'weather' => 'Clima',
    
    // Actions
    'save_settings' => 'Salvar Configurações',
    'test_install' => 'Testar Instalação PWA',
    'preview' => 'Visualizar',
    'upload' => 'Carregar',
    'browse' => 'Navegar',
    'generate' => 'Gerar',
    
    // Messages
    'settings_saved' => 'Configurações PWA salvas com sucesso',
    'icons_generated' => 'Ícones gerados com sucesso',
    'cache_cleared' => 'Cache limpo com sucesso',
    'error_occurred' => 'Ocorreu um erro',
    'file_size_error' => 'Tamanho do arquivo muito grande',
    'file_type_error' => 'Tipo de arquivo inválido',
    'fill_required_fields' => 'Por favor, preencha todos os campos obrigatórios',
    
    // Info
    'pwa_info' => 'Progressive Web App (PWA) permite que os usuários instalem seu site como um aplicativo em seus dispositivos.',
    'manifest_url' => 'URL do Manifesto',
    'service_worker_url' => 'URL do Service Worker',
    'pwa_status' => 'Status PWA',
    'installable' => 'Instalável',
    'not_installable' => 'Não Instalável',
    
];
