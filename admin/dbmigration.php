<?php
include '../setup.php';
require $root.'../../database.php';


##########################
## Begin DB Duplication ##
##########################

$DB = "films";
$newDB = "films2";

$conn = new mysqli($host, $username, $password, $DB);



$DB_check = @mysql_select_db ( $DB );
$getTables  =   $conn->query("SHOW TABLES");
$tables =   array();
$i =0;
while($row = $getTables->fetch_assoc()){
  $tables[$i]   =   $row['Tables_in_films'];
  $i++;
}
if(mysql_query("DROP DATABASE IF EXISTS ".$newDB)){
  echo "New DB Created<br>";
  $createTable    =   mysql_query("CREATE DATABASE `$newDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;") or die(mysql_error());
  $conn2 = new mysqli($host, $username, $password, $newDB);
  foreach($tables as &$cTable){
    $sql = "CREATE TABLE ".$cTable." LIKE ".$DB.".".$cTable;
    $create     =   $conn2->query($sql);
    if(!$create) {
      echo "ERROR: Failed to create table ".$cTable."<br>";
    }else{
      echo "Table ".$cTable." created successfully<br>";
    }
    $insert     =   $conn2->query("INSERT INTO $cTable SELECT * FROM ".$DB.".".$cTable);
    if(!$insert) {
      echo "ERROR: Failed to migrate data for table ".$cTable."<br>";
    }else{
      echo "Data migrated for table ".$cTable."<br>";
    }
  }
}else{
  echo "Eroor: Failed to create new DB";
}

#######################
## End DB Dupliation ##
#######################

?>
