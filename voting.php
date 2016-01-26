<!DOCTYPE html>
<?php include 'StyleSheet.html'; include 'header.php'; if(!loginCheck($session)) : ?>
<div class="g-signin2" data-onsuccess="onSignIn"></div>
<?php else : ?>
<header></header>

<script>window.onload = function() { init() };</script>

  <script>

  var YouHaveBeenWarned =false;
  var ThisTime = false;
  var YouHaveBeenWarnedDown =false;
  var ThisTimeDown =false;
  var numberOfFields = 0;

    function init(){
    //  google.script.run.withSuccessHandler(populateList).getListItems();
    <?php
    require $root.'../../database.php';
    $conn = new mysqli($host, $username, $password, "films");

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }


    $sql = "SELECT * FROM selected_films";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
      echo 'var films = [';
      while($row = $result->fetch_assoc()){
        echo '[';
        echo '"'.$row["Film"].'",';
        echo '"'.$row["Year"].'",';
        echo '"'.$row["Metascore"].'",';
        echo '"'.$row["IMDb"].'",';
        echo '"'.$row["Plot"].'",';
        echo '"'.$row["Poster"].'"';
        echo '],';
      }
      echo '];';
    }else{
      // Selected films is empty.
      echo "var films = []";
    }

    $sql = 'SELECT * FROM votes WHERE ID="'.$_SESSION['Email'].'";';
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
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
    }


    $conn->close();

    ?>


    populateList(films);
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
   }else if(ThisTime){
     alert("Goodbye");
     var background = document.getElementById("background");
     while (background.firstChild) {
       background.removeChild(background.firstChild);
     }
     background.style.height = "6000px";
     background.style.background = "url('https://nguyensindy.files.wordpress.com/2013/10/nic-cage-lkasndlansd.png')";
   }else{
     alert("That is already the top entry");
     YouHaveBeenWarned =true;
   }
   }else if(id==numberOfFields && direction==-1){
    if(YouHaveBeenWarnedDown && !ThisTimeDown){
      alert("I will end you.");
      ThisTimeDown =true;
    }else if(ThisTimeDown){
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
     document.getElementById("Plot"+id).innerHTML = "<b> Plot:</b> "+response[4];
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
     document.getElementById("Plot"+id).innerHTML = "<b> Plot:</b> "+response[4];
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
       orderedTitles.push('"'+encodeURIComponent(document.getElementById(i+1).innerHTML).replace(/'/g, "%27")+'": '+ (i+1));
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
     };
     xhr.send('votes={' + orderedTitles + '}');


     //google.script.run.withSuccessHandler(finishedSubmitting).storeVotes(orderedTitles);
   }


   /* function finishedSubmitting(){
      var button = document.getElementById("submit");
      button.disabled=true;
      button.innerHTML="Submitted";
    } */

    function spookify(numberOfSpookyThings){
      var HTMLToAdd = "";
    var possibleSpookyThings = ["https://upload.wikimedia.org/wikipedia/commons/1/14/Spider_Web_-_Noun_project_813.svg","https://upload.wikimedia.org/wikipedia/commons/c/c6/Spider.svg","http://colouringbook.org/FAVS/2011/October/COLOURINGBOOK.ORG/Citrouille_Halloween_Pumpkin.svg"];
      for(var i=0; i<numberOfSpookyThings; i++){
        HTMLToAdd = HTMLToAdd +  '<img src="' + possibleSpookyThings[Math.floor((Math.random() * 3))] + '" class="falling" style="left:' + Math.floor((Math.random() * 100) + 1) + 'vw; animation-delay:'+ ((Math.random() * 20)+2) +'s; top:'+((Math.random() * -50) - 10)+'vh;">';
      }
     document.getElementById("2spooky4me").innerHTML = HTMLToAdd;
    }

   </script>

<div id="2spooky4me" style="overflow:hidden; height:100%; width:100%;"></div>

<div id="background" style="width:94%;background:#d5d5d5;padding:3%;">
<div id="cards">

</div>

<button type="button" id="submit" style="margin:auto;width:20%;display:block" ><?php if($voted){echo "Update Vote";}else{echo "Submit Vote";} ?></button>
</div>
<script>
    document.getElementById("submit").addEventListener("click",function(){submit();});
</script>

<?php endif; ?>
