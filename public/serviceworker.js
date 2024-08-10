var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
    "/storage/01J4YRHKF087SA50FEM45Q54J1.png",
    "/storage/01J4YRHKF2BM0FAZW1DW2Z91YY.png",
    "/storage/01J4YRHKF3845W4KY8TKDHB4DC.png",
    "/storage/01J4YRHKF480NDH5QEACAS54GN.png",
    "/storage/01J4YRHKF5C2Z57VDS0PKEE15T.png",
    "/storage/01J4YRHKF6A990K0VNP51JPYR3.png",
    "/storage/01J4YRHKF6A990K0VNP51JPYR4.png",
    "/storage/01J4YRHKF74V9NF9KHNMNDW323.png"
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
