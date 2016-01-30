<?php

$numFilms = 5;


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

  // Get users who aren't attending
  $absent = [];
  $sql = "SELECT * FROM users WHERE attending=0";
  $result = $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
    array_push($absent, $row['ID']);
  }


  $sql = "SELECT * FROM nominations WHERE Frequency>0";
  $result = $conn->query($sql);
  $films = [];
    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
          $vetos = explode(",", $row['Veto_For']);
          $veto = FALSE;
          foreach($vetos as &$v){
            if(in_array($v, $absent)){
              $veto=TRUE;
            }
          }
          if(!$veto){
              array_push($films, array($row['Film_Name'],$row['Year']));
          }
        }
        $numbers = [];
        if(sizeof($films)>=$numFilms){
          for ($i = 0; $i <$numFilms; $i++){
            while(true){
              $x = rand(0,sizeof($films)-1);
              if(!in_array($x, $numbers)){
                array_push($numbers, $x);
                break;
              }
            }
          }
          $selected = [];
          foreach($numbers as &$j){
            array_push($selected, $films[$j]);
          }
          print_r($selected);
          $sql = "DELETE FROM selected_films";
          $result = $conn->query($sql);

          foreach($selected as &$film){
            $sql = 'INSERT INTO selected_films VALUES ("'.$film[0].'", '.$film[1].',NULL,NULL,NULL,NULL)';
            $result = $conn->query($sql);
            $ok = TRUE;
            if (!$result == 1){
              $ok = FALSE;
            }
          }
          if($ok){
            header("location: imdb.php");
          }

        }else{
          echo "Not enough available films";
        }
    }else{
      echo "No films found.";
    }

}

 ?>
