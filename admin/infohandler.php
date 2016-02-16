<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="subscribehandler.php failed to verify that you are signed in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    $_SESSION['ERROR']="subscribehandler.php failed connect to sql database: ".$conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_GET['wants'])){

    $wants=$_GET['wants'];
    if($wants == 'endpoints') {
      $sql = 'SELECT * FROM endpoints WHERE ID="'.$_SESSION['ID'].'";';
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $endpoints = array();
        while($row = $result->fetch_assoc()){
          array_push($endpoints, $row);
        }
        echo json_encode($endpoints);
      }else{
        echo "[]";
      }
    } else if($wants == 'notification') {
      switch(get_event($root)) {
        case "Roll_Call_Start":
          echo '{"title": "Film Night", "body": "Coming to film night this week? Roll Call!", "url": "settings.php"}';
          break;
        case "Voting_Start":
          echo '{"title": "Film Night", "body": "Voting is open.", "url": "voting.php"}';
          break;
        case "Results_Start":
          echo '{"title": "Film Night", "body": "The time is now. Results are available.", "url": "results.php"}';
          break;
        default:
          echo '{"title": "Film Night", "body": "You have a notification but nothing is happening.", "url": "index.php"}';
          break;
      }
    }
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }
}
?>
