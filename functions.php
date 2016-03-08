<?php

function dbconnect(){
  static $conn;

  if(!isset($conn)) {
    include $GLOBALS['root'].'../../database.php';
    $conn = new mysqli($host, $username, $password, "films2");
  }

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  return $conn;
}

function query($query){
  // Queries the films database with $query and returns the response
  // In most cases a connection should already be initialised by the time
  // any queries are run.
  return dbconnect()->query($query);
}

function loginCheck($session = "live", $session_started = FALSE) {
  if($session == "dev"){
    $_SESSION['ID'] = "dingbatwhirr@gmail.com";
    $_SESSION['Permission'] = "admin";
    $_SESSION['Name'] = "debug";
    $_SESSION['Token'] = "ABC123";
    $_SESSION['Image'] = "assets/icons/ic_error.svg";
    return "admin";
  }elseif($session == "dev2"){
    $_SESSION['ID'] = "debug@example2.com";
    return "member";
  }else{
    if (isset($_SESSION['Permission'])){
      return $_SESSION['Permission'];
    }else{
      return FALSE;
    }
  }
}

function status(){

  $now = date('Y-m-d H:i:s');
  $sql = "SELECT * FROM timings";
  $result = query($sql);
  if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      if ($row["Roll_Call_Start"] < $now && $now < $row["Roll_Call_End"]){
        return "rollCall";
      }else if($row["Voting_Start"] < $now && $now < $row["Voting_End"]){
        return "voting";
      }else if($row["Results_Start"] < $now && $now < $row["Results_End"]){
        return "results";
      }
    }
  }
  return FALSE;
}

function get_event($advance = 0, $tol = 300){
  $sql = "SELECT * FROM timings";
  $result = query($sql);
  $events = array();
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      if (strtotime($row["Roll_Call_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Roll_Call_Start"]) + $tol){
        array_push($events,"Roll_Call_Start");
      }
      if (strtotime($row["Roll_Call_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Roll_Call_End"]) + $tol){
        array_push($events,"Roll_Call_End");
      }
      if (strtotime($row["Voting_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Voting_Start"]) + $tol){
        array_push($events,"Voting_Start");
      }
      if (strtotime($row["Voting_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Voting_End"]) + $tol){
        array_push($events,"Voting_End");
      }
      if (strtotime($row["Results_Start"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Results_Start"]) + $tol){
        array_push($events,"Results_Start");
      }
      if (strtotime($row["Results_End"]) - $tol < time() + $advance && time() + $advance < strtotime($row["Results_End"]) + $tol){
        array_push($events,"Results_End");
      }
    }
  }
  return $events;
}

function get_emails($event){
  if($event == "All"){
    $field = 1;
  }else if($event == "Roll_Call_Start"){
    $field = "Reminder_Attending";
  }else if($event == "Voting_Start"){
    $field = "Reminder_Voting";
  }else if($event == "Results_Start"){
    $field = "Reminder_Results";
  }else if($event == "No event"){
    return "Error: Nothing is happening";
  }else if($event == "Voting_End30"){
    // handled later
    $field = 0;
  }else if($event == "Voting_End60"){
    // handled later
    $field = 0;
  }else{
    return "Error: Unhandled event";
  }
  $to="";
  $sql = "SELECT Email FROM users WHERE Active=1 AND ".$field."=1;";
  $result = query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $to = $to.$row['Email'].", ";
    }
  }
  if($event == "Voting_End60"){
    $sql = "SELECT * FROM users WHERE Attending=1 AND Reminder_Voting60=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          $to = $to.$row['Email'].", ";
        }
      }
    }
  }
  if($event == "Voting_End30"){
    $sql = "SELECT * FROM users WHERE Attending=1 AND Reminder_Voting30=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          $to = $to.$row['Email'].", ";
        }

      }
    }
  }

  return $to;
}

function get_user_endpoints($user){
  $result = query("SELECT * FROM endpoints WHERE user_id='$user';");
  $endpoints = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
      array_push($endpoints, $row);
    }
  }
  return $endpoints;
}

