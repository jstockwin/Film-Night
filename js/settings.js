var subscriptionButton = document.getElementById('#registerWorker');
var subscriptionName = document.getElementById('#registerName');
var subscriptionCard = document.getElementById('#registerCard');
var table = document.getElementById('#endpointTable');
var endpoints = [];
var thisIndex = -1;

navigator.sayswho= (function(){
    var N= navigator.appName, ua= navigator.userAgent, tem;
    var M= ua.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
    if(M && (tem= ua.match(/version\/([\.\d]+)/i))!= null) M[2]= tem[1];
    M= M? [M[1], M[2]]: [N, navigator.appVersion,'-?'];
    return M;
})();

subscriptionName.value = navigator.sayswho[0] + ', ' + navigator.platform;

// As subscription object is needed in few places let's create a method which
// returns a promise.
function getSubscription() {
  return navigator.serviceWorker.ready.then(function(registration) {
    return registration.pushManager.getSubscription();
  }).catch(function(error) {
    console.log("Couldn't get subscription: ", error)
  });
}

function addTableRow(endpointData, isThisBrowser) {
  var name = endpointData.Name;
  var id = endpointData.id;
  var newRow = table.insertRow(isThisBrowser ? 1 : -1);
  var nameCell = newRow.insertCell(0);
  var unregisterCell = newRow.insertCell(1);
  nameCell.innerHTML = name + (isThisBrowser ? " (this browser)" : "");
  unregisterCell.innerHTML = '<button id="registerDelete' + id + '">Unsubscribe</button>';
  if(isThisBrowser) {
      unregisterCell.addEventListener('click', function(){unsubscribeThis()});
  } else {
      unregisterCell.addEventListener('click', function(ev){unsubscribe(ev, id)});
  }
}

// Register service worker and check the initial subscription state.
// Set the UI (button) according to the status.
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('service-worker.js').then(function() {
    console.log('service worker registered');
    return getSubscription();
  }).then(function(subscription) {
    return fetch('admin/infohandler.php?wants=endpoints', {credentials: 'same-origin'}).then(function(response) {
      return response.json();
    }).then(function(endpointList) {
      endpoints = endpointList;
      var localEndpoint = subscription ? subscription.endpoint : "No local subscription";
      console.log(localEndpoint);
      endpoints.forEach(function(endpointData){
        console.log(endpointData);
        var thisOne = false;
        if(endpointData.Endpoint == localEndpoint) {
          // This endpoint is already in our list. Remove subscribe button
          thisOne = true;
          hideSubscribeButton();
          thisIndex = endpointData.id;
        }
        addTableRow(endpointData, thisOne);
      });
      if(thisIndex < 0) {
        // Browser is subscribed with Google or Mozilla (else this wouldn't be running?), let's unsubscribe.
        console.log("Browser not in sql db, removing subscription from Google/Mozilla");
        if(subscription) {subscription.unsubscribe()};
        showSubscribeButton();
      }
    }).catch(function(error) {
      console.log("Something went wrong:", error);
    });
  })
} else {
  document.getElementById('#registerCard').style.display = 'none';
  document.getElementById('#endpointTable').style.display = 'none';
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
        thisIndex = data.id;
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
  }).then(function() {
    console.log('Unsubscribed', thisIndex);
    return doUnsubscribe(1, thisIndex);
  }).then(function() {
    showSubscribeButton();
    thisIndex = -1
  });
}

// Changed by Jake, this now only removes the details from our database
// It removes the entry corresponding to the given primary key, but only if
// the ID of that entry is the same as the session ID.
// No longer unsubscribe from google.
function unsubscribe(ev, key) {
    console.log('Unsubscribed', key);
    doUnsubscribe(ev.target.parentNode.parentNode.rowIndex, key);
}

function doUnsubscribe(row, key) {
    return fetch('admin/unsubscribehandler.php', {
      method: 'post',
      credentials: 'same-origin',
      headers: {
        'Content-type': 'application/x-www-form-urlencoded'
      },
      body: 'key=' + key
    }).then(function() {
      table.deleteRow(row);
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
