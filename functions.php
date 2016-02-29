<?php

function dbconnect(){
  // Ensures that there is a database connection in $GLOBALS['conn']
  static $conn;

  if(!isset($conn)) {
    include $GLOBALS['root'].'../../database.php';
    $conn = new mysqli($host, $username, $password, "films");
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
  if(session_status() == PHP_SESSION_NONE){
    session_start();
  }
  if($session == "dev"){
    $_SESSION['ID'] = "debug@example.com";
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
  // Get users who aren't attending
  $absent = [];
  $sql = "SELECT * FROM users WHERE attending=0";
  $result = query($sql);
  while($row = $result->fetch_assoc()){
    array_push($absent, $row['ID']);
  }


  $sql = "SELECT * FROM nominations WHERE Frequency>0";
  $result = query($sql);
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
      $result = query($sql);

      foreach($selected as &$film){
        $sql = 'INSERT INTO selected_films VALUES ("'.$film[0].'", '.$film[1].',NULL,NULL,NULL,NULL)';
        $result = query($sql);
        $ok = TRUE;
        if (!$result == 1){
          $ok = FALSE;
        }
      }
      if($ok){
        imdb();
      }

    }else{
      echo "Not enough available films";
    }
  }else{
    echo "No films found.";
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
      array_push($times, array($row['ID'], $row["Roll_Call_Start"], $row["Roll_Call_End"], $row["Voting_Start"], $row["Voting_End"], $row["Results_Start"], $row["Results_End"]));
    }
    return $times;
  }
  return "None";

}

function getUserDetails($ID){
  $sql = "SELECT * FROM users WHERE ID='".$ID."';";
  $result = query($sql);
  if($result->num_rows = 1){
    return $result->fetch_assoc();
  }
}

function nominateFilm($film){
  // Currently we need $conn like this as we call $conn->affected_rows.
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    $_SESSION['ERROR']="nominationhandler.php failed connect to sql database: ".$conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  $sql = 'SELECT * FROM nominations WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'};
  $result = $conn->query($sql);
  $proposed=FALSE;
  $veto=FALSE;
  if ($result->num_rows > 0){
    // Check if user has already proposed the film.
    while ($row=$result->fetch_assoc()) {
      if(strpos($row['Proposed_By'],$_SESSION['ID'])!==FALSE){
        $proposed=TRUE;
        if(strpos($row['Veto_For'],$_SESSION['ID'])!==FALSE){
          $veto=TRUE;
          // We might need the current veto string:
          $veto_string = $row['Veto_For'];
        }
      }
    }
  }
  if($proposed){
    echo "User has already added film ".$film->{'Title'}."\n";
    if($veto && $film->{'Veto'}=="false"){
      // User no longer wants to veto this film
      $sql = 'UPDATE nominations SET Veto_For = "'.str_replace($_SESSION['ID'].",","",$veto_string).'" WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
      $result = $conn->query($sql);
      if($conn->affected_rows==1){
        echo "Removed veto from film: ".$film->{'Title'}."\n";
      }else{
        echo "Error: Something went wrong removing veto from film: ".$film->{'Title'}."\n";
        $_SESSION['ERROR'] = "Error: Something went wrong removing veto from film: ".$film->{'Title'}."<br> SQL Call:<br>".$sql."<br>result<br>".$result;
      }
    }else if(!$veto && $film->{'Veto'}=="true"){
      // User wants to add a veto to this film.
      $sql = 'UPDATE nominations SET Veto_For = concat(ifnull(Veto_For,""), "'.$_SESSION['ID'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
      $result = $conn->query($sql);
      if($conn->affected_rows==1){
        echo "Added veto to film: ".$film->{'Title'}."\n";
      }else{
        echo "Error: Something went wrong adding veto to film: ".$film->{'Title'}."\n";
        $_SESSION['ERROR'] = "Error: Something went wrong addomg veto to film: ".$film->{'Title'}."<br> SQL Call:<br>".$sql."<br>result<br>".$result;
      }
    }else{
      echo "There is nothing to change for film: ".$film->{'Title'}."\n";
    }
  }else{
    if($film->{'Veto'}=="true"){
      $sql = 'UPDATE nominations SET Frequency = Frequency + 1, Veto_For = concat(ifnull(Veto_For,""), "'.$_SESSION['ID'].',"), Proposed_By = concat(ifnull(Proposed_By,""), "'.$_SESSION['ID'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
      $result = $conn->query($sql);
      if($conn->affected_rows==0){
        // film is not already in the list.
        $sql = 'INSERT INTO nominations (Film_Name, Year, Frequency, Proposed_By, Veto_For) VALUES ("'.$film->{'Title'}.'",'.$film->{'Year'}.', 1, "'.$_SESSION{'ID'}.',","'.$_SESSION{'ID'}.',");';
        $result = $conn->query($sql);
        if($result == 1){
          echo "Added a new film: ".$film->{'Title'}."\n";
        }else{
          echo "Error: Adding a new film went wrong";
          $_SESSION['ERROR'] = "Error: Something went wrong adding film: ".$film->{'Title'}."<br> SQL Call:<br>".$sql."<br>result<br>".$result;
        }
      }else if($conn->affected_rows==1){
        echo "Updated film: ".$film->{'Title'}."\n";
      }else{
        echo "Error: Something went wrong updating the film: ".$film->{'Title'}."\n";
        $_SESSION['ERROR'] = "Error: Something went wrong updating film: ".$film->{'Title'}."<br> SQL Call:<br>".$sql."<br>result<br>".$result;
      }
    }else{
      $sql = 'UPDATE nominations SET Frequency = Frequency + 1, Proposed_By = concat(ifnull(Proposed_By,""), "'.$_SESSION['ID'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
      $result = $conn->query($sql);
      if($conn->affected_rows==0){
        // film is not already in the list.
        $sql = 'INSERT INTO nominations (Film_Name, Year, Frequency, Proposed_By, Veto_For) VALUES ("'.$film->{'Title'}.'",'.$film->{'Year'}.', 1,"'.$_SESSION{'ID'}.',","");';
        $result = $conn->query($sql);
        if($result == 1){
          echo "Added a new film: ".$film->{'Title'}."\n";
        }else{
          echo "Error: Adding a new film went wrong: ".$film->{'Title'}."\n";
          $_SESSION['ERROR'] = "Error: Something went wrong adding film: ".$film->{'Title'}."<br> SQL Call:<br>".$sql."<br>result<br>".$result;
        }
      }else if($conn->affected_rows==1){
        echo "Updated film";
      }else{
        echo "Error: Something went wrong updating the film: ".$film->{'Title'}."\n";
        $_SESSION['ERROR'] = "Error: Something went wrong updating film: ".$film->{'Title'}."<br> SQL Call:<br>".$sql."<br>result<br>".$result;
      }
    }
  }
}

function getResults(){
  query("SELECT * FROM votes");
}

function getIncomingResults(){
  query("SELECT * FROM incomingvotes");
}


?>
