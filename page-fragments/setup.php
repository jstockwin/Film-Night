<?php
if (strpos(gethostname(), 'rpi') !== FALSE){
  $root = $_SERVER['DOCUMENT_ROOT'].'/filmnight/';
  $root2 = '/filmnight/';
  $session = "live";
}else{
  $root = $_SERVER['DOCUMENT_ROOT'].'/';
  $root2 = '/';
  $session = "dev";
}
require_once $root.'functions.php';
sessionStart();
?>
