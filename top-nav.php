<?php require_once 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="styles.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<div id="header">
  <div id="tabs-wrapper">
  <div id="page-tabs">
    <?php echo "<!-- ".$root2." ".$_SERVER['PHP_SELF']." -->";?>
    <div id="indicator"></div>
    <a href="index.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."index.php"){echo 'data-active="true"';} ?>>Index</a>
    <a href="nominate.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."nominate.php"){echo 'data-active="true"';} ?>>Add Films</a>
    <a href="voting.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."voting.php"){echo 'data-active="true"';} ?>>Voting</a>
    <a href="results.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."results.php"){echo 'data-active="true"';} ?>>Results</a>
    <a href="settings.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."settings.php"){echo 'data-active="true"';} ?>>Settings</a>
    <?php $permission = loginCheck($session); if ($permission == "admin"){
      echo '<a href="admin-console.php" onClick="slideIndicator(event)" class="tab" ';
      if($_SERVER['PHP_SELF'] === $root2."admin-console.php"){echo 'data-active="true"';}
      echo ">Admin</a>";
    }
    if($permission === FALSE) : ?>
      <?php if(isset($_SESSION['Email'])) : ?>
        <div>
        <label for="profile-toggle">
            <img src="/error.svg" id="profile-image" alt="You are not a registered user.">
        </label>
        <input type="checkbox" id="profile-toggle">
        <div id="profile-dropdown">
          <h3 id="name"><?php echo $_SESSION['Name']?></h3>
          <h4 id="email"><?php echo $_SESSION['Email']?></h4>
          <button type="button" style="float: right" onclick="signOut()">Sign Out</button>
        </div>
      </div>
      <?php else: ?>
        <div class="g-signin2" data-onsuccess="onSignIn"></div>
    <?php endif ?>
    <?php else: ?>
      <div>
        <label for="profile-toggle">
      <?php echo '<img src="'.$_SESSION['Image'].'" id="profile-image" alt="Signed in as '.$_SESSION['Email'].'">' ?>
        </label>
      <input type="checkbox" id="profile-toggle">
      <div id="profile-dropdown">
        <h3 id="name"><?php echo $_SESSION['Name']?></h3>
        <h4 id="email"><?php echo $_SESSION['Email']?></h4>
        <button type="button" style="float: right" onclick="signOut()">Sign Out</button>
      </div>
    </div>
    <?php endif ?>
  </div>
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

window.addEventListener("DOMContentLoaded", setActive(findActiveTab()));
window.addEventListener("resize", function(){setActive(findActiveTab())}, true);;

function closeClapper(){
  document.getElementById('top').style.transform = "rotate(0deg)";
}

function showContent(){
  setTimeout(closeClapper, 300);
  setTimeout(shrinkHeader, 800);
}

function shrinkHeader(){
  document.getElementById('svg-container').style.left = "0";
  document.getElementById('svg-container').style.top = "0";
  document.getElementById('svg-container').style.transform = "scale(0.233333)";
  document.getElementById('header').style.height = "70px";
}

function openClapper(){
  document.getElementById('top').style.transform = "rotate(-45deg)";
}

function expandHeader(){
  document.getElementById('svg-container').style.left = "50%";
  document.getElementById('svg-container').style.top = "50%";
  document.getElementById('svg-container').style.transform = "translate(-50%, -50%)";
  document.getElementById('header').style.height = "100%";
}

function findActiveTab(){
  var pageTabs = document.getElementById('page-tabs');
  for(var i = 0; i < pageTabs.children.length; i++){
    if(pageTabs.children[i].getAttribute('data-active')){
      return pageTabs.children[i];
    }
  }
}

function setActive(target){

  var indicator  = document.getElementById('indicator');
  var targetBBox = target.getBoundingClientRect();
  indicator.style.left = targetBBox.left + "px";
  indicator.style.right = document.getElementById('header').getBoundingClientRect().width - targetBBox.right + "px";
}

function slideIndicator(event){
  event.preventDefault();
  event.stopPropagation();
  expandHeader();
  var indicator  = document.getElementById('indicator');
  var targetBBox = event.target.getBoundingClientRect();
  var currentBBox = indicator.getBoundingClientRect();
  var deltaLeft = targetBBox.left - currentBBox.left;
  if(deltaLeft < 0){
    indicator.style.transition = "left 0.2s, right 0.4s";
  }else{
    indicator.style.transition = "right 0.2s, left 0.4s";
  }
  indicator.style.left = targetBBox.left + "px";
  indicator.style.right = document.getElementById('header').getBoundingClientRect().width - targetBBox.right + "px";
  setTimeout(openClapper, 2000);
  setTimeout(changePage,2500,event.target.href);
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
