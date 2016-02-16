<?php include 'setup.php';?>
<?php include 'head.php'; head('Film Night Settings', '<link rel="stylesheet" type="text/css" href="extrastyles.css">');?>
<body>
<?php include 'top-nav.php' ?>
<?php if($permission != FALSE): ?>
<script>window.onload = function() { closeClapper(); setTimeout(shrinkHeader, 500); };</script>
<div id="container">

<?php
  require $root.'../../database.php';
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $sql = 'SELECT * FROM users WHERE ID="'.$_SESSION['ID'].'"';
  $result = $conn->query($sql);
  if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $rollCall = $row['Attending'];
      $attending = $row['Reminder_Attending'];
      $voting = $row['Reminder_Voting'];
      $results = $row['Reminder_Results'];
      $email = $row['Email'];
    }
  }else{
    echo "ERROR: User settings not found.";
  }
  $conn->close();



echo "<form action='admin/settingshandler.php' method='POST'>";
if(status($root)=="rollCall"){
  echo 'Will you be attending this weeks film night?<br>
  <select name="rollCall">
    <option value="yes" ';
    if ($rollCall == "1"){echo 'selected="selected"';}
    echo '>Yes, I will be attending</option>
    <option value="no"';
    if ($rollCall !== "1"){echo 'selected="selected"';}
    echo '>No, I won\'t be attending. Please veto my films</option>
    </select><br>';

}?>
Tick which events you would like to receive emails for:<br>
<input type="checkbox" name="attending" value="attending" <?php if($attending=="1"){echo "checked=true";}?>> At the start of a roll call<br>
<input type="checkbox" name="voting" value="voting" <?php if($voting=="1"){echo "checked=true";}?>> When voting begins<br>
<input type="checkbox" name="results" value="results" <?php if($results=="1"){echo "checked=true";}?>> When results are made available<br>
Which email address would you like to receive these email to?<br>
<input type="text" name="email" value=<?php echo '"'.$email.'"' ?>><br>
<input type="submit" value="Submit">
</address>
</form>
<input type="text" id="#registerName" value="Device Name"></input>
<button id="#registerWorker" type="button">Subscribe!</button>
<br>
<table id='#endpointTable'>
  <tr>
    <th colspan=2>Notification Recipients</th>
  </tr>
</table>
<script src="settings.js"></script>
</div>
<?php endif; ?>
</body>
</html>
