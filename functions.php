<?php

function dbconnect(){
  // Ensures that there is a database connection in $GLOBALS['conn']
  if(!isset($GLOBALS['conn'])){
    include $GLOBALS['root'].'../../database.php';
    $conn = new mysqli($host, $username, $password, "films");
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $GLOBALS['conn'] = $conn;
  }
}

function dbdisconnect(){
  if(isset($GLOBALS['conn'])){
    $GLOBALS['conn']->close();
    unset($GLOBALS['conn']);
  }
}

function query($query){
  // Queries the films database with $query and returns the response
  dbconnect(); // Ensure a connection is started
  // In most cases a connection should already be initialised by the time
  // any queries are run. 

  return $GLOBALS['conn']->query($query);
}

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

function status(){

  $now = date('Y-m-d H:i:s');
  $sql = "SELECT * FROM timings";
  $result = query($sql);
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

function get_event($advance = 0, $tol = 300){
  $sql = "SELECT * FROM timings";
  $result = query($sql);
  $events = array();
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      if (strtotime($row["Roll_Call_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Roll_Call_Start"]) + $tol){
        array_push($events,"Roll_Call_Start");
      }
      if (strtotime($row["Roll_Call_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Roll_Call_End"]) + $tol){
        array_push($events,"Roll_Call_End");
      }
      if (strtotime($row["Voting_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Voting_Start"]) + $tol){
        array_push($events,"Voting_Start");
      }
      if (strtotime($row["Voting_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Voting_End"]) + $tol){
        array_push($events,"Voting_End");
      }
      if (strtotime($row["Results_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Results_Start"]) + $tol){
        array_push($events,"Results_Start");
      }
      if (strtotime($row["Results_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Results_End"]) + $tol){
        array_push($events,"Results_End");
      }
    }
  }
  return $events;
}

function get_emails($event){
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
  }else if($event == "Voting_End30"){
    // handled later
    $field = 0;
  }else if($event == "Voting_End60"){
    // handled later
    $field = 0;
  }else{
    return "Error: Unhandled event";
  }
  $to="";
  $sql = "SELECT Email FROM users WHERE Active=1 AND ".$field."=1;";
  $result = query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $to = $to.$row['Email'].", ";
    }
  }
  if($event == "Voting_End60"){
    $sql = "SELECT * FROM users WHERE Attending=1 AND Reminder_Voting60=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          $to = $to.$row['Email'].", ";
        }
      }
    }
  }
  if($event == "Voting_End30"){
    $sql = "SELECT * FROM users WHERE Attending=1 AND Reminder_Voting30=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          $to = $to.$row['Email'].", ";
        }

      }
    }
  }

  return $to;
}

function get_endpoints($event){
  include $GLOBALS['root'].'../../database.php';
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
  }else if($event == "Voting_End30"){
    // handled later
    $field = 0;
  }else if($event == "Voting_End60"){
    // handled later
    $field = 0;
  }else{
    return "Error: Unhandled event";
  }
  $endpoints = array();
  $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Active=1 AND ".$field."=1;";
  $result = query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
    }
  }
  if($event == "Voting_End60"){
    $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Attending=1 AND Reminder_Voting60=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
        }
      }
    }
  }
  if($event == "Voting_End30"){
    $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Attending=1 AND Reminder_Voting30=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
        }

      }
    }
  }
  return $endpoints;
}


  ?>
