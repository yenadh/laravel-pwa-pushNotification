function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open("notificationDb", 1);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            db.createObjectStore("urls", { keyPath: "id" });
        };
        request.onsuccess = (event) => {
            resolve(event.target.result);
        };
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

function storeUrl(id, url) {
    return openDatabase().then((db) => {
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(["urls"], "readwrite");
            const store = transaction.objectStore("urls");
            store.put({ id, url });
            transaction.oncomplete = () => resolve();
            transaction.onerror = () => reject(transaction.error);
        });
    });
}

function getUrl(id) {
    return openDatabase().then((db) => {
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(["urls"]);
            const store = transaction.objectStore("urls");
            const request = store.get(id);
            request.onsuccess = (event) =>
                resolve(event.target.result ? event.target.result.url : null);
            request.onerror = () => reject(request.error);
        });
    });
}

// Handle the push event
self.addEventListener("push", async (event) => {
    const notification = event.data.json();
    // Store the URL in IndexedDB with a key
    await storeUrl("notificationUrl", notification.url);

    self.registration.showNotification(notification.title, {
        body: notification.message,
        icon: "/icon.png",
        data: {
            url: notification.url,
        },
    });
});

// Handle notification click
self.addEventListener("notificationclick", (event) => {
    event.waitUntil(
        // Retrieve the URL from IndexedDB
        getUrl("notificationUrl").then((url) => {
            if (url) {
                clients.openWindow(url);
            }
        })
    );
});
