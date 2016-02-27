<?php
include 'setup.php';
if(status()=="roll_call"){
  header("location: settings.php");
}else if(status()=="voting"){
  header("location: voting.php");
}else{
  header("location: results.php");
}


?>
