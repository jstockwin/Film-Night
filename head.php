<?php
function head($title, $extra='') {
  global $root;
  echo '<!DOCTYPE html>';
  echo '<html>';
  echo '<head>';
  echo '<title>'.$title.'</title>';
  echo '<link rel="stylesheet" type="text/css" href="styles.css">';
  echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">';
  echo '<link rel="manifest" href="/manifest.json">';
  echo '<link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg" sizes="any">';
  echo '<link rel="icon" type="image/png" href="assets/icons/favicon.png">';
  echo '<meta name="theme-color" content="#607D8B">';
  require_once $root.'google.php';
  if($extra != '') {
    echo $extra;
  }
  echo '</head>';
}
?>
