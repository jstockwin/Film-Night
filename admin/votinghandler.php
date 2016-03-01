<?php
ob_start();
require '../setup.php';
require $GLOBALS['root'].'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: User not logged in";
  $_SESSION['ERROR']="votinghandler.php failed to confirm that you were logged in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    $_SESSION['ERROR']="votinghandler.php failed connect to sql database: ".$conn->connect_error;
  }
  if(isset($_POST['votes'])){
    if($_POST['votes'] == "WITHDRAW"){
      withdrawVotes(getCurrentFilmNight(), $_SESSION['ID']);
    }else{
      $post = file_get_contents('php://input');
      echo $post;
      $vote = str_replace("votes=","",$post);

      echo "Vote: ".$_POST['votes'];


      // Sanitise votes
      $continue = TRUE;
      $jsonVote = json_decode($vote, TRUE);
      $sql = "SELECT * FROM selected_films";
      $result = $conn->query($sql);
      $selectedFilms = [];
      $num_rows = $result->num_rows;
      if($num_rows > 0){
        while($row = $result->fetch_assoc()){
          array_push($selectedFilms, rawurlencode($row['Film']));
        }
      }
      print_r($selectedFilms);
      print_r($jsonVote);

      foreach ($selectedFilms as $film) {
        if(!isset($jsonVote[$film])){
          // Film not found (selected film is not found in vote)
          $continue = FALSE;
          $_SESSION['ERROR'] = "Error: Failed to validate your vote. <br>Not all selected films are in your vote. <br> ".$vote;
          echo "Error: Failed to validate your vote";
        }
      }
      $nums = range(1,$num_rows);
      foreach($jsonVote as $film){ // $film is actually the voting rank number.
        if(array_search(array_search($film, $jsonVote), $selectedFilms, TRUE)===FALSE){
          // Film not found (film in vote has not been selected)
          $continue = FALSE;
          $_SESSION['ERROR'] = "Error: Failed to validate your vote. <br>A film in your vote is not in selected films <br>".$vote;
          echo "Error: Failed to validate your vote";
        }
        $index = array_search($film, $nums);
        if($index===FALSE){
          // Checks vote indices are in 1,...,number of films.
          $continue = FALSE;
          $_SESSION['ERROR'] = "Error: Failed to validate your vote. <br>Invalid rank index <br>".$vote;
          echo "Error: Failed to validate your vote";
        }else{
          // Removes the number $film from list of numbers - you can't have two films with the same voting rank number.
          unset($nums[$index]);
        }
      }



      if($continue){
        addVote(getCurrentFilmNight() ,$_SESSION['ID'], $jsonVote);
      }
    }
  }
}
?>
