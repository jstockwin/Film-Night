<?php
include '../header.php';
require $root.'../../database.php';



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
session_start();
$sql = 'INSERT INTO users VALUES ("'.$_SESSION['Email'].'", "'.$_POST['name'].'","'.$_POST['email'].'","member",1,1,'.$attending.','.$voting.','.$results.');';
$result = $conn->query($sql);

if($result == 1){
  echo "You have successfully registered";
}else{
  echo "Something went wrong. Shout at Jake<br>";
  echo "SQL Call: ".$sql."<br>";
  echo "Result: ".$result."<br>";
}
?>
