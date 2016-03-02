<?php if(session_status() == PHP_SESSION_NONE){
  session_start();
}
if(isset($_SESSION['ERROR']) && !$_SESSION['ERROR']==""){header("location: error.php");}?>
<div id="header">
  <div id="tabs-wrapper">
  <div id="page-tabs">
    <?php echo "<!-- ".$root2." ".$_SERVER['PHP_SELF']." -->";?>
    <div id="indicator"></div>
    <a id="nominateTab" href="nominate.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."nominate.php"){echo 'data-active="true"';} ?>>Add Films</a>
    <a id="votingTab" href="voting.php" onclick="moveIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."voting.php"){echo 'data-active="true"';} ?>>Voting</a>
    <a id="resultsTab" href="results.php" onclick="slideIndicator(event)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."results.php"){echo 'data-active="true"';} ?>>Results</a>
    <a id="settingsTab" href="settings.php" onclick="moveIndicator(this)" class="tab" <?php if($_SERVER['PHP_SELF'] === $root2."settings.php"){echo 'data-active="true"';} ?>>Settings</a>
    <?php if ($permission == "admin"){
      echo '<a id="adminTab" href="admin-console.php" onclick="moveIndicator(this)" class="tab" ';
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

window.addEventListener("DOMContentLoaded", function(){setActive(findActiveTab()); modifyHistory();});
window.addEventListener("load", function(){setActive(findActiveTab())});
window.addEventListener("resize", function(){setActive(findActiveTab())}, true);;

function closeClapper(){
  document.getElementById('top').style.transform = "rotate(0deg)";
}

function showContent(){
  setTimeout(closeClapper, 300);
  setTimeout(shrinkHeader, 800);
}

showContent();


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

var currentTab;
function findActiveTab(){
  var pageTabs = document.getElementById('page-tabs');
  for(var i = 0; i < pageTabs.children.length; i++){
    if(pageTabs.children[i].getAttribute('data-active') === "true"){
      currentTab = pageTabs.children[i];
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


function moveIndicator(targetTab) {
  var indicator  = document.getElementById('indicator');
  var targetBBox = targetTab.getBoundingClientRect();
  var currentBBox = indicator.getBoundingClientRect();
  var deltaLeft = targetBBox.left - currentBBox.left;
  if(deltaLeft < 0){
    indicator.style.transition = "left 0.2s, right 0.4s";
  }else{
    indicator.style.transition = "right 0.2s, left 0.4s";
  }
  indicator.style.left = targetBBox.left + "px";
  indicator.style.right = document.getElementById('header').getBoundingClientRect().width - targetBBox.right + "px";
  currentTab.setAttribute("data-active",  "false");
  targetTab.setAttribute("data-active", "true");
  currentTab = targetTab;
}

function slideIndicator(event){
  event.preventDefault();
  event.stopPropagation();
  if(event.target.getAttribute("data-active") != "true" ){
    changePage(event.target.getAttribute("href"));
    moveIndicator(event.target);
  }
  return false;
}

function changePage(href){
  var indicator  = document.getElementById('indicator');
  var left = indicator.style.left;
  var right = indicator.style.right;
  var xhr = new XMLHttpRequest();
  xhr.open('GET','page-fragments/' + href);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function(){
    window.history.pushState({html: xhr.responseText, tabID: currentTab.id} , href, href);
    var container = document.getElementById('container');
    container.style.animationName = "slide-out";
    container.style.opacity =  "0";
    var f = function(){

      container.innerHTML = xhr.responseText;
      evalScripts(container);
      container.style.animationName = "slide-in";
      container.style.opacity =  "1";}

    setTimeout(f, 500);
  }
  xhr.send();
}

function evalScripts(element) {
  var scripts = element.getElementsByTagName("script");
  for(var i = 0; i < scripts.length; i++) {
    eval(scripts[i].innerHTML);
  }
}

window.onpopstate = function(e){
    if(e.state){
      document.getElementById('container').innerHTML = e.state.html;
      moveIndicator(document.getElementById(e.state.tabID));
      evalScripts(container);
    }else{
      location.reload();
    }
};

function modifyHistory() {
  if(currentTab.getAttribute("onclick") === "slideIndicator(event)") {
    window.history.replaceState({html: document.getElementById('container').innerHTML, tabID: currentTab.id} , location.href, location.href);
  }
}
</script>
