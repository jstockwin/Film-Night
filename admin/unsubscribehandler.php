
<?php
ob_start();
require '../setup.php';
require $GLOBALS['root'].'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="unsubscribehandler.php failed to verify that you are signed in";
}else{
  if(isset($_POST['key'])){

    $key=$_POST['key'];
    $sql = 'DELETE FROM endpoints WHERE user_id="'.$_SESSION['ID'].'" AND id="'.$key.'";';
    $result = query($sql);
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }

}


?>