function get_endpoints($event){
  include $GLOBALS['root'].'../../database.php';
  if($event == "All"){
    $field = 1;
  }else if($event == "Roll_Call_Start"){
    $field = "Reminder_Attending";
  }else if($event == "Voting_Start"){
    $field = "Reminder_Voting";
  }else if($event == "Results_Start"){
    $field = "Reminder_Results";
  }else if($event == "No event"){
    return "Error: Nothing is happening";
  }else if($event == "Voting_End30"){
    // handled later
    $field = 0;
  }else if($event == "Voting_End60"){
    // handled later
    $field = 0;
  }else{
    return "Error: Unhandled event";
  }
  $endpoints = array();
  $sql = "SELECT * FROM users INNER JOIN endpoints ON user_id = users.id WHERE Active=1 AND ".$field."=1;";
  $result = query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($endpoints, array("id" => $row['id'], "user_id" => $row['user_id'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
    }
  }
  if($event == "Voting_End60"){
    $filmnight_id = getCurrentFilmNight();
    $sql = "SELECT endpoints.* FROM users INNER JOIN endpoints ON endpoints.user_id = users.id LEFT JOIN (SELECT * FROM votes INNER JOIN selections ON selection_id = selections.id WHERE filmnight_id = $filmnight_id) vs ON vs.user_id = users.id WHERE ISNULL(filmnight_id) AND attending AND Reminder_Voting60";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        array_push($endpoints, $row);
      }
    }
  }
  if($event == "Voting_End30"){
    $filmnight_id = getCurrentFilmNight();
    $sql = "SELECT endpoints.* FROM users INNER JOIN endpoints ON endpoints.user_id = users.id LEFT JOIN (SELECT * FROM votes INNER JOIN selections ON selection_id = selections.id WHERE filmnight_id = $filmnight_id) vs ON vs.user_id = users.id WHERE ISNULL(filmnight_id) AND attending AND Reminder_Voting30";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        array_push($endpoints, $row);
      }
    }
  }
  return $endpoints;
}

function imdb(){
  $sql = "SELECT * FROM selected_films";
  $result = query($sql);

  if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $json = file_get_contents("http://www.omdbapi.com/?t=".urlencode($row['Film'])."&y=".$row['Year']);
      $output = json_decode($json);
      $Response = $output->{'Response'};
      if($Response=="True"){
        // Film found
        $Metascore = $output->{'Metascore'};
        $IMDb = $output->{'imdbRating'};
        $Plot = rawurlencode($output->{'Plot'});
        $Poster = $output->{'Poster'};

        $sql2 = 'UPDATE selected_films SET Metascore="'.$Metascore.'", IMDb="'.$IMDb.'", Plot="'.$Plot.'", Poster="'.$Poster.'" WHERE Film="'.$row["Film"].'";';
        $result2 = query($sql2);
        if ($result2 == 1){
          // Updated successfully
          echo $row['Film']." updated successfully<br>";
        }else{
          // something wen't wrong with sql.
          echo $row['Film']." caused an sql error.<br>Called: ".$sql2.".<br>sql returned ".$result2."\n";
        }
      }else{
        echo $row['Film']." not found on omdb<br>";
      }
    }
  }else{
    echo "There are no films.";
  }
}

function selectFilms(){
  $numFilms = 5;
  $filmnight_id = getCurrentFilmNight(5);
  echo $filmnight_id;
  // Sometimes selection happens multiple times per film night. This is usually before anyone has voted,
  // but we'll remove any votes just in case.
  query("DELETE votes FROM votes INNER JOIN selections ON selection_id = selections.id WHERE filmnight_id=$filmnight_id");
  // Now the votes have gone, we can delete existing films in selections
  query("DELETE FROM selections WHERE filmnight_id=$filmnight_id");

  // Finally, select 5 new films and add them into selections.
  query("INSERT INTO selections
    SELECT NULL, films.id, $filmnight_id
    FROM proposals
    INNER JOIN users ON users.id = proposals.user_id
    RIGHT JOIN films ON films.id = proposals.film_id
    WHERE enabled
    GROUP BY films.id
    HAVING IFNULL(NOT SUM(is_veto AND NOT attending), TRUE)
    ORDER BY RAND()
    LIMIT 5;");
}

function resetAttendence(){
  query("UPDATE users SET Attending=1 WHERE Active=1");
}

function getFilmNights(){
  // Returns an array of timings
  $times = [];
  $sql = "SELECT * FROM timings";
  $result = query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($times, array($row['id'], $row["Roll_Call_Start"], $row["Roll_Call_End"], $row["Voting_Start"], $row["Voting_End"], $row["Results_Start"], $row["Results_End"]));
    }
    return $times;
  }
  return "None";

}

