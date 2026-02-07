// self.addEventListener("install", (event) => {
//     event.waitUntil(
//         caches.open("app-cache").then((cache) => {
//             return cache.addAll(["/admin"]);
//         }),
//     );
// });

// self.addEventListener("fetch", (event) => {
//     event.respondWith(
//         caches.match(event.request).then((resp) => {
//             return resp || fetch(event.request);
//         }),
//     );
// });

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open("app-cache").then((cache) => {
            return cache.addAll(["/", "/offline.html"]);
        }),
    );
});

self.addEventListener("fetch", (event) => {
    const url = new URL(event.request.url);

    // ❌ jangan cache area admin & login
    if (
        url.pathname.startsWith("/admin") ||
        url.pathname.startsWith("/login")
    ) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((resp) => {
            return resp || fetch(event.request);
        }),
    );
});
