self.addEventListener("push", (event) => {
    const notification = event.data.json();

    event.waitUntil(self.registration.showNotification(notification.title, {
        body: notification.body,
        icon: "./assets/logo.png",
        data: {
            notifURL: notification.url
        }
    }));
});

self.addEventListener("notificationclick", (event) => {
    event.waitUntil(clients.openWindow(event.notification.data.notifURL));
});