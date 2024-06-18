const CACHE_NAME = "coopex";
const urlsToCache = [
  "./login.php",
  "./coopex.php"
  // Adicione aqui outros arquivos estáticos que você deseja cache
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches
      .match(event.request)
      .then((response) => response || fetch(event.request))
  );
});
