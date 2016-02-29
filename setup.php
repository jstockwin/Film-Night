<?php
if (strpos(gethostname(), 'rpi') !== FALSE){
  $GLOBALS['root'] = $_SERVER['DOCUMENT_ROOT'].'/filmnight/';
  $GLOBALS['root2'] = '/filmnight/';
  $session = "live";
}else{
  $GLOBALS['root'] = $_SERVER['DOCUMENT_ROOT'].'/';
  $GLOBALS['root2'] = '/';
  $session = "dev";
}
require_once $GLOBALS['root'].'functions.php';
sessionStart();
?>
