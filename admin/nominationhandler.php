
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
    $nominations = json_decode($_POST['nominations'], TRUE);
    foreach($nominations as &$film){
      $json = file_get_contents("http://www.omdbapi.com/?t=".urlencode($film['Title'])."&y=".$film['Year']);
      $output = json_decode($json);
      $Response = $output->{'Response'};
      if($Response=="True"){
        // Film found
        $film['id'] = $output->{'imdbID'};
        $film['metascore'] = $output->{'Metascore'};
        $film['imdbscore'] = $output->{'imdbRating'};
        $film['plot'] = rawurlencode($output->{'Plot'});
        $film['poster'] = $output->{'Poster'};
        if($film['Veto']==="true"){
          $film['veto'] = 1;
        }else{
          $film['veto'] = 0;
        }
        nominateFilm($film, $_SESSION['ID']);
      }else{
        echo "Error: IMDB Pull Failed";
        $_SESSION['ERROR'] = "IMDB Pull Failed";
      }
    }
  }else{
    echo "Error: Nothing recieved";
    $_SESSION['ERROR'] = "nominationhandler.php recieved no films";
  }

}


?>
