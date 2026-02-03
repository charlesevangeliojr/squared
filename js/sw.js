const CACHE_NAME = 'squared-qr-cache-v1';

const urlsToCache = [
  '/', 
  '/index.html',
  '/programs.php',
  '/avatars.php',
  '/css/landing.css',
  '/js/pass.js',
  '/js/avatarerror.js',
  '/js/login.js',
  '/js/service-worker.js',
  '/images/Squared_Logo.png',
  '/images/dcc_logo.png',
  '/images/Squared.png',
  '/images/Cover.jpg',
  '/images/Cover1.jpg',
  '/images/Cover2.jpg',
  '../images/Profile_Card.jpg',
  '/images/Squared_Logo.jpg',
  '/avatars/JOY.jpg',
  '/avatars/SEVI.jpg',
  '/avatars/SAMANTHA.jpg',
  '/avatars/ZEKE.jpg',
  '/fx/beep.mp3',
  '/fx/beep.wav',
  '/fx/error.wav',
  '/manifest.json'
];

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    (async () => {
      const cache = await caches.open(CACHE_NAME);
      let cachedCount = 0;

      for (const url of urlsToCache) {
        try {
          const response = await fetch(url);
          await cache.put(url, response.clone());
          cachedCount++;

          const clientsList = await self.clients.matchAll();
          clientsList.forEach(client => {
            client.postMessage({
              type: 'CACHE_PROGRESS',
              loaded: cachedCount,
              total: urlsToCache.length
            });
          });
        } catch (err) {
          console.warn(`Failed to cache: ${url}`, err);
        }
      }
    })()
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request).catch(() => {
        if (event.request.destination === 'document') {
          return caches.match('/index.php');
        }
      });
    })
  );
});

self.addEventListener('activate', (event) => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames.map((cacheName) => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      )
    )
  );
  self.clients.claim();
});
