<?php include 'setup.php';?>
<?php include 'head.php'; head('Film Night Settings');?>
<body>
<?php include 'top-nav.php' ?>
<?php if($permission != FALSE): ?>
<script>window.onload = function() { closeClapper(); setTimeout(shrinkHeader, 500); };</script>
<?php
if(session_status()== PHP_SESSION_NONE){
  session_start();
}
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
      $voting30 = $row['Reminder_Voting30'];
      $voting60 = $row['Reminder_Voting60'];

      $attendingNotification = $row['Notification_Attending'];
      $votingNotification = $row['Notification_Voting'];
      $resultsNotification = $row['Notification_Results'];
      $voting30Notification = $row['Notification_Voting30'];
      $voting60Notification = $row['Notification_Voting60'];
    }
  }else{
    echo "ERROR: User settings not found.";
  }
  $conn->close();


echo '<div id="background" style="width:94%;background:#d5d5d5;padding:3%;height:100%;">';
echo "<form action='admin/settingshandler.php' method='POST' class='card'>";
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
<p>If you wish to update your settings then please fill in the form below. Make sure you click the Submit button or your changes will not be saved</p>
<table>
  <tr>
    <td>Event</td><td>Email<br>Notification</td><td>Broswer<br>Notification</td>
  </tr>
  <tr>
    <td>At the start of a roll call</td>
    <td><input type="checkbox" name="attending" value="attending" <?php if($attending=="1"){echo "checked=true";}?>></td>
    <td><input type="checkbox" name="attendingNotification" value="attendingNotification" <?php if($attendingNotification=="1"){echo "checked=true";}?>></td>
  </tr>
  <tr>
    <td>When voting begins</td>
    <td><input type="checkbox" name="voting" value="voting" <?php if($voting=="1"){echo "checked=true";}?>></td>
    <td><input type="checkbox" name="votingNotification" value="votingNotification" <?php if($votingNotification=="1"){echo "checked=true";}?>></td>
  </tr>
  <tr>
    <td>An hour before voting closes, if you have not yet voted</td>
    <td><input type="checkbox" name="voting60" value="voting60" <?php if($voting60=="1"){echo "checked=true";}?>></td>
    <td><input type="checkbox" name="voting60Notification" value="voting60Notification" <?php if($voting60Notification=="1"){echo "checked=true";}?>></td>
  </tr>
  <tr>
    <td>Half an hour before voting closes, if you have not yet voted</td>
    <td><input type="checkbox" name="voting30" value="voting30" <?php if($voting30=="1"){echo "checked=true";}?>></td>
    <td><input type="checkbox" name="voting30Notification" value="voting30Notification" <?php if($voting30Notification=="1"){echo "checked=true";}?>></td>
  </tr>
  <tr>
    <td>When results are made available</td>
    <td><input type="checkbox" name="results" value="results" <?php if($results=="1"){echo "checked=true";}?>></td>
    <td><input type="checkbox" name="resultsNotification" value="resultsNotification" <?php if($resultsNotification=="1"){echo "checked=true";}?>></td>
  </tr>
</table>
 <br>

Which email address would you like to receive these email to?<br>
<input type="text" name="email" value=<?php echo '"'.$email.'"' ?>><br>
<input type="submit" value="Submit">
</address>
</form>

<div class='card' id='#registerCard'>
  <p>Name this device and click subscribe to recieve push notifications to your browser</p>
<input type="text" id="#registerName" value="Device Name"></input>
<button id="#registerWorker" type="button">Subscribe!</button>
</div>

<table id='#endpointTable' class='card'>
  <tr>
    <th colspan=2>Notification Recipients:</th>
  </tr>
</table>
<script src="settings.js"></script>
</div>
<?php endif; ?>
</body>
</html>
