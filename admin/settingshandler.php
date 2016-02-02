<?php
ob_start();
include '../header.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.


// Create connection
$conn = new mysqli($host, $username, $password, "films");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  if(session_status()== PHP_SESSION_NONE){
    session_start();
  }
  $_SESSION['ERROR']="nominationhandler.php failed connect to sql database: ".$conn->connect_error;
}
$attending=0;
$voting=0;
$results=0;
if(isset($_POST['attending'])){
  $attending=1;
}
if(isset($_POST['results'])){
  $results=1;
}
if(isset($_POST['voting'])){
  $voting=1;
}
if($_POST['rollCall']=="Yes"){
  $rollCall=1;
}else{
  $rollCall=0;
}
if(session_status()== PHP_SESSION_NONE){
  session_start();
}
$sql = 'UPDATE users SET Email="'.$_POST['email'].'", Attending='.$rollCall.', Reminder_Attending='.$attending.', Reminder_Voting='.$voting.', Reminder_Results='.$results.' WHERE ID="'.$_SESSION['Email'].'";';
$result = $conn->query($sql);

if($result == 1){
  unset($_SESSION['Email']); //Unset email to force a proper login again.
  header('location: ../settings.php');
}else{
  echo "Something went wrong. Shout at Jake<br>";
  echo "SQL Call: ".$sql."<br>";
  echo "Result: ".$result."<br>";
  $_SESSION['ERROR'] = "Error: Something went updating settings. <br> SQL Call:<br>".$sql."<br>result<br>".$result;
}
?>
