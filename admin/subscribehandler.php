
<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="subscribehandler.php failed to verify that you are signed in";
}else{
  echo "Hello";
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    $_SESSION['ERROR']="subscribehandler.php failed connect to sql database: ".$conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_POST['endpoint']) && isset($_POST['name'])){

    $endpoint=$_POST['endpoint'];
    $sql = 'INSERT INTO endpoints VALUES (NULL,"'.$_SESSION['ID'].'","'.$_POST['name'].'","'.$_POST['endpoint'].'");';
    $result = $conn->query($sql);
    print_r($result);
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }

}


?>
