<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="subscribehandler.php failed to verify that you are signed in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    $_SESSION['ERROR']="subscribehandler.php failed connect to sql database: ".$conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_POST['endpoint']) && isset($_POST['name'])){

    $endpoint=$_POST['endpoint'];
    $sql = 'INSERT INTO endpoints VALUES (NULL,"'.$_SESSION['ID'].'","'.$_POST['name'].'","'.$_POST['endpoint'].'");';
    $result = $conn->query($sql);
    error_log(print_r($endpoint, TRUE));
    $sql = 'SELECT Identifier, Name FROM endpoints where Endpoint="'.$endpoint.'";';
    $result = $conn->query($sql);
    if($result->num_rows > 0){
      $row = $result->fetch_assoc();
      $result = json_encode(array("Identifier" => $row['Identifier'], "Name" => $row['Name']));
    }
    error_log(print_r($result, TRUE));
    print_r($result);
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "subscribehandler.php recieved nothing";
  }

}


?>
