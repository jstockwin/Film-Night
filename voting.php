<?php include 'setup.php';?>
<?php require $root.'../../database.php'; ?>
<?php $permission = loginCheck($session); ?>
<?php include 'head.php'; head('Film Night Voting');?>
<body>
<?php include 'top-nav.php' ?>
<div id="container">
<?php if(($permission != FALSE && status() =="voting") || $permission == "admin"): ?>
<div id="background" style="width:94%;background:#d5d5d5;padding:3%;">
  <div id="cards"></div>
  <div style="margin:auto;width:50%;display:flex;justify-content:space-around">
    <button type="button" id="submit" style="flex-basis:40%" >Submit Vote</button>
    <button type="button" id="withdraw" style="flex-basis:40%" >Withdraw Vote</button>
  </div>
</div>
<script src="js/voting.js"></script>
<?php elseif ($permission != FALSE): ?>
<div><p>No votes are taking place</p></div>
<?php endif; ?>
</div>
</body>
</html>
