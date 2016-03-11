<?php
ob_start();
require '../setup.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(isset($_GET['wants'])){
  $wants=$_GET['wants'];
  if($wants == 'endpoints') {
    $endpoints = array();
    if(loginCheck($session)){
      $endpoints = get_user_endpoints($_SESSION['ID']);
    }
    echo json_encode($endpoints);
  } else if($wants == 'notification') {
    if(in_array("Voting_End", get_event(1800))){
      echo '{"title": "Film Night", "body": "Voting closes in half an hour. You still haven\'t voted.", "url": "voting.php"}';
    }else if(in_array("Voting_End", get_event(3600))){
      echo '{"title": "Film Night", "body": "Voting closes in an hour. You still haven\'t voted.", "url": "voting.php"}';
    }else{
      $now_events = get_event();
      if(in_array("Roll_Call_Start", $now_events)) {
          echo '{"title": "Film Night", "body": "Coming to film night this week? Roll Call!", "url": "settings.php"}';
      } else if(in_array("Voting_Start", $now_events)) {
          echo '{"title": "Film Night", "body": "Voting is open.", "url": "voting.php"}';
      } else if(in_array("Results_Start", $now_events)) {
          echo '{"title": "Film Night", "body": "The time is now. Results are available.", "url": "results.php"}';
      } else {
          echo '{"title": "Film Night", "body": "You have a notification but nothing is happening.", "url": "index.php"}';
      }
    }
  } else if($wants == "films") {
    $filmnight_id = getCurrentFilmNight();
    $selections = [];
    if(loginCheck($session)){
      $selections = getSelectedFilmsInUserOrder($filmnight_id, $_SESSION['ID']);
    }
    echo json_encode(["status" => "success", "filmList" => $selections, "hasVoted" => $selections[1]["voted"] == "1"]);
  } else if($wants == "votes") {
    $results = [];
    if (loginCheck($session)=="admin" && status()=="voting" && !isset($_GET["night"])){
      $results = getResults(getCurrentFilmNight());
    }else{
      $results = getResults(getCurrentResultsFilmNight());
    }
    echo json_encode(["status" => "success", "votes" => $results]);
  } else {
    echo '{"status": "error", "error": "Don\'t know how to get '.$wants.'"}';
  }
} else {
  echo '{"status": "error", "error": "Nothing Received"}';
}
?>
