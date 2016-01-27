<?php
  require '../../database.php';
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  session_start();
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
    $sql = "SELECT * FROM users WHERE ID='".$email."';";
    echo "sql: ".$sql;
    $result = $conn->query($sql);

    if (!$result){
      // something went wrong
    }elseif($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        if($row["Active"]==1){
          // Active user returned
          $_SESSION["Email"] = $row["Email"];
          $_SESSION["Permission"] = $row["Permission"];
          $_SESSION["Name"] = $row["Name"];
          $_SESSION["Token"] = $token;
          $_SESSION["Image"] = $image;
        }else{
          // Inactive user returned
        }
      }
    }else{

      // User authenticated with Google, but is not in our list of users.
      $_SESSION["Email"] = $email;

    }
    $conn->close();
	}else{
    // Error: Bad aud.
  }


?>
