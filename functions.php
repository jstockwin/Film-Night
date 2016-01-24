<?php

  function loginCheck() {
    session_start();
    if (isset($_SESSION['Permission'])){
    	return $_SESSION['Permission'];
    }else{
    	return FALSE;
    }
  }

 ?>
