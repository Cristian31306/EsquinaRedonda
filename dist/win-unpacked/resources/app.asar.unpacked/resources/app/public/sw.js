const CACHE_NAME = 'parkiapp-cache-v1';
const STATIC_ASSETS = [
    '/favicon.ico',
    '/manifest.json'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    // Solo interceptar peticiones GET
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // EXCEPCIÓN: Si es una ruta de administración o no es estática, pasar directo a la red.
    // Esto evita el error "Failed to fetch" en rutas dinámicas como /users o /reports
    if (!url.pathname.includes('/build/') && !url.pathname.includes('/assets/')) {
        return; // Dejar que el navegador lo maneje normalmente
    }

    // Estrategia Network First para recursos estáticos clonados por Vite
    event.respondWith(
        fetch(event.request).then(response => {
            if (response.status === 200) {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
            }
            return response;
        }).catch(() => {
            return caches.match(event.request);
        })
    );
});
