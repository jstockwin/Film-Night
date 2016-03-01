<?php
ob_start();
require '../setup.php';
require $GLOBALS['root'].'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="subscribehandler.php failed to verify that you are signed in";
}else{
  if(isset($_POST['endpoint']) && isset($_POST['name'])){

    $endpoint=$_POST['endpoint'];
    $name=$_POST['name'];
    $sql = 'INSERT INTO endpoints VALUES (NULL,"'.$_SESSION['ID'].'","'.$_POST['name'].'","'.$endpoint.'");';
    $result = query($sql);
    $sql = 'SELECT id, Name FROM endpoints where Endpoint="'.$endpoint.'";';
    $result = query($sql);
    $answer = "{}";
    if($result->num_rows > 0){
      $row = $result->fetch_assoc();
      $answer = json_encode($row);
    }
    echo $answer;
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }

}


?>
