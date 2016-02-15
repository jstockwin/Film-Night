// [Working example](/serviceworker-cookbook/push-subscription-management/).
var subscriptionButton = document.getElementById('#registerWorker');
var subscriptionName = document.getElementById('#registerName');

// As subscription object is needed in few places let's create a method which
// returns a promise.
function getSubscription() {
  return navigator.serviceWorker.ready
    .then(function(registration) {
      return registration.pushManager.getSubscription();
    });
}

function printSubInfo(end, endpoint) {
  if(endpoint == '') {
    console.log(end, 'not subscribed');
  } else {
    console.log(end, 'subscribed at', endpoint);
  }
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
    fetch('admin/infohandler.php?wants=endpoint', {credentials: 'same-origin'}).then(function(response) {
      return response.text();
    }).then(function(data) {
      printSubInfo('Server', data);
      console.log("Data: "+data);
      setSubscribeButton()
      console.log(subscription.endpoint);
      var strings = data.split(",");
      var inList = false;
      for(var i= 0; i < strings.length; i++){
        console.log(strings[i]);
        if(strings[i] == subscription.endpoint) {
          // This endpoint is already in our list. Remove subscribe button
          inList = true;
          printSubInfo('Local', subscription.endpoint);
          setUnsubscribeButton();
        }
      }
      if(!inList){
        // Browser is subscribed with Google (else this wouldn't be running?), let's unsubscribe.
        console.log("Browser not in sql db, removing subscription from Google");
        subscription.unsubscribe();
      }
    }).catch(function() {
      console.log('Couldn\'t contact server');
    });
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
      body: 'endpoint=' + subscription.endpoint + '& name=' + subscriptionName.value
    });
  }).then(function(response) {
        return response.text();
      }).then(function(data) {
        console.log(data);
        if(data.indexOf("Error")>-1){
          location.reload();
        }
      }).then(function(){setUnsubscribeButton; location.reload();});

}

// Get existing subscription from service worker, unsubscribe
// (`subscription.unsubscribe()`) and unregister it in the server with
// a POST request to stop sending push messages to
// unexisting endpoint.

// Changed by Jake, this now only removes the details from our database
// It removes the entry corresponding to the given primary key, but only if
// the ID of that entry is the same as the session ID.
// No longer unsubscribe from google.
function unsubscribe(key) {

    console.log('Unsubscribed', key);
    var name = 'registerDelete' + key;
    document.getElementById(name).style.display = "none";
    return fetch('admin/unsubscribehandler.php', {
      method: 'post',
      credentials: 'same-origin',
      headers: {
        'Content-type': 'application/x-www-form-urlencoded'
      },
      body: 'key=' + key
    });

}

// Change the subscription button's text and action.
function setSubscribeButton() {
  subscriptionButton.onclick = subscribe;
  subscriptionButton.textContent = 'Click to Subscribe';
  subscriptionButton.removeAttribute('disabled');
  subscriptionButton.style.display = "inline-block";
  subscriptionName.style.display = "inline-block";
}

function setUnsubscribeButton() {
  // Changed so that this now removes the button entirely.
  subscriptionButton.onclick = "";
  subscriptionButton.textContent = 'Button disabled';
  subscriptionButton.disabled = true;
  subscriptionButton.style.display = "none";
  subscriptionName.style.display = "none";
}
