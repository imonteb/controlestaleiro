const CACHE_NAME = 'estaleiro-cache-v4';
const assetsToCache = [
  '/',
  '/phone',
  '/cme_logo.svg',
  '/manifest.json'
];

// Instalación: Guardamos los archivos base
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(assetsToCache))
  );
  self.skipWaiting();
});

// Activación: Limpiamos cachés antiguas
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(
      keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
    ))
  );
  self.clients.claim();
});

// Estrategia: "Network First" (Red primero, luego caché)
self.addEventListener('fetch', event => {
  // Solo interceptamos peticiones GET que NO sean de Livewire
  if (event.request.method !== 'GET' || event.request.url.includes('/livewire/')) return;

  event.respondWith(async function () {
    try {
      return await fetch(event.request);
    } catch {
      const cached = await caches.match(event.request);
      return cached || new Response('Offline: Recurso não disponível', {
        status: 503,
        statusText: 'Service Unavailable',
        headers: { 'Content-Type': 'text/plain; charset=utf-8' },
      });
    }
  }());
});

// ── PUSH NOTIFICATIONS ────────────────────────────────────────
self.addEventListener('push', event => {
  let data = { title: 'CME C016', body: 'Tem uma notificação.', icon: '/images/procme_logo.svg', badge: '/images/procme_logo.svg', tag: 'cme-notif', url: '/phone' };

  if (event.data) {
    try { Object.assign(data, event.data.json()); } catch (e) { data.body = event.data.text(); }
  }

  // Tudo dentro de waitUntil para que Chrome não mate o SW antes de terminar
  event.waitUntil(
    Promise.all([
      self.registration.showNotification(data.title, {
        body:    data.body,
        icon:    data.icon   || '/images/procme_logo.svg',
        badge:   data.badge  || '/images/procme_logo.svg',
        tag:     data.tag    || 'cme-notif',
        data:    { url: data.url || '/phone' },
        vibrate: [200, 100, 200],
        requireInteraction: data.requireInteraction || false,
      }),
      clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
        for (const client of clientList) {
          client.postMessage({ type: 'PUSH_RECEIVED' });
        }
      }),
    ])
  );
});

// Clic en la notificación → abre/foca la app
self.addEventListener('notificationclick', event => {
  event.notification.close();

  const targetUrl = event.notification.data?.url || '/phone';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
      for (const client of clientList) {
        if (client.url.includes('/phone') && 'focus' in client) {
          return client.focus();
        }
      }
      return clients.openWindow(targetUrl);
    })
  );
});
