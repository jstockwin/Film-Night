
<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="unsubscribehandler.php failed to verify that you are signed in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    $_SESSION['ERROR']="unsubscribehandler.php failed connect to sql database: ".$conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_POST['key'])){

    $key=$_POST['key'];
    $sql = 'DELETE FROM endpoints WHERE ID="'.$_SESSION['ID'].'" AND Identifier="'.$key.'";';
    $result = $conn->query($sql);
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }

}


?>
