<?php
ob_start();
require '../header.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  // Not signed in.
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_POST['votes'])){
    echo "Vote: ".$_POST['votes'];
    // Check if they have already voted
    $sql = "SELECT * FROM votes WHERE ID='".$_SESSION['Email']."'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0){
      // User has not yet voted.
      $sql2 = 'INSERT INTO votes VALUES ("'.$_SESSION['Email'].'", "'.$_POST['votes'].'");';
      $result2 = $conn->query($sql2);
      echo "New Vote: ".$result2;
      // Should check $result2 == 1 (no errors)
    }else{
      //user has already voted.
      $sql2 = 'UPDATE votes SET Vote="'.$_POST['votes'].'" WHERE ID="'.$_SESSION['Email'].'";';
      $result2 = $conn->query($sql2);
      echo "Updated Vote: ".$result2;
      // Should check $result2 == 1 (no errors)
    }
  }
}
?>
