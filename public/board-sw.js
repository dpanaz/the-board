self.addEventListener('push', event => {
    let data = {};
    try { data = event.data ? event.data.json() : {}; } catch (_) { data = { body: event.data?.text() || '' }; }
    const title = String(data.title || 'The Board').slice(0, 120);
    const options = {
        body: String(data.body || 'You have a new update.').slice(0, 300),
        icon: '/favicon.ico',
        tag: String(data.tag || 'board-update'),
        data: { url: typeof data.url === 'string' && data.url.startsWith('/') && !data.url.startsWith('//') ? data.url : '/' }
    };
    event.waitUntil(self.registration.showNotification(title, options));
});
self.addEventListener('notificationclick', event => {
    event.notification.close();
    const path = event.notification.data?.url || '/';
    const target = new URL(path, self.location.origin).href;
    event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(async windows => {
        for (const client of windows) {
            if (client.url.startsWith(self.location.origin)) {
                await client.focus();
                if ('navigate' in client) await client.navigate(target);
                return;
            }
        }
        return clients.openWindow(target);
    }));
});
