<?php
ob_start();
include '../setup.php';
require $GLOBALS['root'].'../../database.php';
ob_end_clean(); // supresses output.


// Create connection
$conn = new mysqli($host, $username, $password, "films");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  $_SESSION['ERROR']="settingshandler.php failed connect to sql database: ".$conn->connect_error;
}
$attending=0;
$voting=0;
$results=0;
$voting30=0;
$voting60=0;
$attendingNotification=0;
$votingNotification=0;
$resultsNotification=0;
$voting30Notification=0;
$voting60Notification=0;
if(isset($_POST['attending'])){
  $attending=1;
}
if(isset($_POST['results'])){
  $results=1;
}
if(isset($_POST['voting'])){
  $voting=1;
}
if(isset($_POST['voting30'])){
  $voting30=1;
}
if(isset($_POST['voting60'])){
  $voting60=1;
}
if(isset($_POST['attendingNotification'])){
  $attendingNotification=1;
}
if(isset($_POST['resultsNotification'])){
  $resultsNotification=1;
}
if(isset($_POST['votingNotification'])){
  $votingNotification=1;
}
if(isset($_POST['voting30Notification'])){
  $voting30Notification=1;
}
if(isset($_POST['voting60Notification'])){
  $voting60Notification=1;
}



$rollCall = 1;
if(isset($_POST['rollCall']) && $_POST['rollCall']!="yes"){
  $rollCall=0;
}
$sql = 'UPDATE users SET Email="'.$_POST['email'].'", Attending='.$rollCall.', Reminder_Attending='.$attending.',
Reminder_Voting='.$voting.', Reminder_Results='.$results.'
, Reminder_Voting30='.$voting30.', Reminder_Voting60='.$voting60.', Notification_Attending='.$attendingNotification.',
 Notification_Voting='.$votingNotification.', Notification_Results='.$resultsNotification.'
, Notification_Voting30='.$voting30Notification.', Notification_Voting60='.$voting60Notification.'
 WHERE id="'.$_SESSION['ID'].'";';
$result = $conn->query($sql);
echo $result;
echo $sql;
if($result == 1){
  header('location: ../settings.php');
}else{
  echo "Something went wrong. Shout at Jake<br>";
  echo "SQL Call: ".$sql."<br>";
  echo "Result: ".$result."<br>";
  $_SESSION['ERROR'] = "Error: Something went updating settings. <br> SQL Call:<br>".$sql."<br>result<br>".$result;
}
?>
