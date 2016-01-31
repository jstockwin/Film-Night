<?php
require '../header.php';
require $root.'../../database.php';

$conn = new mysqli($host, $username, $password, "films");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM selected_films";
$result = $conn->query($sql);

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
      $result2 = $conn->query($sql2);
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
$conn->close();
 ?>
