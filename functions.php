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
  $result = query("SELECT * FROM endpoints WHERE ID='$user';");
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
  $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Active=1 AND ".$field."=1;";
  $result = query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
    }
  }
  if($event == "Voting_End60"){
    $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Attending=1 AND Reminder_Voting60=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
        }
      }
    }
  }
  if($event == "Voting_End30"){
    $sql = "SELECT * FROM users INNER JOIN endpoints USING (ID) WHERE Attending=1 AND Reminder_Voting30=1";
    $result = query($sql);
    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()){
        $sql2 = "SELECT * FROM incomingvotes WHERE ID='".$row['ID']."'";
        $result2 = query($sql2);
        if($result2->num_rows == 0){
          // Then the user has not yet voted.
          array_push($endpoints, array("Identifier" => $row['Identifier'], "ID" => $row['ID'], "Name" => $row['Name'], "Endpoint" => $row['Endpoint']));
        }

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
  $filmnight_id = getCurrentFilmNight();
  echo $filmnight_id;
  // Sometimes selection happens multiple times per film night. This is usually before anyone has voted,
  // but we'll remove any votes just in case.
  $ids = query("SELECT id FROM selections WHERE filmnight_id=$filmnight_id");
  while($id = $ids->fetch_assoc()['id']){
    echo $id;
    query("DELETE FROM votes WHERE selection_id=$id");
  }
  // Now the votes have gone, we can delete existing films in selections
  query("DELETE FROM selections WHERE filmnight_id=$filmnight_id");

  // Finally, select 5 new films and add them into selections.
  // Could this be done as a SELECT INTO selections?
  $films = query("SELECT nominations.* FROM nominations INNER JOIN proposals ON nominations.id = proposals.film_id AND nominations.Frequency<>0 INNER JOIN users on users.id = proposals.user_id GROUP BY film_id HAVING NOT SUM(is_veto AND NOT attending) ORDER BY RAND() LIMIT 5;");
  while($row = $films->fetch_assoc()){
    $film_id = $row['id'];
    query("INSERT INTO selections VALUES (NULL, '$film_id', $filmnight_id)");
  }
}

function resetAttendence(){
  query("UPDATE users SET Attending=1 WHERE Active=1");
}

function resetVotes(){
  query("DROP TABLE votes");
  query("ALTER TABLE incomingvotes RENAME TO votes");
  query("CREATE TABLE incomingvotes (ID varchar(127), Vote varchar(255))");
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
  query('REPLACE INTO nominations VALUES ("'.$film['id'].'","'.$film['Title'].'",'.$film['Year'].',1,"'.$film['metascore'].'","'.$film['imdbscore'].'","'.$film['plot'].'","'.$film['poster'].'")');
  query("REPLACE INTO proposals VALUES ('$proposer', '".$film['id']."', '".$film['veto']."')");
}

function getResults($filmnight_id){
  $voters = query("SELECT DISTINCT user_id FROM `votes` INNER JOIN selections ON votes.selection_id=selections.id INNER JOIN nominations ON selections.film_id = nominations.id WHERE filmnight_id=$filmnight_id");
  $votes = [];
  while($voter = $voters->fetch_assoc()['user_id']){
    array_push($votes, getUserVotes($voter));
  }
  return '['.implode(',', $votes).']';
}



function sessionStart(){
  if(session_status()== PHP_SESSION_NONE){
    session_start();
  }
}

function getSelectedFilms($filmnight_id){
  return query("SELECT * FROM `selections`  INNER JOIN nominations WHERE selections.film_id = nominations.id AND selections.filmnight_id=$filmnight_id");
}

function getUserVotes($filmnight_id,$ID){
  $filmnight_id = getCurrentFilmNight();
  $votes = query("SELECT Film_Name, position FROM `votes` INNER JOIN selections ON votes.selection_id=selections.id INNER JOIN nominations ON selections.film_id = nominations.id WHERE filmnight_id=$filmnight_id AND user_id='$ID'");
  if($votes->num_rows > 0){
    // user has voted.
    while($entry = $votes -> fetch_assoc()){
      $vote[$entry['Film_Name']] = $entry['position'];
    }
    return(json_encode($vote));
  }else{
    return "FALSE";
  }

}

function getCurrentFilmNight(){
  $mostRecentNight = query("SELECT id FROM timings WHERE Roll_Call_End < NOW() ORDER BY Roll_Call_End DESC LIMIT 1");
  return $mostRecentNight->fetch_object()->id;
}

function getCurrentResultsFilmNight(){
  $mostRecentNight = query("SELECT id FROM timings WHERE Results_Start < NOW() ORDER BY Voting_End DESC LIMIT 1");
  return $mostRecentNight->fetch_object()->id;
}

function withdrawVotes($filmnight_id, $user_id){
  $result = query("DELETE votes.* FROM `votes` INNER JOIN selections ON votes.selection_id = selections.id AND user_id='$user_id' AND filmnight_id=$filmnight_id");
  if($result==1){
    echo "successfully removed vote";
  }else{
    echo "Error: There was an error submitting to the database: ".$sql2;
    echo "Returned: ".$result2;
    $_SESSION['Error'] = "There was an error submitting to the database: ".$sql2;
  }
}

function addVote($filmnight_id, $user_id, $vote){
  $result = query("SELECT selections.id,Film_Name FROM `selections` INNER JOIN nominations ON nominations.id = selections.film_id AND selections.filmnight_id=$filmnight_id");
  while($row = $result->fetch_assoc()){
    $position = $vote[rawurlencode($row['Film_Name'])];
    $selection_id = $row['id'];
    $result2 = query("REPLACE INTO votes VALUES ('$user_id', $selection_id, $position)");
    if($result2 != 1){
      echo "Error: Failed to update vote";
      $_SESSION['Error'] = "Failed to update vote";
    }
  }
}


?>
