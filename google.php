<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>
<meta name="google-signin-client_id" content="387268322087-pnkcj2h1noi2emj25m3n9i1goi6rb2ah.apps.googleusercontent.com">

<script>
function onLoad() {
  gapi.load('auth2', function() {
    gapi.auth2.init();
  });
}
function signOut() {
  var auth2 = gapi.auth2.getAuthInstance();
  auth2.signOut().then(function () {
    console.log('User signed out.');
  });
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'https://jakestockwin.co.uk/filmnight/logout.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function(){
    location.replace("https://jakestockwin.co.uk/filmnight/index.php");
  }
  xhr.send();
}
function onSignIn(googleUser) {
  var id_token = googleUser.getAuthResponse().id_token;


  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'https://jakestockwin.co.uk/filmnight/loginhandler.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    console.log('Signed in as: ' + xhr.responseText);
    location.replace("https://jakestockwin.co.uk/filmnight/index.php");
  };
  xhr.send('idtoken=' + id_token);

}

</script>
