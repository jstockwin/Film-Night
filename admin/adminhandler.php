<?php
ob_start();
require '../setup.php';
ob_end_clean(); // supresses output.
if(loginCheck($session)=="admin"){
  if(isset($_POST['updateID'])){
    $sql = 'INSERT INTO timings
            VALUES ('.$_POST['updateID'].',"'.$_POST['roll_call_start'].'","'.$_POST['roll_call_end'].'","'.$_POST['voting_start'].'","'.$_POST['voting_end'].'"
            ,"'.$_POST['results_start'].'", "'.$_POST['results_end'].'")
            ON DUPLICATE KEY UPDATE
            Roll_Call_Start=VALUES(Roll_Call_Start),
            Roll_Call_End=VALUES(Roll_Call_End),
            Voting_Start=VALUES(Voting_Start),
            Voting_End=VALUES(Voting_End),
            Results_Start=VALUES(Results_Start),
            Results_End=VALUES(Results_End)';
    $result = query($sql);
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
