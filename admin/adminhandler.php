<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.
if(session_status()== PHP_SESSION_NONE){
  session_start();
}
if(loginCheck($session)=="admin"){
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    $_SESSION['ERROR']="adminhandler.php failed to connect to sql db: ".$conn->connect_error;
  }

  if(isset($_POST['updateID'])){
    if($_POST['updateID'] == "new"){
      $sql = 'INSERT INTO timings (Roll_Call_Start, Roll_Call_End, Voting_Start, Voting_End, Results_Start, Results_End)
            VALUES ("'.$_POST['roll_call_start'].'","'.$_POST['roll_call_end'].'","'.$_POST['voting_start'].'","'.$_POST['voting_end'].'","'.$_POST['results_start'].'", "'.$_POST['results_end'].'" )';
    }else{
      $sql = 'UPDATE timings SET Roll_Call_Start="'.$_POST['roll_call_start'].'", Roll_Call_End="'.$_POST['roll_call_end'].'", Voting_Start="'.$_POST['voting_start'].'", Voting_End="'.$_POST['voting_end'].'",
      Results_Start="'.$_POST['results_start'].'", Results_End="'.$_POST['results_end'].'" WHERE ID='.$_POST['updateID'].'';
    }
    $result = $conn->query($sql);
    if($result == 1){
      header("location: ../admin-console.php");
    }else{
      echo "Something went wrong. SQL call:<br>".$sql."<br>Result:<br>".$result;
      $_SESSION['ERROR']="adminhandler.php went wrong. SQL call:<br>".$sql."<br>Result:<br>".$result;
    }
  }else if(isset($_POST['deleteID'])){
    $sql = 'DELETE FROM timings WHERE ID='.$_POST['deleteID'];
    $result = $conn->query($sql);
    if($result == 1){
      header("location: ../admin-console.php");
    }else{
      echo "Something went wrong. SQL call:<br>".$sql."<br>Result:<br>".$result;
      $_SESSION['ERROR']="adminhandler.php went wrong. SQL call:<br>".$sql."<br>Result:<br>".$result;
    }
  }else{
    echo "ERROR: Unknown request";
    $_SESSION['ERROR']="adminhandler.php reported an unknown request";

  }

}else{
  echo "You do not have sufficient permissions to do this";
}

?>
