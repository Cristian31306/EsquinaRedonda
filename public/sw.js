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

    // Estrategia Network First para una App en Tiempo Real
    event.respondWith(
        fetch(event.request).then(response => {
            // Clonar y guardar en cache los recursos estáticos ensamblados por Vite
            if (event.request.url.includes('/build/') && response.status === 200) {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
            }
            return response;
        }).catch(() => {
            // Si no hay red, buscar en el cache la versión offline guardada
            return caches.match(event.request);
        })
    );
});
