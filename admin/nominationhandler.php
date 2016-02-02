
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
    echo "Recieved nominations\n";
    $nominations = json_decode($_POST['nominations']);
    foreach($nominations as &$film){
      if($film->{'Veto'}=="true"){
          $sql = 'UPDATE nominations SET Frequency = Frequency + 1, Veto_For = concat(ifnull(Veto_For,""), "'.$_SESSION['Email'].',"), Proposed_By = concat(ifnull(Proposed_By,""), "'.$_SESSION['Email'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
          $result = $conn->query($sql);
          if($conn->affected_rows==0){
            // film is not already in the list.
            $sql = 'INSERT INTO nominations VALUES ("'.$film->{'Title'}.'",'.$film->{'Year'}.', 1, "'.$_SESSION{'Email'}.',","'.$_SESSION{'Email'}.',");';
            $result = $conn->query($sql);
            if($result == 1){
              echo "Added a new film";
            }else{
              echo "Error: Adding a new film went wrong";
            }
          }else if($conn->affected_rows==1){
            echo "Updated film";
          }else{
            echo "Error: Something went wrong updating the film";
          }
      }else{
        $sql = 'UPDATE nominations SET Frequency = Frequency + 1, Proposed_By = concat(ifnull(Proposed_By,""), "'.$_SESSION['Email'].',") WHERE Film_Name="'.$film->{'Title'}.'" AND Year='.$film->{'Year'}.';';
        $result = $conn->query($sql);
        if($conn->affected_rows==0){
          // film is not already in the list.
          $sql = 'INSERT INTO nominations VALUES ("'.$film->{'Title'}.'",'.$film->{'Year'}.', 1,"'.$_SESSION{'Email'}.',","");';
          $result = $conn->query($sql);
          if($result == 1){
            echo "Added a new film";
          }else{
            echo "Error: Adding a new film went wrong";
          }
        }else if($conn->affected_rows==1){
          echo "Updated film";
        }else{
          echo "Error: Something went wrong updating the film";
        }
      }
    }
  }else{
    echo "Error: Nothing recieved";
  }

}


 ?>
