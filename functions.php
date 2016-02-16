<?php
function loginCheck($session = "live", $session_started = FALSE) {
  if(session_status() == PHP_SESSION_NONE){
    session_start();
  }
  if($session == "dev"){
    $_SESSION['ID'] = "debug@example.com";
    $_SESSION['Permission'] = "admin";
    $_SESSION['Name'] = "debug";
    $_SESSION['Token'] = "ABC123";
    $_SESSION['Image'] = "assets/icons/ic_error.svg";
    return "admin";
  }elseif($session == "dev2"){
    $_SESSION['ID'] = "debug@example2.com";
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
  if($result && $result->num_rows > 0){
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

function get_event($root, $advance = 0, $tol = 300){
  include $root.'../../database.php';
  $conn = new mysqli($host, $username, $password, "films");
  $sql = "SELECT * FROM timings";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      if (strtotime($row["Roll_Call_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Roll_Call_Start"]) + $tol){
        return "Roll_Call_Start";
      }else if (strtotime($row["Roll_Call_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Roll_Call_End"]) + $tol){
        return "Roll_Call_End";
      }else if (strtotime($row["Voting_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Voting_Start"]) + $tol){
        return "Voting_Start";
      }else if (strtotime($row["Voting_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Voting_End"]) + $tol){
        return "Voting_End";
      }else if (strtotime($row["Results_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Results_Start"]) + $tol){
        return "Results_Start";
      }else if (strtotime($row["Results_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Results_End"]) + $tol){
        return "Results_End";
      }
    }
  }else{
    return "Error";

  }
  return "No event";
}

function get_emails($root, $event){
  include $root.'../../database.php';
  if($event == "All"){
    $field = 1;
  }else if($event == "Roll_Call_Start"){
    $field = "Reminder_Attending";
  }else if($event == "Voting_Start"){
    $field = "Reminder_Voting";
  }else if($event == "Results_Start"){
    $field = "Reminder_Results";
  }else if($event == "No event"){
    return "Error: Nothing is happening";
  }else{
    return "Error: Unhandled event";
  }
  $to="";
  $conn = new mysqli($host, $username, $password, "films");
  $sql = "SELECT Email FROM users WHERE Active=1 AND ".$field."=1;";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $to = $to.$row['Email'].", ";
    }
  }
  return $to;
}

function get_endpoints($root, $event){
  include $root.'../../database.php';
  if($event == "All"){
    $field = 1;
  }else if($event == "Roll_Call_Start"){
    $field = "Reminder_Attending";
  }else if($event == "Voting_Start"){
    $field = "Reminder_Voting";
  }else if($event == "Results_Start"){
    $field = "Reminder_Results";
  }else if($event == "No event"){
    return "Error: Nothing is happening";
  }else{
    return "Error: Unhandled event";
  }
  $endpoints = array();
  $conn = new mysqli($host, $username, $password, "films");
  $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Active=1 AND ".$field."=1;";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
    }
  }
  return $endpoints;
}


  ?>
