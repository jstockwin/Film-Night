<?php

function loginCheck($session = "live") {
  session_start();
  if($session == "dev"){
    $_SESSION['Email'] = "debug@example.com";
    $_SESSION['Permission'] = "admin";
    $_SESSION['Name'] = "debug";
    $_SESSION['Token'] = "ABC123";
    return "admin";
  }else{
    if (isset($_SESSION['Permission'])){
      return $_SESSION['Permission'];
    }else{
      return FALSE;
    }
  }
}

?>
