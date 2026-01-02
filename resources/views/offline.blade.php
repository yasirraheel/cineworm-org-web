<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pwa_settings->offline_page_title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, {{ $pwa_settings->background_color }} 0%, {{ $pwa_settings->theme_color }} 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #333;
            padding: 20px;
        }

        .offline-container {
            text-align: center;
            max-width: 500px;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .offline-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: {{ $pwa_settings->theme_color }};
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        .offline-icon svg {
            width: 60px;
            height: 60px;
            fill: white;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 30px;
        }

        .retry-button {
            background: {{ $pwa_settings->theme_color }};
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .retry-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .retry-button:active {
            transform: translateY(0);
        }

        .app-name {
            margin-top: 30px;
            font-size: 14px;
            color: #999;
        }

        @media (max-width: 480px) {
            .offline-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            p {
                font-size: 14px;
            }

            .offline-icon {
                width: 100px;
                height: 100px;
            }

            .offline-icon svg {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/>
                <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>

        <h1>{{ $pwa_settings->offline_page_title }}</h1>

        @if($pwa_settings->offline_page_message)
            <p>{{ $pwa_settings->offline_page_message }}</p>
        @else
            <p>You're currently offline. Please check your internet connection and try again.</p>
        @endif

        <button class="retry-button" onclick="window.location.reload()">
            <svg width="20" height="20" style="vertical-align: middle; margin-right: 8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            Retry Connection
        </button>

        <div class="app-name">{{ $pwa_settings->app_name }}</div>
    </div>

    <script>
        // Auto-retry when connection is restored
        window.addEventListener('online', function() {
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        });

        // Check connection status
        if (navigator.onLine) {
            window.location.reload();
        }
    </script>
</body>
</html>
