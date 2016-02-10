<?php
ob_start();
include '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.


// Create connection
$conn = new mysqli($host, $username, $password, "films");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
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
if(session_status()== PHP_SESSION_NONE){
  session_start();
}
$sql = 'INSERT INTO users VALUES ("'.$_SESSION['Email'].'", "'.$_POST['name'].'","'.$_POST['email'].'","member",1,1,'.$attending.','.$voting.','.$results.',NULL);';
$result = $conn->query($sql);

if($result == 1){
  unset($_SESSION['Email']); //Unset email to force a proper login again.
  header('location: ../registration.php');
}else{
  echo "Something went wrong. Shout at Jake<br>";
  echo "SQL Call: ".$sql."<br>";
  echo "Result: ".$result."<br>";
}
?>