function getUserDetails($ID){
  $sql = "SELECT * FROM users WHERE id='$ID';";
  $result = query($sql);
  if($result->num_rows == 1){
    return $result->fetch_assoc();
  }else{
    return "Error";
  }
}

function nominateFilm($film,$proposer){
  // Currently we need $conn like this as we call $conn->affected_rows.
  query('REPLACE INTO films VALUES ("'.$film['id'].'","'.$film['title'].'",'.$film['year'].',"'.$film['metascore'].'","'.$film['imdbscore'].'","'.$film['plot'].'","'.$film['poster'].'", 1)');
  query("REPLACE INTO proposals VALUES ('$proposer', '".$film['id']."', '".$film['veto']."')");
}

function getResults($filmnight_id){
  $voters = query("SELECT DISTINCT user_id FROM `votes` INNER JOIN selections ON votes.selection_id=selections.id INNER JOIN films ON selections.film_id = films.id WHERE filmnight_id=$filmnight_id");
  $votes = [];
  while($voter = $voters->fetch_assoc()['user_id']){
    array_push($votes, getUserVotes($filmnight_id, $voter));
  }
  return '['.implode(',', $votes).']';
}



function sessionStart(){
  if(session_status()== PHP_SESSION_NONE){
    session_start();
  }
}

function getSelectedFilmsInUserOrder($filmnight_id, $user_id){
  $result = query("SELECT films.*, !ISNULL(position) AS voted
    FROM selections
    LEFT JOIN votes ON selection_id = selections.id AND user_id = '$user_id'
    INNER JOIN films ON film_id = films.id WHERE filmnight_id = $filmnight_id
    ORDER BY position, selections.id");

  $selections = [];
  if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($selections, $row);
    }
  }
  return $selections;
}

function getSelectedFilms($filmnight_id){
  $result = query("SELECT * FROM selections INNER JOIN films ON film_id = films.id WHERE filmnight_id=$filmnight_id");

  $selections = [];
  if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($selections, $row);
    }
  }
  return $selections;
}

function getUserVotes($filmnight_id, $ID){
  $votes = query("SELECT title, position FROM votes INNER JOIN selections ON votes.selection_id=selections.id INNER JOIN films ON film_id = films.id WHERE filmnight_id=$filmnight_id AND user_id='$ID'");
  if($votes->num_rows > 0){
    // user has voted.
    while($entry = $votes -> fetch_assoc()){
      $vote[$entry['title']] = intval($entry['position']);
    }
    return(json_encode($vote));
  }else{
    return "FALSE";
  }

}

function getCurrentFilmNight($tolerance=0){
  $mostRecentNight = query("SELECT id FROM timings WHERE Roll_Call_End < DATE_ADD(NOW(), INTERVAL $tolerance MINUTE) ORDER BY Roll_Call_End DESC LIMIT 1");
  return $mostRecentNight->fetch_object()->id;
}

function getCurrentResultsFilmNight(){
  if(isset($_GET["night"]) && is_numeric($_GET["night"])) {
    return intval($_GET["night"]);
  }
  $mostRecentNight = query("SELECT id FROM timings WHERE Results_Start < NOW() ORDER BY Voting_End DESC LIMIT 1");
  return $mostRecentNight->fetch_object()->id;
}

function withdrawVotes($filmnight_id, $user_id){
  $result = query("DELETE votes FROM votes INNER JOIN selections ON votes.selection_id = selections.id WHERE user_id='$user_id' AND filmnight_id=$filmnight_id");
  if($result==1){
    echo "successfully removed vote";
  }else{
    echo "Error: There was an error submitting to the database: ".$sql2;
    echo "Returned: ".$result2;
    $_SESSION['Error'] = "There was an error submitting to the database: ".$sql2;
  }
}

function addVote($filmnight_id, $user_id, $vote){
  foreach($vote as $selection_id => $position){
    $result2 = query("REPLACE INTO votes VALUES ('$user_id', $selection_id, $position)");
    if(!$result2){
      echo "Error: Failed to update vote";
      $_SESSION['Error'] = "Failed to update vote";
    }
  }
}


?>
