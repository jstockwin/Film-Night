
<?php
ob_start();
require '../setup.php';
require $GLOBALS['root'].'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
  $_SESSION['ERROR']="nominationhandler.php failed to verify that you are signed in";
}else{
  if(isset($_POST['nominations'])){
    $nominations = json_decode($_POST['nominations']);
    foreach($nominations as &$film){
      nominateFilm($film);
    }
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "nominationhandler.php recieved no films";
  }

}


?>
