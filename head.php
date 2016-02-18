<?php
function head($title, $extra='') {
  global $root;
  echo '<!DOCTYPE html>';
  echo '<html>';
  echo '<head>';
  echo '<title>'.$title.'</title>';
  echo '<link rel="stylesheet" type="text/css" href="styles.css">';
  echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">';
  echo '<link rel="manifest" href="./manifest.json">';
  echo '<link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg" sizes="any">';
  echo '<link rel="icon" type="image/png" href="assets/icons/favicon.png">';
  echo '<meta name="theme-color" content="#455A64">';
  echo '<script type="text/javascript" async src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML"></script>';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
  require_once $root.'google.php';
  if($extra != '') {
    echo $extra;
  }
  echo '</head>';
}
?>
