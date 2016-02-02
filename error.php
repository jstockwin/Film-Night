<?php
if(session_status()== PHP_SESSION_NONE){
  session_start();
}
  if(isset($_SESSION['ERROR'])){
    $error = $_SESSION['ERROR'];
    $_SESSION['ERROR'] = "";
  }
echo "An error has occured. Please notify Jake/Tom of the error message below:<br>";
if(isset($error)){
  echo $error;
}else{
  echo "Unknown reason";
}


 ?>
