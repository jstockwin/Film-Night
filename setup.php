<?php
if (strpos(gethostname(), 'ubuntu-1gb-lon1-01') !== FALSE){
  $GLOBALS['root'] = $_SERVER['DOCUMENT_ROOT'].'/';
  $GLOBALS['root2'] = '/';
  $session = "live";
}else{
  $GLOBALS['root'] = $_SERVER['DOCUMENT_ROOT'].'/';
  $GLOBALS['root2'] = '/';
  $session = "dev";
}
require_once $GLOBALS['root'].'functions.php';
sessionStart();
?>
