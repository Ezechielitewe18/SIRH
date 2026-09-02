/* GLOBIT - Service Worker PWA */
const CACHE_NAME = 'globit-v1';
const APP_SHELL = [
  './',
  './mobile.php',
  './manifest.webmanifest',
  './public/img/icon-192.png',
  './public/img/icon-512.png'
];

// Installation : pré-cacher le shell applicatif
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(APP_SHELL))
      .then(() => self.skipWaiting())
  );
});

// Activation : nettoyer les anciens caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

// Stratégie : cache d'abord pour le shell, réseau d'abord pour les API
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Ne pas intercepter les requêtes cross-origin (API sur même origine OK)
  if (url.origin !== location.origin) return;

  // Les requêtes API ne doivent JAMAIS être servies depuis le cache
  if (url.pathname.includes('/api.php')) {
    event.respondWith(fetch(event.request).catch(() =>
      new Response(JSON.stringify({ success: false, message: 'Hors ligne' }),
        { status: 503, headers: { 'Content-Type': 'application/json' } })
    ));
    return;
  }

  // Cache-first pour le shell et les assets statiques
  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      return fetch(event.request).then((response) => {
        // Mettre en cache les réponses statiques réussies
        if (response && response.status === 200 && response.type === 'basic') {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
        }
        return response;
      }).catch(() => {
        // Fallback : page d'accueil pour les navigations hors-ligne
        if (event.request.mode === 'navigate') {
          return caches.match('./mobile.php');
        }
      });
    })
  );
});

// Notifications push (à brancher plus tard avec un endpoint d'envoi)
self.addEventListener('push', (event) => {
  const data = event.data ? event.data.json() : {};
  const options = {
    body: data.message || 'Nouvelle notification GLOBIT',
    icon: './public/img/icon-192.png',
    badge: './public/img/icon-192.png',
    vibrate: [100, 50, 100]
  };
  event.waitUntil(
    self.registration.showNotification(data.title || 'GLOBIT', options)
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if ('focus' in client) return client.focus();
      }
      return clients.openWindow('./mobile.php');
    })
  );
});
