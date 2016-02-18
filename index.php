<?php
include 'setup.php';
if(status($root)=="roll_call"){
  header("location: settings.php");
}else if(status($root)=="voting"){
  header("location: voting.php");
}else{
  header("location: results.php");
}


?>
