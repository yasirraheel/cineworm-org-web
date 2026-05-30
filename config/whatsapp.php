<?php

return [
    'server_url' => env('WA_SERVER_URL', 'http://127.0.0.1:3025'),
    'api_key' => env('WA_SERVER_API_KEY', 'change-this-long-random-key'),
    'timeout' => env('WA_SERVER_TIMEOUT', 8),
    'pm2_name' => env('WA_PM2_NAME', 'cineworm-whatsapp'),
    'server_path' => env('WA_SERVER_PATH', base_path('wa-server-js')),
];
