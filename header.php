<?php
if (strpos(gethostname(), 'rpi') !== FALSE){
  $root = $_SERVER['DOCUMENT_ROOT'].'/filmnight/';
}else{
  $root = $_SERVER['DOCUMENT_ROOT'].'/';
}

require_once $root.'google.php';
require_once $root.'functions.php';
$session = "live";
include $root.'../override.php';
 ?>
