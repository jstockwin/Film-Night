<?php if(session_status() == PHP_SESSION_NONE){
  session_start();
}
if(isset($_SESSION['ERROR']) && !$_SESSION['ERROR']==""){header("location: error.php");}?>
<div id="header">
  <div id="tabs-wrapper">
  <div id="page-tabs">
    <?php echo "<!-- ".$root2." ".$_SERVER['PHP_SELF']." -->";?>
    <div id="indicator"></div>
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
      <div class="g-signin2" data-onsuccess="onSignIn"></div>
    <?php else: ?>
      <div>
        <label for="profile-toggle">
      <?php echo '<img src="'.$_SESSION['Image'].'" id="profile-image" alt="Signed in as '.$_SESSION['ID'].'">' ?>
        </label>
      <input type="checkbox" id="profile-toggle">
      <div id="profile-dropdown">
        <h3 id="name"><?php echo $_SESSION['Name']?></h3>
        <h4 id="email"><?php echo $_SESSION['ID']?></h4>
        <button type="button" style="float: right" onclick="signOut()">Sign Out</button>
      </div>
    </div>
    <?php endif ?>
  </div>
  </div>
</div>
<div id="svg-container" class="svg-container">
  <div id="top">
    <svg  xmlns="http://www.w3.org/2000/svg"  fill="#fff"  viewBox="2 4 20 4"  style="overflow: visible">
    <path  d="M 18,4 20,8 17,8 15,4 13 4 15,8 12,8 10,4 8,4 10,8 7,8 5,4 4,4 C 3,4 2,4.9 2,6 L 2,8 22,8 22,4 z" />
  </div>
  <div  id="bottom" >
  <svg    xmlns="http://www.w3.org/2000/svg"  fill="#fff"  viewBox="2 8 20 12"  style="overflow: visible">
  <path d="M 2,8 2,18 c 0,1.1 0.9,2 2,2 l 16,0 c 1.1,0 2,-0.9 2,-2 L 22,8 z"/>
</div>
</svg>
</div>
<script>

window.addEventListener("DOMContentLoaded", function(){setActive(findActiveTab())});
window.addEventListener("load", function(){setActive(findActiveTab())});
window.addEventListener("resize", function(){setActive(findActiveTab())}, true);;

function closeClapper(){
  document.getElementById('top').style.transform = "rotate(0deg)";
}

function showContent(){
  setTimeout(closeClapper, 300);
  setTimeout(shrinkHeader, 800);
}

function shrinkHeader(){
  document.getElementById('svg-container').style.transition = "transform 2s,left 2s, top 2s";
  document.getElementById('svg-container').classList.add('small-svg-container');
  document.getElementById('header').style.height = "70px";
}

function openClapper(){
  document.getElementById('top').style.transform = "rotate(-45deg)";
}

function expandHeader(){
  document.getElementById('svg-container').classList.remove('small-svg-container');
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
  if(event.target.dataset['active'] != "true"){
    expandHeader();
    setTimeout(openClapper, 2000);
    setTimeout(changePage, 2500, event.target.href);
  }
  return false;
}

function changePage(href){
  var indicator  = document.getElementById('indicator');
  var left = indicator.style.left;
  var right = indicator.style.right;
  var xhr = new XMLHttpRequest();
  xhr.open('GET',href);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function(){
    document.open();
    document.write(xhr.responseText);
    document.close();
    window.history.pushState({html: xhr.responseText}, "", href);
    indicator = document.getElementById('indicator');
    indicator.style.left = left;
    indicator.style.right = right;

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
