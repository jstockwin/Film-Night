<?php require_once 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="styles.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<div id="header">
  <div id="page-tabs">
    <?php echo "<!-- ".$root2." ".$_SERVER['PHP_SELF']." -->";?>
    <div  class="tab"><a href="index.php" onclick="slideIndicator(event)">Index</a><?php if($_SERVER['PHP_SELF'] === $root2."index.php"){echo '<div id="indicator"></div>';} ?></div>
    <div  class="tab"><a href="voting.php" onclick="slideIndicator(event)">Voting</a> <?php if($_SERVER['PHP_SELF'] === $root2."voting.php"){echo '<div id="indicator"></div>';} ?></div>
    <div  class="tab"><a href="results.php" onclick="slideIndicator(event)">Results</a><?php if($_SERVER['PHP_SELF'] === $root2."results.php"){echo '<div id="indicator"></div>';} ?></div>
    <?php $permission = loginCheck($session); if($permission === FALSE) : ?>
      <?php session_start(); if(isset($_SESSION['Email'])) : ?>
        <img src="/error.svg" id="profile-image" alt="You do not have sufficient permission to view this page.">
      <?php else: ?>
        <div class="g-signin2" data-onsuccess="onSignIn"></div>
      }
    <?php endif ?>
    <?php else: ?>
      <div>
        <label for="profile-toggle">
      <?php echo '<img src="'.$_SESSION['Image'].'" id="profile-image" alt="Signed in as '.$_SESSION['Email'].'">' ?>
    </lable>
      <input type="checkbox" id="profile-toggle">
      <div id="profile-dropdown">
        <h3 id="name"><?php echo $_SESSION['Name']?></h3>
        <h4 id="email"><?php echo $_SESSION['Email']?></h4>
        <button type="button" style="float: right" onclick="signOut">Sign Out</button>
      </div>
    </div>
    <?php endif ?>
  </div>
</div>
<div id="svg-container">
  <svg
  id="icon"
  xmlns="http://www.w3.org/2000/svg"
  fill="#fff"
  viewBox="0 0 24 24"
  style="overflow: visible">
  <path
  d="M 2.005,8 2,18 c 0,1.1 0.9,2 2,2 l 16,0 c 1.1,0 2,-0.9 2,-2 L 22,8 z"
  id="bottom" />
  <path
  d="M0 0h24v24H0z"
  fill="none"
  id="border" />
  <path
  id="top"
  d="M 17.999,4 19.9995,8 16.99875,8 14.99825,4 12.997749,4 14.99825,8 11.997499,8 9.9969992,4 7.9964991,4 9.9969992,8 6.9962491,8 4.9957489,4 3.9954989,4 C 2.8952238,4 2.0050013,4.9 2.0050013,6 L 2,8 22,8 22,4 z" />
</svg>
</div>
<script>
function slideIndicator(event){
  event.preventDefault();
  event.stopPropagation();

  var indicator  = document.getElementById('indicator');
  var targetBBox = event.target.getBoundingClientRect();
  var currentBBox = indicator.getBoundingClientRect();
  var deltaLeft = targetBBox.left - currentBBox.left;
  if(deltaLeft < 0 ){
    indicator.style.transformOrigin = "left center";
  }else{
    indicator.style.transformOrigin = "right center";
  }
  indicator.style.transition = "all 0.5s linear";
  indicator.style.transform = "translateX("+ deltaLeft / 2+"px) scale("+ (currentBBox.width + Math.abs(deltaLeft) / 4) /currentBBox.width +  ",1)";
  var secondStep = function(){
    indicator.style.transform = "translateX("+deltaLeft+"px) scale("+targetBBox.width / currentBBox.width +  ",1)";
    indicator.style.transition = "all 0.5s ease-out";
  }
  setTimeout(secondStep, 500);
  setTimeout(changePage,1000,event.target.href);
  return false;
}

function changePage(href){
  var xhr = new XMLHttpRequest();
  xhr.open('GET',href);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function(){
    document.open();
    document.write(xhr.responseText);
    document.close();
    window.history.pushState({html: xhr.responseText}, "", href);
  }
  xhr.send();
}

window.onpopstate = function(e){
    if(e.state){
      document.open();
      document.write(e.state.html);
      document.close();
    }
};
</script>
