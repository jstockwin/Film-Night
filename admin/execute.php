<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
require $root.'vendor/autoload.php';
use Minishlink\WebPush\WebPush;
ob_end_clean(); // supresses output.

include $root.'../../database.php';
$conn = new mysqli($host, $username, $password, "films");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM timings";
$result = $conn->query($sql);
if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
    if (get_event($root)=="Roll_Call_Start"){
      // within 5 minutes of roll call start. Reset attendence of all active users.
      echo "roll call";
      $sql2 = "UPDATE users SET Attending=1 WHERE Active=1";
      $result2 = $conn->query($sql2);
      $to = get_emails($root, "Roll_Call_Start");
      $message = '
      <html>
      <body>
      <p>Dear HiveMember,</p>
      <br>
      <p>Please <a href="https://jakestockwin.co.uk/filmnight/settings.php">click here</a> and fill in the form if you are not planning on attending film night this week.</p>
      <p>If you are attending you do not need to do anything. If you say you are not attending, then films which you have vetoed will not be selected this week.</p>
      <br>
      <p><b>NOTIFICATIONS ARE NOW WORKING!</b> click "subscribe" on the link above to receive film night notifications through your browser</p>
      <br>
      <p>Best wishes,<br>The HiveBot&trade;</p>
      </body>
      ';
      mail($to, "Film Night Attendance", $message, "Content-type:text/html");

      $sql4 = "SELECT * FROM users WHERE Active=1 AND Endpoint <> ''";
      $result4 = $conn->query($sql4);
      $endpoints = array ();
      while($row4 = $result4->fetch_assoc()){
        array_push($endpoints, $row4['Endpoint']);
      }

      $webPush = new WebPush(array('GCM'=>$push_api));
      // send multiple notifications
      foreach ($endpoints as $endpoint) {
        $webPush->sendNotification($endpoint);
      }
      $webPush->flush();
    }else if (get_event($root)=="Roll_Call_End"){
      // Select films:
      header("location: select-films.php");
    }else if(get_event($root)=="Voting_Start"){


      // Email users:
      $to = get_emails($root, "Voting_Start");
      $message = '
      <html>
      <body>
      <p>Dear HiveMember,</p>
      <br>
      <p>Please <a href="https://jakestockwin.co.uk/filmnight/voting.php">click here</a> to vote for this week&apos;s film night.</p>
      <br>
      <p>Best wishes,<br>The HiveBot&trade;</p>
      </body>
      ';
      mail($to,"Film Night Voting", $message, "Content-type:text/html");

      $sql4 = "SELECT * FROM users WHERE Active=1 AND Endpoint <> ''";
      $result4 = $conn->query($sql4);
      $endpoints = array ();
      while($row4 = $result4->fetch_assoc()){
        array_push($endpoints, $row4['Endpoint']);
      }

      $webPush = new WebPush(array('GCM'=>$push_api));
      // send multiple notifications
      foreach ($endpoints as $endpoint) {
        error_log(print_r($endpoint, TRUE));
        $webPush->sendNotification($endpoint);
      }
      $webPush->flush();

    }else if(get_event($root)=="Results_Start"){
      // Within 5 minutes of results starting. Notify users.
      echo "results";
      $sql3 = "DROP TABLE votes";
      $result3 = $conn->query($sql3);
      $sql3 = "ALTER TABLE incomingvotes RENAME TO votes";
      $result3 = $conn->query($sql3);
      $sql3 = "CREATE TABLE incomingvotes (ID varchar(127), Vote varchar(255))";
      $result3 = $conn->query($sql3);
      $to = get_emails($root, "Results_Start");
      $message = '
      <html>
      <body>
      <p>Dear HiveMember,</p>
      <p>Please <a href="https://jakestockwin.co.uk/filmnight/results.php">click here</a> to view the winning films!</p>
      <br>
      <p>Best wishes,<br>The HiveBot&trade;</p>
      </body>
      </html>
      ';
      mail($to,"Film Night Results", $message, "Content-type:text/html");

      $sql4 = "SELECT * FROM users WHERE Active=1 AND Endpoint <> ''";
      $result4 = $conn->query($sql4);
      $endpoints = array ();
      while($row4 = $result4->fetch_assoc()){
        array_push($endpoints, $row4['Endpoint']);
      }

      $webPush = new WebPush(array('GCM'=>$push_api));
      // send multiple notifications
      foreach ($endpoints as $endpoint) {
        error_log(print_r($endpoint, TRUE));
        $webPush->sendNotification($endpoint);
      }
      $webPush->flush();

      echo "results";
    }
  }
}

?>
