<?php
ob_start();
require '../header.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(session_status()== PHP_SESSION_NONE){
  session_start();
}
if(!loginCheck($session)){
  echo "Error: User not logged in";
  $_SESSION['ERROR']="votinghandler.php failed to confirm that you were logged in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    $_SESSION['ERROR']="votinghandler.php failed connect to sql database: ".$conn->connect_error;
  }
  if(isset($_POST['votes'])){
    if($_POST['votes'] == "WITHDRAW"){
      $sql2 = "DELETE FROM incomingvotes WHERE ID='".$_SESSION['Email']."';";
      $result2 = $conn->query($sql2);
      if($result2==1){
        echo "successfully removed vote";
      }else{
        echo "Error: There was an error submitting to the database: ".$sql2;
        echo "Returned: ".$result2;
      }
    }else{
      $post = file_get_contents('php://input');
      echo $post;
      $vote = str_replace("votes=","",$post);

      echo "Vote: ".$_POST['votes'];
      // Check if they have already voted
      $sql = "SELECT * FROM incomingvotes WHERE ID='".$_SESSION['Email']."'";
      $result = $conn->query($sql);
      if ($result->num_rows == 0){
        // User has not yet voted.
        $sql2 = "INSERT INTO incomingvotes VALUES ('".$_SESSION['Email']."', '".$vote."');";
        $result2 = $conn->query($sql2);
        echo "New Vote: ".$result2;
        if($result2==1){
          echo "successfully sumbitted vote";
        }else{
          echo "Error: There was an error submitting to the database: ".$sql2;
          echo "Returned: ".$result2;
          $_SESSION['ERROR'] = "Error: Failed to submit vote:<br> SQL Call:<br>".$sql."<br>result<br>".$result;
        }

      }else{
        //user has already voted.
        $sql2 = "UPDATE incomingvotes SET Vote='".$vote."' WHERE ID='".$_SESSION['Email']."';";
        $result2 = $conn->query($sql2);
        echo "Updated Vote: ".$result2;
        if($result2==1){
          echo "successfully sumbitted vote";
        }else{
          echo "Error: There was an error submitting to the database: ".$sql2;
          echo "Returned: ".$result2;
          $_SESSION['ERROR'] = "Error: Failed to submit vote:<br> SQL Call:<br>".$sql."<br>result<br>".$result;
        }
      }
    }
  }
}
?>
