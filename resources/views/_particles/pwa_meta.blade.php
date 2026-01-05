@php
    $pwa_settings = \App\PwaSettings::getSettings();
@endphp

@if($pwa_settings->pwa_enabled)
{{-- Web App Manifest --}}
<link rel="manifest" href="{{ route('pwa.manifest') }}">

{{-- Theme Color --}}
<meta name="theme-color" content="{{ $pwa_settings->theme_color }}">

{{-- Apple Touch Icons --}}
@if($pwa_settings->apple_touch_icon)
<link rel="apple-touch-icon" href="{{ asset($pwa_settings->apple_touch_icon) }}">
@endif

{{-- iOS Meta Tags --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $pwa_settings->app_short_name }}">

{{-- Windows Tile --}}
<meta name="msapplication-TileColor" content="{{ $pwa_settings->theme_color }}">
@if($pwa_settings->icon_512)
<meta name="msapplication-TileImage" content="{{ asset($pwa_settings->icon_512) }}">
@endif

{{-- Application Name --}}
<meta name="application-name" content="{{ $pwa_settings->app_name }}">

{{-- Description --}}
@if($pwa_settings->app_description)
<meta name="description" content="{{ $pwa_settings->app_description }}">
@endif

{{-- Service Worker Registration --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration.scope);
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        });
    }
</script>

{{-- PWA Install Prompt --}}
<script>
    let deferredPrompt;
    let installButton;

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();

        // Stash the event so it can be triggered later
        deferredPrompt = e;

        // Show custom install button/banner if exists
        installButton = document.getElementById('pwa-install-button');
        if (installButton) {
            installButton.style.display = 'block';

            installButton.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    deferredPrompt = null;
                    installButton.style.display = 'none';
                }
            });
        }
    });

    window.addEventListener('appinstalled', () => {
        console.log('PWA was installed');
        deferredPrompt = null;
        if (installButton) {
            installButton.style.display = 'none';
        }
    });
</script>

@if($pwa_settings->push_notification_enabled && $pwa_settings->vapid_public_key)
{{-- Push Notification Setup --}}
<script>
    // Request notification permission
    function requestNotificationPermission() {
        if ('Notification' in window && 'serviceWorker' in navigator) {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    subscribeUserToPush();
                }
            });
        }
    }

    function subscribeUserToPush() {
        navigator.serviceWorker.ready.then(function(registration) {
            const vapidPublicKey = '{{ $pwa_settings->vapid_public_key }}';

            registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            }).then(function(subscription) {
                console.log('User is subscribed:', subscription);
                // Send subscription to server
                sendSubscriptionToServer(subscription);
            }).catch(function(err) {
                console.log('Failed to subscribe user: ', err);
            });
        });
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function sendSubscriptionToServer(subscription) {
        // Send subscription data to your server
        // You can implement this based on your backend API
        fetch('/api/push-subscription', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(subscription)
        }).then(response => response.json())
          .then(data => console.log('Subscription saved:', data))
          .catch(error => console.error('Error saving subscription:', error));
    }
</script>
@endif

{{-- iOS Add to Home Screen Prompt Styles --}}
<style>
    .ios-install-prompt {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: {{ $pwa_settings->theme_color }};
        color: white;
        padding: 15px;
        text-align: center;
        z-index: 9999;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
    }

    .ios-install-prompt button {
        background: white;
        color: {{ $pwa_settings->theme_color }};
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        margin-top: 10px;
        cursor: pointer;
        font-weight: bold;
    }
</style>

{{-- iOS Installation Prompt Detection --}}
<script>
    // Detect if device is iOS
    const isIos = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent);
    };

    // Detect if app is in standalone mode
    const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

    // Show iOS install prompt if applicable
    if (isIos() && !isInStandaloneMode()) {
        // You can show custom iOS installation instructions here
        console.log('iOS device detected. User can add to home screen manually.');
    }
</script>
@endif
