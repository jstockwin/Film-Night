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
  
  //USER ID CHANGES
  
  echo "Making changes to user table\n";
  $changeID = $conn2->query("ALTER TABLE users CHANGE COLUMN ID gmail VARCHAR(127) DEFAULT NULL");
  if(!$changeID) {
    echo "ERROR: Failed to change column name for table users\n";
  }else{
    echo "ID changed to gmail for table users\n";
  }
  $changeID = $conn2->query("ALTER TABLE users ADD COLUMN id int AUTO_INCREMENT PRIMARY KEY FIRST");  
  $userID = $conn2->query("SELECT id, gmail FROM users");
  $userToIdList = array();
  while($row = $userID->fetch_assoc()) {
    $userToIdList[$row["gmail"]] = $row["id"];
  }
  echo "Got user id mapping\n";
  
  //USER IDs DONE
  
  //FILM ID CHANGES
  
  $conn2->query("ALTER TABLE nominations ADD COLUMN id VARCHAR(15) FIRST");
  
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

        $sql2 = 'UPDATE nominations SET id="'.$id.'" WHERE Film_Name = "'.$film.'";';
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
  //get rid of the weird Chloe film from 1960 with no name
  $conn2->query("DELETE FROM nominations WHERE ISNULL(id);");
  
  //DONE WITH FILMS
  
  //ADD IDs TO OTHER TABLES  
  foreach($tables as &$cTable){
    if($cTable == "endpoints" || $cTable == "votes" || $cTable == "incomingvotes")
    {
      $changeID =   $conn2->query("ALTER TABLE $cTable ADD COLUMN user_id int DEFAULT NULL AFTER `ID`");
      if(!$changeID) {
        echo "ERROR: Failed to add column user_id for table $cTable\n";
      }else{
        echo "Added user_id for table $cTable\n";
      }
      foreach($userToIdList as $name => $number) {
        $conn2->query("UPDATE $cTable SET user_id=$number WHERE ID='$name'");
      }
      $conn2->query("ALTER TABLE $cTable ADD FOREIGN KEY (user_id) REFERENCES users(id);");
      $conn2->query("ALTER TABLE $cTable DROP COLUMN ID");
    }
    if($cTable == "endpoints") {
      $sql = "ALTER TABLE $cTable CHANGE COLUMN Identifier id int AUTO_INCREMENT";
    } else if($cTable == "timings") {
      $sql = "ALTER TABLE $cTable CHANGE COLUMN ID id int AUTO_INCREMENT";
    } else if($cTable == "nominations") {
      $sql = "ALTER TABLE $cTable ADD PRIMARY KEY (id);";
    } else {
      $sql = "ALTER TABLE $cTable ADD COLUMN id int AUTO_INCREMENT PRIMARY KEY FIRST";
    }
    if($cTable != "users") {$changeID =   $conn2->query($sql);}
    if(!$changeID) {
      echo "ERROR: Failed to change or add id column for table $cTable\n";
    }else{
      echo "Added or changed id for table $cTable\n";
    }
  }
  
  //DONE ADDING IDs
  
  //CREATE PROPOSALS
  
  $createProposals = $conn2->query("CREATE TABLE proposals (
    user_id INT,
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
        if(array_key_exists($user, $userToIdList)) {
          $veto = in_array($user, $vetoers) ? 1 : 0;
          $conn2->query("INSERT INTO proposals VALUES ($userToIdList[$user], '$id', $veto)");
        }
      }
    }
  }
  
  $conn2->query("ALTER TABLE nominations DROP COLUMN Proposed_By");
  $conn2->query("ALTER TABLE nominations DROP COLUMN Veto_For");
}else{
  echo "Error: Failed to create new DB";
}

########################
## End DB Duplication ##
########################

?>
