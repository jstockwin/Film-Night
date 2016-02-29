<?php
require 'setup.php';
$token = $_POST["idtoken"];
$link = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=".$token;
$response = file_get_contents($link);
$var = json_decode($response);
$email = $var->{'email'};
$image = $var->{'picture'};
echo "Email: ".$email;
$aud = $var->{'aud'};
if ($aud == "387268322087-pnkcj2h1noi2emj25m3n9i1goi6rb2ah.apps.googleusercontent.com"){
  // Continue

  $details = getUserDetails($email);
  if($details["Active"]==1){
    // Active user returned
    $_SESSION["ID"] = $details["ID"];
    $_SESSION["Permission"] = $details["Permission"];
    $_SESSION["Name"] = $details["Name"];
    $_SESSION["Token"] = $token;
    $_SESSION["Image"] = $image;
  }else{
    // Inactive user returned
  }
}else{
  // User authenticated with Google, but is not in our list of users.
  $_SESSION["ID"] = $email;
}
}else{
  // Error: Bad aud.
}

?>
