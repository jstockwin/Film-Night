<?php include 'setup.php';?>
<?php require $GLOBALS['root'].'../../database.php'; ?>
<?php include 'head.php'; head('Film Night Voting');?>
<body>
<?php include 'top-nav.php'?>
<?php if(($permission != FALSE && status() =="voting") || $permission == "admin"): ?>

<script>window.onload = function() { init() };</script>
<script>
  var YouHaveBeenWarned =false;
  var ThisTime = false;
  var YouHaveBeenWarnedDown =false;
  var ThisTimeDown =false;
  var numberOfFields = 0;

  function init(){
    <?php
    $result = getSelectedFilms();

    if ($result->num_rows > 0){
      echo 'var films = [';
      while($row = $result->fetch_assoc()){
        echo '[';
        echo '"'.$row["Film_Name"].'",';
        echo '"'.$row["Year"].'",';
        echo '"'.$row["metascore"].'",';
        echo '"'.$row["imdbscore"].'",';
        echo '"'.$row["plot"].'",';
        echo '"'.$row["poster"].'"';
        echo '],';
      }
      echo '];';
    }else{
      // Selected films is empty.
      echo "var films = []";
    }
    $vote = getUserVotes($_SESSION['ID']);
    if($vote!="FALSE"){
      $voted = TRUE;
      echo "var Vote = {";
      foreach ($vote as $name => $position) {
        echo '"'.$name.'": '.$position.',';
      }
      echo "};";
      echo "films.sort(function(a, b){return Vote[a[0]] - Vote[b[0]];});";
      echo "console.log('voted');";
    }else{
      $voted = FALSE;
      echo "console.log('not voted');";
    }


    /*if ($result->num_rows > 0){
      // User has voted previously
      $voted = TRUE;
      echo "console.log('true');";
      while($row = $result->fetch_assoc()){
        echo 'var Vote = '.urldecode($row["Vote"]).';';
      }

      echo "films.sort(function(a, b){return Vote[a[0]] - Vote[b[0]];});";

      echo "console.log(films);";

    }else{
      // User has not voted previously
      $voted = FALSE;
    }*/


    ?>


    populateList(films);
    closeClapper();
    setTimeout(shrinkHeader, 500);
  }

  function populateList(values){
    console.log(values);
    var list = document.getElementById("cards");

     for(var i=0; i<values.length;i++){
        numberOfFields++;
        list.innerHTML += generateHTMLToAdd();
     }
     for( var i=0; i< values.length;i++){
     [i+1, values[i]];
     fillOutListItem([i+1, values[i]]);
     }

     //spookify(50);

  }

  function MoveItem(id, direction) {
    id = id.substring(2);
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

    fillOutListItemWithoutListeners([id,values2]);
    fillOutListItemWithoutListeners([id-direction,values]);
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
    console.log( buttonDivider +upButton + downButton + divEnd+ div + title+ div2+img +plot+year+rating+metaScore+divEnd+divEnd );
    return  buttonDivider +upButton + downButton + divEnd+ div + title+ div2+img +plot+year+rating+metaScore+divEnd+divEnd ;
  }

  function getInfoFromListItem(id){
    var response =[];
    response.push(document.getElementById(id).innerHTML);
    response.push(document.getElementById("Year"+id).innerHTML.substring(14));
    response.push(document.getElementById("MetaScore"+id).innerHTML.substring(19));
    response.push(document.getElementById("Rating"+id).innerHTML.substring(21));
    response.push(document.getElementById("Plot"+id).innerHTML.substring(14));
    response.push(document.getElementById("img"+id).src);
    console.log(response);
    return response;
  }

  function fillOutListItemWithoutListeners(parameters){
    var response = parameters[1];
    var id = parameters[0];
    document.getElementById(id).innerHTML = response[0];
    document.getElementById("img"+id).src = response[5];
    document.getElementById("Plot"+id).innerHTML = "<b> Plot:</b> "+decodeURIComponent(response[4]);
    document.getElementById("Year"+id).innerHTML = "<b> Year:</b> "+response[1];
    document.getElementById("Rating"+id).innerHTML = "<b> IMDb Rating:</b> "+response[3];
    document.getElementById("MetaScore"+id).innerHTML = "<b> Metascore:</b> "+response[2];
  }

  function fillOutListItem(parameters){
    console.log(parameters);
    var response = parameters[1];
    var id = parameters[0];
    document.getElementById(id).innerHTML = response[0];
    document.getElementById("img"+id).src = response[5];
    document.getElementById("Plot"+id).innerHTML = "<b> Plot:</b> "+decodeURIComponent(response[4]);
    document.getElementById("Year"+id).innerHTML = "<b> Year:</b> "+response[1];
    document.getElementById("Rating"+id).innerHTML = "<b> IMDb Rating:</b> "+response[3];
    document.getElementById("MetaScore"+id).innerHTML = "<b> Metascore:</b> "+response[2];
    document.getElementById("UB"+id).addEventListener("click", function() {MoveItem(this.id,1);}, false);
    document.getElementById("DB"+id).addEventListener("click", function() {MoveItem(this.id,-1);}, false);
  }

  function submit(){
    console.log("submit");
    var orderedTitles =[];
    for(var i=0; i< numberOfFields;i++){
      var targets = [{target: "\\!", result: "%21"}, {target: "\\*", result: "%2A"},{target: "\\(", result: "%27"},{target: "\\)", result: "%28"},{target: "\\'", result: "%29"}];
      var encodedName = encodeURIComponent(document.getElementById(i+1).innerHTML);
      for (var j = 0; j < targets.length; j++){
        encodedName = encodedName.replace(new RegExp(targets[j].target, 'g'), targets[j].result);
      }
      orderedTitles.push('"'+encodedName+'": '+ (i+1));
    }
    var button = document.getElementById("submit");
    console.log(orderedTitles);
    console.log('votes={' + orderedTitles + '}');
    button.disabled=true;
    button.innerHTML="Submitting";

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'admin/votinghandler.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      button.innerHTML="Submitted";
      console.log(xhr.responseText);
      if(xhr.responseText.indexOf("Error")>-1){
        location.reload();
      }
    };
    xhr.send('votes={' + orderedTitles + '}');
  }

  function withdraw(){
    console.log("withdraw");
    var button = document.getElementById("withdraw");
    var button2 = document.getElementById("submit");
    button.disabled=true;
    button2.disabled=true;
    button.innerHTML="Withdrawing";
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'admin/votinghandler.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      button.innerHTML="Withdrawn";
      console.log(xhr.responseText);
    };
    xhr.send('votes=WITHDRAW');
  }

</script>

<div id="background" style="width:94%;background:#d5d5d5;padding:3%;">
  <div id="cards"></div>
  <?php if($voted){
    echo '<div style="margin:auto;width:50%;display:block">';
    echo '<button type="button" id="submit" style="margin-left:5%;margin-right:5%;width:40%;display:inline-block" >Update Vote</button>';
    echo '<button type="button" id="withdraw" style="margin-left:5%;margin-right:5%;width:40%;display:inline-block" >Withdraw Vote</button>';
    echo '</div>';
  }else{
    echo '<button type="button" id="submit" style="margin: auto; width:20%;display:block">Submit Vote</button>';
  }
  ?>
</div>

<script>
  document.getElementById("submit").addEventListener("click",function(){submit();});
  <?php if($voted){
    echo 'document.getElementById("withdraw").addEventListener("click",function(){withdraw();});';
  } ?>
</script>

<?php endif; ?>
</body>
</html>
