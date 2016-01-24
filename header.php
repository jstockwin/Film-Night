<?php
if (strpos(gethostname(), 'rpi') !== FALSE){
  $root = '/filmnight/';
}else{
  $root = '/';
}

require_once $root.'google.php';
require_once $root.'functions.php';
include_once $root.'../override.php';


 ?>
