// Immediately take control of the page, see the 'Immediate Claim' recipe
// for a detailed explanation of the implementation of the following two
// event listeners.
var url = 'index.php';

self.addEventListener('install', function(event) {
  console.log('Installed');
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function(event) {
  console.log('Active');
  event.waitUntil(self.clients.claim());
});

// Register event listener for the 'push' event.
self.addEventListener('push', function(event) {
  event.waitUntil(
    fetch('admin/infohandler.php?wants=notification', {credentials: 'same-origin'}).then(function(response) {
      return response.json().then(function(data) {
        console.log('Current state: ', JSON.stringify(data, null, 4));
        url = data['url'];
        return data;
      }).catch(function(error) {
        console.log('Error: ', error);
        return {'title': 'Film Night', 'body': 'You received a notification, but there was an error finding out what it is'}
      }).then(function(data) {
        return self.registration.showNotification(data['title'], {
          body: data['body'],
          icon: 'assets/icons/favicon.png'
        })
      });
    })
  );
});

// Register event listener for the 'notificationclick' event.
self.addEventListener('notificationclick', function(event) {
  event.waitUntil(
    // Retrieve a list of the clients of this service worker.
    self.clients.matchAll().then(function(clientList) {
      // If there is at least one client, focus it.
      if (clientList.length > 0) {
        return clientList[0].focus();
      }

      // Otherwise, open a new page.
      return self.clients.openWindow(url);
    })
  );
});
