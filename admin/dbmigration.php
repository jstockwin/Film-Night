<?php
include '../setup.php';
require $GLOBALS['root'].'../../database.php';


##########################
## Begin DB Duplication ##
##########################

$DB = "films";
$newDB = "films2";

$conn = new mysqli($host, $username, $password, $DB);

$getTables = $conn->query("SHOW TABLES");
$tables = array();
$i =0;
while($row = $getTables->fetch_assoc()){
  $tables[$i] = $row['Tables_in_films'];
  $i++;
}

if($conn->query("DROP DATABASE IF EXISTS `$newDB`")){
  echo "New DB Created\n";
  $createTable = $conn->query("CREATE DATABASE `$newDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;") or die(mysql_error());
  $conn2 = new mysqli($host, $username, $password, $newDB);
  foreach($tables as &$cTable){
    $sql = "CREATE TABLE $cTable LIKE $DB.$cTable;";
    $create = $conn2->query($sql);
    if(!$create) {
      echo "ERROR: Failed to create table $cTable\n";
    }else{
      echo "Table $cTable created successfully\n";
    }
    $utf8 = $conn2->query("ALTER TABLE $cTable CONVERT TO CHARACTER SET utf8;");
    
    $insert = $conn2->query("INSERT INTO $cTable SELECT * FROM $DB.$cTable");
    if(!$insert) {
      echo "ERROR: Failed to migrate data for table $cTable\n";
    }else{
      echo "Data migrated for table $cTable\n";
    }
  }
  
  //DONE DUPLICATING
    
  //FILM ID CHANGES
  
  $conn2->query("ALTER TABLE nominations ADD COLUMN id VARCHAR(15) FIRST");
  $conn2->query("ALTER TABLE nominations ADD COLUMN metascore VARCHAR(7)");
  $conn2->query("ALTER TABLE nominations ADD COLUMN imdbscore VARCHAR(7)");
  $conn2->query("ALTER TABLE nominations ADD COLUMN plot VARCHAR(1023)");
  $conn2->query("ALTER TABLE nominations ADD COLUMN poster VARCHAR(15)");
  
  $sql = "SELECT * FROM nominations";
  $result = $conn2->query($sql);

  if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $film = $row["Film_Name"];
      $json = file_get_contents("http://www.omdbapi.com/?t=".urlencode($film)."&y=".$row['Year']);
      $output = json_decode($json);
      $Response = $output->{'Response'};
      if($Response=="True"){
        // Film found
        $id = $output->{'imdbID'};
        $Metascore = $output->{'Metascore'};
        $IMDb = $output->{'imdbRating'};
        $Plot = rawurlencode($output->{'Plot'});
        $Poster = $output->{'Poster'};

        $sql2 = 'UPDATE nominations SET id="'.$id.'", metascore="'.$Metascore.'", imdbscore="'.$IMDb.'", plot="'.$Plot.'", poster="'.$Poster.'" WHERE Film_Name = "'.$film.'";';
        $result2 = $conn2->query($sql2);
        if ($result2 == 1){
          echo $row['Film_Name']." updated successfully\n";
        }else{
          echo $row['Film_Name']." caused an sql error.\nCalled: ".$sql2.".\nsql returned ".$result2."\n";
        }
      }else{
        echo $row['Film_Name']." not found on omdb\n";
      }
    }
  }else{
    echo "There are no films.";
  }
  //get rid of the weird Chloe film from 1960 with no name.
  //I'm told this was a botched accidental nomination of Ocean's 11.
  $conn2->query("DELETE FROM nominations WHERE ISNULL(id);");
  
  //DONE WITH FILMS
  
  //ADD IDs TO OTHER TABLES  
  foreach($tables as &$cTable){
    if($cTable == "endpoints" || $cTable == "votes" || $cTable == "incomingvotes")
    {
      $changeID =   $conn2->query("ALTER TABLE $cTable CHANGE COLUMN ID user_id VARCHAR(255) ");
      if(!$changeID) {
        echo "ERROR: Failed to add column user_id for table $cTable\n";
      }else{
        echo "Added user_id for table $cTable\n";
      }
      $conn2->query("ALTER TABLE $cTable ADD FOREIGN KEY (user_id) REFERENCES users(id);");
    }
    if($cTable == "endpoints") {
      $sql = "ALTER TABLE $cTable CHANGE COLUMN Identifier id int AUTO_INCREMENT";
    } else if($cTable == "timings") {
      $sql = "ALTER TABLE $cTable CHANGE COLUMN ID id int AUTO_INCREMENT";
    } else if($cTable == "nominations") {
      $sql = "ALTER TABLE $cTable ADD PRIMARY KEY (id)";
    } else if($cTable == "users") {
      $sql = "ALTER TABLE $cTable CHANGE COLUMN ID id VARCHAR(127) PRIMARY KEY";
    } else {
      $sql = "ALTER TABLE $cTable ADD COLUMN id int AUTO_INCREMENT PRIMARY KEY FIRST";
    }
    $changeID = $conn2->query($sql);
    if(!$changeID) {
      echo "ERROR: Failed to change or add id column for table $cTable\n";
    }else{
      echo "Added or changed id for table $cTable\n";
    }
  }
  
  //DONE ADDING IDs
  
  //CREATE PROPOSALS
  
  $createProposals = $conn2->query("CREATE TABLE proposals (
    user_id VARCHAR(127),
    film_id VARCHAR(15),
    is_veto BOOL DEFAULT FALSE,
    PRIMARY KEY (user_id, film_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (film_id) REFERENCES nominations(id)
  );");
  
  if(!$createProposals) {
    echo "ERROR: Failed to create proposals table\n";
  } else {
    echo "Table proposals created\n";
  }
  
  //POPULATE PROPOSALS

  $films = $conn2->query("SELECT * FROM nominations");
  
  if(!$films)
  {
    echo "ERROR: Failed to get films\n";
  } else {
    while($film = $films->fetch_object()) {
      $id = $film->id;
      $proposed = $film->Proposed_By;
      $veto = $film->Veto_For;
      $proposers = explode(",", $proposed);
      $vetoers = explode(",", $veto);
      foreach($proposers as $user) {
        if(strpos($user, "@") > 0) {
          $veto = in_array($user, $vetoers) ? 1 : 0;
          $conn2->query("INSERT INTO proposals VALUES ('$user', '$id', $veto)");
        }
      }
      echo "Migrated proposals for film $film->Film_Name\n";
    }
  }
  //REMOVE OLD PROPOSAL INFORMATION
  echo "Dropping old proposal information\n";
  $dropp = $conn2->query("ALTER TABLE nominations DROP COLUMN Proposed_By");
  $dropv = $conn2->query("ALTER TABLE nominations DROP COLUMN Veto_For");
  if(!($dropp && $dropv)) {
    echo "ERROR: Problem dropping old proposal informaiton\n";
  } else {
    echo "Dropped old proposal information\n";
  }
  
  //DONE WITH PROPOSALS
  
  //CREATE SELECTIONS
  
  $createSelections = $conn2->query("CREATE TABLE selections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    film_id VARCHAR(15),
    filmnight_id INT,
    FOREIGN KEY (film_id) REFERENCES nominations(id),
    FOREIGN KEY (filmnight_id) REFERENCES timings(id)
  );");
  
  if(!$createSelections) {
    echo "ERROR: Failed to create selections table\n";
  } else {
    echo "Table selections created\n";
  }
  
  //POPULATE SELECTIONS
  
  $mostRecentNight = $conn2->query("SELECT id FROM timings WHERE Roll_Call_End < NOW() ORDER BY Roll_Call_End DESC LIMIT 1");
  $nightID = $mostRecentNight->fetch_object()->id;
  echo "Most recent film night id: $nightID\n";

  $lastSelections = $conn2->query("SELECT Film FROM selected_films");
  if(!$lastSelections) {
    echo "ERROR: Failed to get selected films\n";
  } else {
    while($film = $lastSelections->fetch_object()) {
      $name = $film->Film;
      $filmID = $conn2->query("SELECT id FROM nominations WHERE Film_Name = '$name'")->fetch_object()->id;
      $success = $conn2->query("INSERT INTO selections VALUES (NULL, '$filmID', $nightID)");
      if(!$success) {
        echo "ERROR: failed to add $name to selections\n";
      } else {
        echo "Added $name to selections\n";
      }
    }
  }
  
  $dropOldSelections = $conn2->query("DROP TABLE selected_films");
  if(!$dropOldSelections) {
    echo "ERROR: Failed to drop selected_films table\n";
  } else {
    echo "Table selected_films dropped\n";
  }
  //DONE WITH SELECTIONS
  
  //RENAME OLD VOTES
  
  $conn2->query("ALTER TABLE votes RENAME TO oldvotes");
  
  //CREATE NEW VOTES
  
  $createVotes = $conn2->query("CREATE TABLE votes (
    user_id VARCHAR(127),
    selection_id INT,
    position INT,
    PRIMARY KEY (user_id, selection_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (selection_id) REFERENCES selections(id)
  )");
  
  if(!$createVotes) {
    echo "ERROR: Failed to create votes table\n";
  } else {
    echo "Table votes created\n";
  }
  
  //MIGRATE VOTES
  
  $allvotes = $conn2->query("SELECT * FROM oldvotes");
  while($votes = $allvotes->fetch_object()) {
    $user = $votes->user_id;
    $vote = json_decode($votes->Vote);
    foreach($vote as $film => $position)
    {
      $fid = $conn2->query("SELECT id FROM nominations WHERE Film_Name = '".urldecode($film)."'")->fetch_object()->id;
      $sid = $conn2->query("SELECT id FROM selections WHERE film_id = '$fid'")->fetch_object()->id;
      $success = $conn2->query("INSERT INTO votes VALUES ('$user', $sid, $position)");
      if(!$success) {
        echo "ERROR: failed to add $user's vote for ".urldecode($film)." to selections\n";
      } else {
        echo "Added $user's vote for ".urldecode($film)." to selections\n";
      }
    }
  }
  
  $conn2->query("DROP TABLE oldvotes");
  
  //DONE WITH VOTES
  
  //INCOMING VOTES IS EMPTY
  
  $conn2->query("DROP TABLE incomingvotes");
  
  //DONE WITH INCOMING VOTES
  
}else{
  echo "Error: Failed to create new DB";
}

########################
## End DB Duplication ##
########################

?>
