
<?php
ob_start();
require '../header.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="subscribehandler.php failed to verify that you are signed in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    $_SESSION['ERROR']="nominationhandler.php failed connect to sql database: ".$conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_POST['endpoint'])){
  
    $endpoint=$_POST['endpoint'];
    $sql = 'UPDATE users SET Endpoint="'.$endpoint.'" WHERE ID="'.$_SESSION['Email'].'";';
    $result = $conn->query($sql);
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }

}


?>
