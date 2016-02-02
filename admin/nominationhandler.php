
<?php
ob_start();
require '../header.php';
require $root.'../../database.php';
ob_end_clean(); // supresses output.

if(!loginCheck($session)){
  echo "Error: You're not signed in";
}else{
  $conn = new mysqli($host, $username, $password, "films");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  if(isset($_POST['nominations'])){
    $nominations = json_decode($_POST['nominations']);
    foreach($nominations as &$film){
      $sql = 'SELECT * FROM nominations WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'};
      $result = $conn->query($sql);
      $proposed=FALSE;
      $veto=FALSE;
      if ($result->num_rows > 0){
        // Check if user has already proposed the film.
        while ($row=$result->fetch_assoc()) {
          if(strpos($row['Proposed_By'],$_SESSION['Email'])!==FALSE){
            $proposed=TRUE;
            if(strpos($row['Veto_For'],$_SESSION['Email'])!==FALSE){
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
          $sql = 'UPDATE nominations SET Veto_For = "'.str_replace($_SESSION['Email'].",","",$veto_string).'" WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
          $result = $conn->query($sql);
          if($conn->affected_rows==1){
            echo "Removed veto from film: ".$film->{'Title'}."\n";
          }else{
            echo "Error: Something went wrong removing veto from film: ".$film->{'Title'}."\n";
          }
        }else if(!$veto && $film->{'Veto'}=="true"){
          // User wants to add a veto to this film.
          $sql = 'UPDATE nominations SET Veto_For = concat(ifnull(Veto_For,""), "'.$_SESSION['Email'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
          $result = $conn->query($sql);
          if($conn->affected_rows==1){
            echo "Added veto to film: ".$film->{'Title'}."\n";
          }else{
            echo "Error: Something went wrong adding veto to film: ".$film->{'Title'}."\n";
          }
        }else{
          echo "There is nothing to change for film: ".$film->{'Title'}."\n";
        }
      }else{
        if($film->{'Veto'}=="true"){
          $sql = 'UPDATE nominations SET Frequency = Frequency + 1, Veto_For = concat(ifnull(Veto_For,""), "'.$_SESSION['Email'].',"), Proposed_By = concat(ifnull(Proposed_By,""), "'.$_SESSION['Email'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
          $result = $conn->query($sql);
          if($conn->affected_rows==0){
            // film is not already in the list.
            $sql = 'INSERT INTO nominations VALUES ("'.$film->{'Title'}.'",'.$film->{'Year'}.', 1, "'.$_SESSION{'Email'}.',","'.$_SESSION{'Email'}.',");';
            $result = $conn->query($sql);
            if($result == 1){
              echo "Added a new film: ".$film->{'Title'}."\n";
            }else{
              echo "Error: Adding a new film went wrong";
            }
          }else if($conn->affected_rows==1){
            echo "Updated film: ".$film->{'Title'}."\n";
          }else{
            echo "Error: Something went wrong updating the film: ".$film->{'Title'}."\n";
          }
        }else{
          $sql = 'UPDATE nominations SET Frequency = Frequency + 1, Proposed_By = concat(ifnull(Proposed_By,""), "'.$_SESSION['Email'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
          $result = $conn->query($sql);
          if($conn->affected_rows==0){
            // film is not already in the list.
            $sql = 'INSERT INTO nominations VALUES ("'.$film->{'Title'}.'",'.$film->{'Year'}.', 1,"'.$_SESSION{'Email'}.',","");';
            $result = $conn->query($sql);
            if($result == 1){
              echo "Added a new film: ".$film->{'Title'}."\n";
            }else{
              echo "Error: Adding a new film went wrong: ".$film->{'Title'}."\n";
            }
          }else if($conn->affected_rows==1){
            echo "Updated film";
          }else{
            echo "Error: Something went wrong updating the film: ".$film->{'Title'}."\n";
          }
        }
      }
    }
  }else{
    echo "Error: Nothing recieved";
  }

}


?>
