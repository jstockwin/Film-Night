<?php
if (strpos(gethostname(), 'rpi') !== FALSE){
  $root = $_SERVER['DOCUMENT_ROOT'].'/filmnight/';
  $root2 = '/filmnight/';
}else{
  $root = $_SERVER['DOCUMENT_ROOT'].'/';
  $root2 = '/';
}

require_once $root.'google.php';
require_once $root.'snap.php';
require_once $root.'functions.php';
$session = "live";
include $root.'../override.php';
 ?>
