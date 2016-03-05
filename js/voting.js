var YouHaveBeenWarned = false;
var ThisTime = false;
var YouHaveBeenWarnedDown = false;
var ThisTimeDown = false;
var numberOfFields = 0;
var voted;
var films;

function init(){
  fetch('admin/infohandler.php?wants=films', {credentials: 'same-origin'}).then(function(response) {
    return response.json();
  }).then(function(filmInfo) {
    if(filmInfo.status == "success") {
      voted = filmInfo.hasVoted;
      films = filmInfo.filmList;
      console.log('voted:', voted);
      populateList(films);
      setButtons(voted);
      closeClapper();
      setTimeout(shrinkHeader, 500);
    } else if(filmInfo.status == "error") {
      throw filmInfo.error;
    }
  }).catch(function(error) {
    console.log("Something went wrong:", error);
  });
}

function populateList(values){
  console.log(values);
  var list = document.getElementById("cards");
  for(var i=0; i<values.length;i++){
    numberOfFields++;
    list.innerHTML += generateHTMLToAdd();
  }
  for( var i=0; i< values.length;i++){
    fillOutListItem(i+1, values[i]);
  }

    //spookify(50);

}

function setButtons(voted) {
  if(!voted) {
    document.getElementById("submit").innerHTML = "Submit Vote";
    document.getElementById("withdraw").style.display = "none";
  } else {
    document.getElementById("submit").innerHRML = "Update Vote";
    document.getElementById("withdraw").style.display = "inline";
  }
}

function reenableSubmitButton(message) {
  var button = document.getElementById("submit");
  if(button.disabled) {
    button.disabled = false;
    button.innerHTML = message;
  }
}

function reenableWithdrawButton(message) {
  var button = document.getElementById("withdraw");
  button.style.display = "inline";
  if(button.disabled) {
    button.disabled = false;
    button.innerHTML = message;
  }
}

function MoveItem(id, direction) {
  id = id.substring(2);
  reenableSubmitButton("Update Vote");
  if( id ==1 && direction == 1){
    if(YouHaveBeenWarned && !ThisTime){
      alert("Press it again. I dare you.");
      ThisTime =true;
    } else if(ThisTime) {
      alert("Goodbye");
      var background = document.getElementById("background");
      while (background.firstChild) {
        background.removeChild(background.firstChild);
      }
      background.style.height = "6000px";
      background.style.background = "url('https://nguyensindy.files.wordpress.com/2013/10/nic-cage-lkasndlansd.png')";
    } else {
      alert("That is already the top entry");
      YouHaveBeenWarned =true;
    }
  } else if(id==numberOfFields && direction==-1) {
    if(YouHaveBeenWarnedDown && !ThisTimeDown) {
      alert("I will end you.");
      ThisTimeDown =true;
    } else if(ThisTimeDown) {
      alert("Goodbye");
      var background = document.getElementById("background");
      while (background.firstChild) {
        background.removeChild(background.firstChild);
      }
      background.style.height = "6000px";
      background.style.background = "url('https://s-media-cache-ak0.pinimg.com/originals/c5/1e/63/c51e637e078f71ea397e01c01bdee399.jpg')";
    }else{
      alert("That is already the bottom entry");
      YouHaveBeenWarnedDown =true;
    }
  }
  var values = getInfoFromListItem(id)
  var values2 = getInfoFromListItem(id-direction);

  fillOutListItemWithoutListeners(id, values2);
  fillOutListItemWithoutListeners(id-direction, values);
}

