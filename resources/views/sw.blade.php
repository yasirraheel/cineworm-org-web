const CACHE_VERSION = '{{ $pwa_settings->cache_version }}';
const CACHE_NAME = 'cineworm-cache-' + CACHE_VERSION;
const OFFLINE_URL = '/offline';

const CACHE_STRATEGY = '{{ $pwa_settings->cache_strategy }}';

// Assets to cache on install
const ASSETS_TO_CACHE = [
    '/',
    '/offline',
    '/manifest.json',
];

// Install event - cache assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// Fetch event - handle requests based on strategy
self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // Handle navigation requests
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                return caches.match(OFFLINE_URL);
            })
        );
        return;
    }

    // Apply cache strategy
    @if($pwa_settings->cache_strategy === 'cache-first')
        event.respondWith(cacheFirst(event.request));
    @elseif($pwa_settings->cache_strategy === 'network-first')
        event.respondWith(networkFirst(event.request));
    @else
        event.respondWith(staleWhileRevalidate(event.request));
    @endif
});

// Cache First Strategy
async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    
    if (cached) {
        return cached;
    }
    
    try {
        const response = await fetch(request);
        if (response.status === 200) {
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return caches.match(OFFLINE_URL);
    }
}

// Network First Strategy
async function networkFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    
    try {
        const response = await fetch(request);
        if (response.status === 200) {
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }
        return caches.match(OFFLINE_URL);
    }
}

// Stale While Revalidate Strategy
async function staleWhileRevalidate(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    
    const fetchPromise = fetch(request).then((response) => {
        if (response.status === 200) {
            cache.put(request, response.clone());
        }
        return response;
    });
    
    return cached || fetchPromise;
}

@if($pwa_settings->push_notification_enabled)
// Push notification event
self.addEventListener('push', (event) => {
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '{{ $pwa_settings->notification_icon ? asset($pwa_settings->notification_icon) : asset('images/logo.png') }}',
        badge: '{{ $pwa_settings->notification_badge ? asset($pwa_settings->notification_badge) : asset('images/logo.png') }}',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        }
    };
    
    event.waitUntil(
        self.registration.showNotification('{{ $pwa_settings->app_name }}', options)
    );
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    event.waitUntil(
        clients.openWindow('/')
    );
});
@endif

// Background sync event (for future use)
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-data') {
        event.waitUntil(syncData());
    }
});

async function syncData() {
    // Implement background sync logic here
    console.log('Background sync triggered');
}
