<?php
ob_start();
require '../setup.php';
require $GLOBALS['root'].'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: User not logged in";
  $_SESSION['ERROR']="votinghandler.php failed to confirm that you were logged in";
}else{
  if(isset($_POST['votes'])){
    if($_POST['votes'] == "WITHDRAW"){
      withdrawVotes(getCurrentFilmNight(), $_SESSION['ID']);
    }else{
      $vote = $_POST['votes'];

      echo "Vote: ".$_POST['votes'];

      // Sanitise votes
      $continue = TRUE;
      $jsonVote = json_decode($vote, TRUE);
      $filmnight_id = getCurrentFilmNight();
      $sql = "SELECT selections.id, films.title FROM selections INNER JOIN films ON film_id = films.id WHERE filmnight_id = $filmnight_id";
      $result = query($sql);
      $selectedFilms = [];
      $num_rows = $result->num_rows;
      if($num_rows > 0){
        while($row = $result->fetch_assoc()){
            $selectedFilms[$row['title']] =  $row['id'];
        }
      }
      error_log(print_r($selectedFilms, TRUE));
      error_log(print_r($jsonVote, TRUE));

      $idVote = [];

      if(sort(array_keys($jsonVote)) != sort(array_keys($selectedFilms))) {
        $continue = FALSE;
        $_SESSION['ERROR'] = "Error: Failed to validate your vote.<br>Your list of films doesn't match our list of films<br>$vote";
        echo "Error: Failed to validate your vote: bad films";
      }

      if(sort(array_values($jsonVote)) != range(1,$num_rows)) {
        $continue = FALSE;
        $_SESSION['ERROR'] = "Error: Failed to validate your vote.<br>You didn't give the correct positions.<br>$vote";
        echo "Error: Failed to validate your vote: bad position";
      }

      foreach ($selectedFilms as $film => $filmid) {
        $idVote[$filmid] = $jsonVote[$film];
      }

      if($continue){
        addVote(getCurrentFilmNight() ,$_SESSION['ID'], $idVote);
      }
    }
  }
}
?>