function generateHTMLToAdd(){
  var list = "<li>";
  var endList = "</li>";
  var buttonDivider = "<div style=\"width:8%;display:inline-block;margin:1%\">";
  var upButton = "<button type=\"button\" id=\"UB"+numberOfFields+"\"  style=\"width:100%;margin-bottom:20px\" >Up</button>";
  var downButton = "<button type=\"button\" id=\"DB"+numberOfFields+"\" style=\"width:100%;\" >Down</button>";
  var div = "<div class=\"card\">";
  var title = "<h1 id=\""+numberOfFields+"\"></h1>";
  var div2 = "<div style=\"min-height:222px\">";
  var img = "<img id=\"img"+numberOfFields+"\" class=\"picture\">";
  var plot = "<p id=\"Plot"+numberOfFields+"\"><b>Plot:</b></p>";
  var year = "<p id=\"Year"+numberOfFields+"\"><b>Year:</b></p>";
  var rating = "<p id=\"Rating"+numberOfFields+"\"><b>IMDb Rating:</b></p>";
  var metaScore = "<p id=\"MetaScore"+numberOfFields+"\"><b>Metascore:</b></p>";
  var divEnd = "</div>";
  //console.log( buttonDivider +upButton + downButton + divEnd+ div + title+ div2+img +plot+year+rating+metaScore+divEnd+divEnd );
  return  buttonDivider +upButton + downButton + divEnd+ div + title+ div2+img +plot+year+rating+metaScore+divEnd+divEnd ;
}

function getInfoFromListItem(id){
  var response = {};
  response.id = document.getElementById(id).dataset.id;
  response.title = document.getElementById(id).innerHTML;
  response.year = document.getElementById("Year"+id).innerHTML.substring(14);
  response.metascore = document.getElementById("MetaScore"+id).innerHTML.substring(19);
  response.imdbscore = document.getElementById("Rating"+id).innerHTML.substring(21);
  response.plot = document.getElementById("Plot"+id).innerHTML.substring(14);
  response.poster = document.getElementById("img"+id).src;
  console.log(response);
  return response;
}

function fillOutListItemWithoutListeners(id, response){
  document.getElementById(id).dataset.id = response.id;
  document.getElementById(id).innerHTML = response.title;
  document.getElementById("img"+id).src = response.poster;
  document.getElementById("Plot"+id).innerHTML = "<b> Plot:</b> "+decodeURIComponent(response.plot);
  document.getElementById("Year"+id).innerHTML = "<b> Year:</b> "+response.year;
  document.getElementById("Rating"+id).innerHTML = "<b> IMDb Rating:</b> "+response.imdbscore;
  document.getElementById("MetaScore"+id).innerHTML = "<b> Metascore:</b> "+response.metascore;
}

function fillOutListItem(id, response){
  fillOutListItemWithoutListeners(id, response);
  document.getElementById("UB"+id).addEventListener("click", function() {MoveItem(this.id,1);}, false);
  document.getElementById("DB"+id).addEventListener("click", function() {MoveItem(this.id,-1);}, false);
}

function submit(){
  console.log("submit");
  var orderedTitles = {};
  for(var i=0; i< numberOfFields;i++){
    var imdbid = document.getElementById(i+1).dataset.id;
    orderedTitles[imdbid] = i+1;
  }
  var button = document.getElementById("submit");
  console.log(orderedTitles);
  console.log('votes=' + JSON.stringify(orderedTitles));
  button.disabled=true;
  button.innerHTML="Submitting";
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'admin/votinghandler.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    button.innerHTML="Submitted";
    console.log(xhr.responseText);
    reenableWithdrawButton("Withdraw Vote");
    if(xhr.responseText.indexOf("Error")>-1){
      location.reload();
    }
  };
  xhr.send('votes=' + JSON.stringify(orderedTitles));
}

function withdraw(){
  console.log("withdraw");
  var button = document.getElementById("withdraw");
  var button2 = document.getElementById("submit");
  button.disabled=true;
  button.innerHTML="Withdrawing";
  reenableSubmitButton("Submit Vote");
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'admin/votinghandler.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    button.innerHTML="Withdrawn";
    console.log(xhr.responseText);
  };
  xhr.send('votes=WITHDRAW');
}

window.addEventListener("load",function(){init();});
document.getElementById("submit").addEventListener("click",function(){submit();});
document.getElementById("withdraw").addEventListener("click",function(){withdraw();});
