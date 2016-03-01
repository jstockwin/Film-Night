<?php include 'setup.php';?>
<?php include 'head.php'; head('Film Night Admin Console');?>
<body>
<?php include 'top-nav.php';?>
<div id="container">;
<?php
if (loginCheck($session, TRUE)=="admin"){

  echo "<script>window.onload = function() { closeClapper(); setTimeout(shrinkHeader, 500); };</script>";

  echo "<p>Connected successfully";
  echo "<br>";

  echo "Current time:<br>";
  $now = date('Y-m-d H:i:s');
  echo $now;
  echo "<br>Current status:<br>";
  echo status();
  $times = getFilmNights();
  if($times != "None"){
    echo "</p><table>";
    echo "<tr><td>ID</td><td>Roll Call Start</td><td>Roll Call End</td><td>Voting Start</td><td>Voting End</td><td>Results Start</td><td>Results End</td></tr>";
    foreach( $times as &$time){
      echo "<tr>";
      echo "<td>".$time[0]."</td><td>".$time[1]."</td><td>".$time[2]."</td><td>".$time[3]."</td><td>".$time[4]."</td><td>".$time[5]."</td><td>".$time[6]."</td>";
      echo "</tr>";
    }
    echo "</table>";
  }
  echo "<p>To update/insert a new entry, fill in the form below<br>Every start time must be on the 1/2 hour for automatic emails/film selection to work.</p>";
  echo '<form action="admin/adminhandler.php" method="post">';
  echo 'ID: <select id="updateID" name="updateID" onchange="updateText()">';
  echo '<option value="'.(end($times)[0]+1).'">Create New</option>';
  foreach($times as &$id){
    echo '<option value="'.$id[0].'">'.$id[0].'</option>';
  }
  echo '</select><br>
  Roll Call Start: <input type="datetime-local" name="roll_call_start" id="roll_call_start"><br>
  Roll Call End: <input type="datetime-local" name="roll_call_end" id="roll_call_end"><br>
  Voting Start: <input type="datetime-local" name="voting_start" id="voting_start"><br>
  Voting End: <input type="datetime-local" name="voting_end" id="voting_end"><br>
  Results Start: <input type="datetime-local" name="results_start" id="results_start"><br>
  Results End: <input type="datetime-local" name="results_end" id="results_end"><br>
  <input type="submit" value="Submit">
  </form>';
  echo '


  ';
  echo '<script type="text/javascript">';
  echo "function updateText() {
    var e = document.getElementById('updateID')
    var id = e.options[e.selectedIndex].value;";
    foreach($times as &$time){
      echo 'if(id == '.$time[0].'){
        document.getElementById("roll_call_start").value = "'.date('Y-m-d',strtotime($time[1]))."T".date('H:i:s.00',strtotime($time[1])).'";
        document.getElementById("roll_call_end").value = "'.date('Y-m-d',strtotime($time[2]))."T".date('H:i:s.00',strtotime($time[2])).'";
        document.getElementById("voting_start").value = "'.date('Y-m-d',strtotime($time[3]))."T".date('H:i:s.00',strtotime($time[3])).'";
        document.getElementById("voting_end").value = "'.date('Y-m-d',strtotime($time[4]))."T".date('H:i:s.00',strtotime($time[4])).'";
        document.getElementById("results_start").value = "'.date('Y-m-d',strtotime($time[5]))."T".date('H:i:s.00',strtotime($time[5])).'";
        document.getElementById("results_end").value = "'.date('Y-m-d',strtotime($time[6]))."T".date('H:i:s.00',strtotime($time[6])).'";
      };
      if(id == "new"){
        document.getElementById("roll_call_start").value = "";
        document.getElementById("roll_call_end").value = "";
        document.getElementById("voting_start").value = "";
        document.getElementById("voting_end").value = "";
        document.getElementById("results_start").value = "";
        document.getElementById("results_end").value = "";
      }';
    }



    echo "}";
    echo "</script>";


  }else{
    echo "You are not authorised to use this page";
  }
      echo "</div>";
  ?>
</body>
</html>
