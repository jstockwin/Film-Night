<?php
  require_once '../header.php';
  require_once $root.'../../database.php';


  if (loginCheck($session)=="admin"){

  // Create connection
  $conn = new mysqli($host, $username, $password, "films");

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  echo "Connected successfully";
  echo "<br>";

  echo "Current time:<br>";
  $now = date('Y-m-d H:i:s');
  echo $now;
  echo "<br>Current status:<br>";
  echo status($root);
  $times = [];
  $sql = "SELECT * FROM timings";
  $result = $conn->query($sql);
  if($result->num_rows > 0){

    while($row = $result->fetch_assoc()){
      array_push($times, array($row['ID'], $row["Roll_Call_Start"], $row["Roll_Call_End"], $row["Voting_Start"], $row["Voting_End"], $row["Results_Start"], $row["Results_End"]));
    }

    echo "<table>";
    echo "<tr><td>ID</td><td>Roll Call Start</td><td>Roll Call End</td><td>Voting Start</td><td>Voting End</td><td>Results Start</td><td>Results End</td></tr>";
    foreach( $times as &$time){
      echo "<tr>";
      echo "<td>".$time[0]."</td><td>".$time[1]."</td><td>".$time[2]."</td><td>".$time[3]."</td><td>".$time[4]."</td><td>".$time[5]."</td><td>".$time[6]."</td>";
      echo "</tr>";
    }
    echo "</table>";
  }
  $conn->close();
  echo "<br><br>To update/insert a new entry, fill in the form below<br><br>";
  echo '<form action="adminhandler.php" method="post">';
  echo 'ID: <select id="updateID" name="updateID" onchange="updateText()">';
  echo '<option value="new">Create New</option>';
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
  echo "<br><br>To delete an entry, select the ID and click delete below<br><br>";
  echo '<form action="adminhandler.php" method="post">';
  echo 'ID: <select name="deleteID">';
  foreach($times as &$id){
    echo '<option value="'.$id[0].'">'.$id[0].'</option>';
  }
  echo '</select><br>';
  echo '<input type="submit" value="Delete">';
  echo '</form>';
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
?>
