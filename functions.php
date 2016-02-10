<?php
function loginCheck($session = "live", $session_started = FALSE) {
  if(session_status() == PHP_SESSION_NONE){
    session_start();
  }
  if($session == "dev"){
    $_SESSION['Email'] = "debug@example.com";
    $_SESSION['Permission'] = "admin";
    $_SESSION['Name'] = "debug";
    $_SESSION['Token'] = "ABC123";
    $_SESSION['Image'] = "assets/icons/ic_error.svg";
    return "admin";
  }elseif($session == "dev2"){
    $_SESSION['Email'] = "debug@example2.com";
    return "member";
  }else{
    if (isset($_SESSION['Permission'])){
      return $_SESSION['Permission'];
    }else{
      return FALSE;
    }
  }
}

function status($root){
  include $root.'../../database.php';
  $conn = new mysqli($host, $username, $password, "films");

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $now = date('Y-m-d H:i:s');

  $sql = "SELECT * FROM timings";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      if ($row["Roll_Call_Start"] < $now && $now < $row["Roll_Call_End"]){
        return "rollCall";
      }else if($row["Voting_Start"] < $now && $now < $row["Voting_End"]){
        return "voting";
      }else if($row["Results_Start"] < $now && $now < $row["Results_End"]){
        return "results";
      }
    }
  }
  return FALSE;
}

function get_event($root){
  include $root.'../../database.php';
  $conn = new mysqli($host, $username, $password, "films");
  $sql = "SELECT * FROM timings";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      if (strtotime($row["Roll_Call_Start"]) - 300 < time() && time() < strtotime($row["Roll_Call_Start"]) + 300){
        return "Roll_Call_Start";
      }else if (strtotime($row["Roll_Call_End"]) - 300 < time() && time() < strtotime($row["Roll_Call_End"]) + 300){
        return "Roll_Call_End";
      }else if (strtotime($row["Voting_Start"]) - 300 < time() && time() < strtotime($row["Voting_Start"]) + 300){
        return "Voting_Start";
      }else if (strtotime($row["Voting_End"]) - 300 < time() && time() < strtotime($row["Voting_End"]) + 300){
        return "Voting_End";
      }else if (strtotime($row["Results_Start"]) - 300 < time() && time() < strtotime($row["Results_Start"]) + 300){
        return "Results_Start";
      }else if (strtotime($row["Results_End"]) - 300 < time() && time() < strtotime($row["Results_End"]) + 300){
        return "Results_End";
      }
    }
  }else{
    return "Error";

  }
  return "No event";
}


  ?>
