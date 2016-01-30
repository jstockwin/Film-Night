<?php
ob_start();
require '../header.php';
require $root.'../../database.php';
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
    if (strtotime($row["Roll_Call_Start"]) - 300 < time() && time() < strtotime($row["Roll_Call_Start"]) + 300){
      // within 5 minutes of roll call start. Reset attendence of all active users.
      echo "roll call";
      $sql2 = "UPDATE users SET Attending=1 WHERE Active=1";
      $result2 = $conn->query($sql2);
      $sql3 = "SELECT * FROM users WHERE Permission='admin'"; // Change to WHERE Active=1 after testing.
      $result3 = $conn->query($sql3);
      $to = "jstockwin@gmail.com";
      while($row3 = $result3->fetch_assoc()){
        $to = $to.", ".$row3['Email'];
      }
      $message = '
      <html>
      <head>Film Night</head>
      <body>
      <p>Dear HiveMember,</p>
      <br>
      <p>Please <a href="https://jakestockwin.co.uk/filmnight/settings.php">click here</a> and fill in the form if you are not planning on attending film night this week.</p>
      <p>If you are attending you do not need to do anything. If you say you are not attending, then films which you have vetod will not be selected this week.</p>
      <br>
      <p>Best wishes,<br>The HiveBot</p>
      </body>
      ';
      mail($to, "Film Night Attendence", "Bcc: ".$to."\r\n".$message, "Content-type:text/html");
    }else if(strtotime($row["Voting_Start"]) - 300 < time() && time() < strtotime($row["Voting_Start"]) + 300){
      // Select films:
      header("location: select_films.php");

    // Email users:
    $sql2 = "SELECT * FROM users WHERE Permission='admin'"; // Change to WHERE Active=1 after testing.
    $result2 = $conn->query($sql2);
    $to = "jstockwin@gmail.com";
    while($row2 = $result2->fetch_assoc()){
      $to = $to.", ".$row2['Email'];
    }
    $message = '
    <html>
    <head>Film Night</head>
    <body>
    <p>Dear HiveMember,</p>
    <br>
    <p>Please <a href="https://jakestockwin.co.uk/filmnight/voting.php">click here</a> to vote for this weeks film night.</p>
    <br>
    <p>Best wishes,<br>The HiveBot</p>
    </body>
    ';
    mail($to,"Film Night Voting", "Bcc: ".$to."\r\n".$message, "Content-type:text/html");
  }else if(strtotime($row["Results_Start"]) - 300 < time() && time() < strtotime($row["Results_Start"]) +  300){
    // Within 5 minutes of results starting. Notify users.
    echo "results";
    //mail("localhost","test","test");
    $sql2 = "SELECT * FROM users WHERE Permission='admin'"; // Change to WHERE Active=1 after testing.
    $result2 = $conn->query($sql2);
    $to = "jstockwin@gmail.com";
    while($row2 = $result2->fetch_assoc()){
      $to = $to.", ".$row2['Email'];
    }
    $message = '
    <html>
    <body>
    <p>Dear HiveMember,</p>
    <p>Please <a href="https://jakestockwin.co.uk/filmnight/results.php">click here</a> to view the winning films!</p>
    <br>
    <p>Best wishes,<br>The HiveBot</p>
    </body>
    </html>
    ';
    mail($to,"Film Night Results", "Bcc: ".$to."\r\n".$message, "Content-type:text/html");

    echo "results";
  }
}
}

?>