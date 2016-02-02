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
    if($_POST['votes'] == "WITHDRAW"){
      $sql2 = "DELETE FROM votes WHERE ID='".$_SESSION['Email']."';";
      $result2 = $conn->query($sql2);
      if($result2==1){
        echo "successfully removed vote";
      }else{
        echo "There was an error submitting to the database: ".$sql2;
        echo "Returned: ".$result2;
      }
    }else{
      $post = file_get_contents('php://input');
      echo $post;
      $vote = str_replace("votes=","",$post);
      // Should implement a "if($_POST['votes']=="REMOVE"){delete their vote}" here

      echo "Vote: ".$_POST['votes'];
      // Check if they have already voted
      $sql = "SELECT * FROM votes WHERE ID='".$_SESSION['Email']."'";
      $result = $conn->query($sql);
      if ($result->num_rows == 0){
        // User has not yet voted.
        $sql2 = "INSERT INTO votes VALUES ('".$_SESSION['Email']."', '".$vote."');";
        $result2 = $conn->query($sql2);
        echo "New Vote: ".$result2;
        if($result2==1){
          echo "successfully sumbitted vote";
        }else{
          echo "There was an error submitting to the database: ".$sql2;
          echo "Returned: ".$result2;
        }

        // Should check $result2 == 1 (no errors)
      }else{
        //user has already voted.
        $sql2 = "UPDATE votes SET Vote='".$vote."' WHERE ID='".$_SESSION['Email']."';";
        $result2 = $conn->query($sql2);
        echo "Updated Vote: ".$result2;
        if($result2==1){
          echo "successfully sumbitted vote";
        }else{
          echo "There was an error submitting to the database: ".$sql2;
          echo "Returned: ".$result2;
        }
      }
    }
  }
}
?>
