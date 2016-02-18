var subscriptionButton = document.getElementById('#registerWorker');
var subscriptionName = document.getElementById('#registerName');
var subscriptionCard = document.getElementById('#registerCard');
var table = document.getElementById('#endpointTable');
var endpoints = [];
var thisIndex = -1;

// As subscription object is needed in few places let's create a method which
// returns a promise.
function getSubscription() {
  return navigator.serviceWorker.ready
    .then(function(registration) {
      return registration.pushManager.getSubscription();
    });
}

function addTableRow(endpointData, isThisBrowser) {
  name = endpointData.Name;
  id = endpointData.Identifier;
  newRow = table.insertRow(isThisBrowser ? 1 : -1);
  nameCell = newRow.insertCell(0);
  unregisterCell = newRow.insertCell(1);
  nameCell.innerHTML = name + (isThisBrowser ? " (this browser)" : "");
  unregisterCell.innerHTML = '<button id="registerDelete' + id + '">Unsubscribe</button>';
  if(isThisBrowser) {
      unregisterCell.addEventListener('click', function(){unsubscribeThis()});
  } else {
      unregisterCell.addEventListener('click', function(){unsubscribe(id)});
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
    fetch('admin/infohandler.php?wants=endpoints', {credentials: 'same-origin'}).then(function(response) {
      return response.json();
    }).then(function(endpointList) {
      endpoints = endpointList;
      localEndpoint = subscription ? subscription.endpoint : "No local subscription";
      console.log(endpoints);
      console.log(localEndpoint);
      endpoints.forEach(function(endpointData){
        console.log(endpointData);
        thisOne = false;
        if(endpointData.Endpoint == localEndpoint) {
          // This endpoint is already in our list. Remove subscribe button
          thisOne = true;
          hideSubscribeButton();
          thisIndex = endpointData.Identifier;
        }
        addTableRow(endpointData, thisOne);
      });
      if(thisIndex < 0) {
        // Browser is subscribed with Google or Mozilla (else this wouldn't be running?), let's unsubscribe.
        console.log("Browser not in sql db, removing subscription from Google/Mozilla");
        if(subscription) {subscription.unsubscribe()};
        showSubscribeButton();
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
      body: 'endpoint=' + subscription.endpoint + '&name=' + subscriptionName.value
    }).then(function(response) {
      return response.json();
    }).then(function(data) {
      console.log(data);
      if(data.hasOwnProperty('error')) {
        location.reload();
      } else {
        hideSubscribeButton();
        addTableRow(data, true);
      }
    });
  });
}

// Get existing subscription from service worker, unsubscribe
// (`subscription.unsubscribe()`) and unregister it in the server with
// a POST request to stop sending push messages to
// unexisting endpoint.
function unsubscribeThis() {
  getSubscription().then(function(subscription) {
    return subscription.unsubscribe()
      .then(function() {
        if(table.rows[1].cells[1].firstChild.id == "registerDelete"+thisIndex)
        {table.deleteRow(1);}
        console.log('Unsubscribed', subscription.endpoint);
        return fetch('admin/unsubscribehandler.php', {
          method: 'post',
          credentials: 'same-origin',
          headers: {
            'Content-type': 'application/x-www-form-urlencoded'
          },
          body: 'key=' + thisIndex
        });
      });
  }).then(showSubscribeButton);
}

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
function showSubscribeButton() {
  subscriptionButton.onclick = subscribe;
  subscriptionButton.textContent = 'Click to Subscribe';
  subscriptionButton.removeAttribute('disabled');
  subscriptionCard.style.display = "inline-block";
}

function hideSubscribeButton() {
  // Changed so that this now removes the button entirely.
  subscriptionButton.onclick = "";
  subscriptionButton.textContent = 'Button disabled';
  subscriptionButton.disabled = true;
  subscriptionCard.style.display = "none";
}
