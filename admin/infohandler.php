<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

$conn = new mysqli($host, $username, $password, "films");
if ($conn->connect_error) {
  $_SESSION['ERROR']="subscribehandler.php failed connect to sql database: ".$conn->connect_error;
  die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['wants'])){
  $wants=$_GET['wants'];
  if($wants == 'endpoints') {
    $endpoints = array();
    if(loginCheck($session)){
      $sql = 'SELECT * FROM endpoints WHERE ID="'.$_SESSION['ID'].'";';
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
          array_push($endpoints, $row);
        }
      }
    }
    echo json_encode($endpoints);
  } else if($wants == 'notification') {
    if(in_array("Voting_End", get_event($root, 1800))){
      echo '{"title": "Film Night", "body": "Voting closes in half an hour. You still haven\'t voted.", "url": "voting.php"}';
    }else if(in_array("Voting_End", get_event($root, 3600))){
      echo '{"title": "Film Night", "body": "Voting closes in an hour. You still haven\'t voted.", "url": "voting.php"}';
    }else{
      $now_events = get_event($root);
      if(in_array("Roll_Call_Start", $now_events)) {
          echo '{"title": "Film Night", "body": "Coming to film night this week? Roll Call!", "url": "settings.php"}';
      } else if(in_array("Voting_Start", $now_events)) {
          echo '{"title": "Film Night", "body": "Voting is open.", "url": "voting.php"}';
      } else if(in_array("Results_Start", $now_events)) {
          echo '{"title": "Film Night", "body": "The time is now. Results are available.", "url": "results.php"}';
      } else {
          echo '{"title": "Film Night", "body": "You have a notification but nothing is happening.", "url": "index.php"}';
      }
    }
  }
} else {
  echo '{"error": "Nothing Received"}';
}
?>
