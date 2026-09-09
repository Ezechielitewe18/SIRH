const CACHE_NAME = 'globit-v5';
const APP_SHELL = [
  './',
  './manifest.webmanifest',
  './public/img/icon-192.png',
  './public/img/icon-512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(APP_SHELL))
      .then(() => self.skipWaiting())
  );
});

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

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  if (url.origin !== location.origin) return;

  if (url.pathname.includes('/api.php')) {
    event.respondWith(fetch(event.request).catch(() =>
      new Response(JSON.stringify({ success: false, message: 'Hors ligne' }),
        { status: 503, headers: { 'Content-Type': 'application/json' } })
    ));
    return;
  }

  const isNav = event.request.mode === 'navigate';
  const isShell = url.pathname.endsWith('/mobile.php') || url.pathname.endsWith('/SIRH/') || url.pathname.endsWith('/SIRH');

  if (isShell || isNav) {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          if (response && response.status === 200) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
          }
          return response;
        })
        .catch(() => caches.match(event.request).then((cached) =>
          cached || caches.match('./mobile.php')
        ))
    );
    return;
  }

  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      return fetch(event.request).then((response) => {
        if (response && response.status === 200 && response.type === 'basic') {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
        }
        return response;
      }).catch(() => caches.match('./mobile.php'));
    })
  );
});

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
