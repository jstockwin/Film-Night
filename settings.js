// [Working example](/serviceworker-cookbook/push-subscription-management/).
var subscriptionButton = document.getElementById('#registerWorker');

// As subscription object is needed in few places let's create a method which
// returns a promise.
function getSubscription() {
  return navigator.serviceWorker.ready
    .then(function(registration) {
      return registration.pushManager.getSubscription();
    });
}

// Register service worker and check the initial subscription state.
// Set the UI (button) according to the status.
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('service-worker.js')
    .then(function() {
      console.log('service worker registered');
      subscriptionButton.removeAttribute('disabled');
    });
  getSubscription().then(function(subscription) {
    if (subscription) {
      console.log('Already subscribed', subscription.endpoint);
      
      fetch('admin/infohandler.php?wants=endpoint', {credentials: 'same-origin'}).then(function(response) {
        return response.text();
      }).then(function(data) {
        console.log('Server subscription at', data);
        if(data == subscription.endpoint) {
          setUnsubscribeButton();
        } else if (data == '') {
          setSubscribeButton()
        } else {
          setChangeSubscriptionButton();
        }
      }).catch(function() {
        console.log('Couldn\'t contact server');
      });
    } else {
      setSubscribeButton();
    }
  });
}

// Get the `registration` from service worker and create a new
// subscription using `registration.pushManager.subscribe`. Then
// register received new subscription by sending a POST request with its
// endpoint to the server.
function subscribe() {
  navigator.serviceWorker.ready.then(function(registration) {
    return registration.pushManager.subscribe({ userVisibleOnly: true });
  }).then(function(subscription) {
    console.log('Subscribed', subscription.endpoint);
    return fetch('admin/subscribehandler.php', {
      method: 'post',
      credentials: 'same-origin',
      headers: {
        'Content-type': 'application/x-www-form-urlencoded',
      },
      body: 'endpoint=' + subscription.endpoint
    });
  }).then(setUnsubscribeButton);
}

// Get existing subscription from service worker, unsubscribe
// (`subscription.unsubscribe()`) and unregister it in the server with
// a POST request to stop sending push messages to
// unexisting endpoint.
function unsubscribe() {
  getSubscription().then(function(subscription) {
    return subscription.unsubscribe()
      .then(function() {
        console.log('Unsubscribed', subscription.endpoint);
        return fetch('admin/unsubscribehandler.php', {
          method: 'post',
          credentials: 'same-origin',
          headers: {
            'Content-type': 'application/x-www-form-urlencoded'
          },
          body: 'endpoint=' + subscription.endpoint
        });
      });
  }).then(setSubscribeButton);
}

// Change the subscription button's text and action.
function setSubscribeButton() {
  subscriptionButton.onclick = subscribe;
  subscriptionButton.textContent = 'Click to Subscribe';
}

function setUnsubscribeButton() {
  subscriptionButton.onclick = unsubscribe;
  subscriptionButton.textContent = 'Click to Unsubscribe';
}

function setChangeSubscriptionButton() {
  subscriptionButton.onclick = subscribe;
  subscriptionButton.textContent = 'Click to Move Subscription to this Browser';
}
