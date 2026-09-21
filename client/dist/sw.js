// Service worker de la PWA.
//
// La versión anterior era "primero caché, luego red" con un nombre de caché
// fijo. Resultado: después de cada despliegue el celular seguía ejecutando el
// JavaScript ANTERIOR hasta que la caché se vaciara sola, y el index.html
// cacheado apuntaba a un archivo con hash que ya no existía en el servidor.
//
// Ahora:
//   - HTML y navegación: SIEMPRE red primero. Solo se usa la caché si no hay
//     conexión. Así cada apertura trae la versión desplegada.
//   - Archivos con hash (assets/index-XXXX.js): caché primero. Son inmutables
//     por construcción: si cambia el contenido, cambia el nombre.
//   - /api/: nunca se toca.
//   - El nombre de la caché lleva fecha: cada despliegue borra la anterior.
const CACHE = 'aicompany-2026-09-20';

self.addEventListener('install', e => { self.skipWaiting(); });

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', e => {
    const req = e.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;
    if (url.pathname.startsWith('/api/')) return;

    // Assets con hash: inmutables, caché primero.
    const esAssetConHash = /\/assets\/.+-[A-Za-z0-9_-]{8,}\.(js|css|woff2?)$/.test(url.pathname);
    if (esAssetConHash) {
        e.respondWith(
            caches.match(req).then(hit => hit || fetch(req).then(res => {
                if (res && res.status === 200) caches.open(CACHE).then(c => c.put(req, res.clone()));
                return res;
            }))
        );
        return;
    }

    // Todo lo demás (index.html, manifest, imágenes): red primero, caché de respaldo.
    e.respondWith(
        fetch(req).then(res => {
            if (res && res.status === 200 && res.type === 'basic') {
                caches.open(CACHE).then(c => c.put(req, res.clone()));
            }
            return res;
        }).catch(() => caches.match(req).then(hit => hit || caches.match('/')))
    );
});
