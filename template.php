<?php include 'setup.php';?>
<?php require $root.'../../database.php'; ?>
<?php $permission = loginCheck($session); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php $title ?></title>
  <link rel="stylesheet" type="text/css" href="styles.css">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
  <link rel="manifest" href="./manifest.json">
  <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg" sizes="any">
  <link rel="icon" type="image/png" href="assets/icons/favicon.png">
  <meta name="theme-color" content="#455A64">
  <script type="text/javascript" async src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML"></script>
  <script type="text/javascript" src="js/voting-systems.js"></script>
  <script type="text/javascript" src="js/results-graphs.js"></script>
  <script type="text/javascript" src="js/nominate.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once $root.'google.php' ?>
  <?php if($extra != '') {echo $head;} ?>
</head>
<body>
  <?php include 'top-nav.php'; ?>
  <div id="container">
    <?php include $root.$fragment ?>
  </div>
</body>
</html>
