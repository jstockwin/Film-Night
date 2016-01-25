<?php
  require_once '../header.php';
  require_once $root.'../../database.php';


  if (loginCheck($session)=="admin"){

  // Create connection
  $conn = new mysqli($host, $username, $password, "films");

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  echo "Connected successfully";
  echo "<br>";

  echo "List of nominated films:<br>";

  $sql = "SELECT * FROM nominations";
  $result = $conn->query($sql);

  echo "<table>";
  while($row = $result->fetch_assoc()){
    echo "<tr>";
    echo "<td>".$row["Film_Name"]."</td><td>".$row["Year"]."</td><td>".$row["Frequency"]."</td><td>".$row["Proposed_By"]."</td><td>".$row["Veto_For"]."</td>";
    echo "</tr>";
  }
  echo "</table>";



  $conn->close();
}else{
  echo "You are not authorised to use this page";
}
?>
