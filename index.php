<?php require_once 'google.php';  require_once 'functions.php'; require_once "../override.php"; if(!loginCheck($session)) : ?>
  <div class="g-signin2" data-onsuccess="onSignIn"></div>
<?php else : ?>
  <a href="#" onclick="signOut();">Sign out</a>

  <?php
  require '../../database.php';

  // Create connection
  $conn = new mysqli($host, $username, $password, "films");

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  echo "Connected successfully";
  echo "<br>";


  echo "This week's films are:<br>";

  $sql = "SELECT * FROM selected_films";
  $result = $conn->query($sql);

  if ($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      echo $row["Film"]."<br>";
    }
  }else{
    echo "There are no films.";
  }
  $conn->close();
  ?>


<?php endif; ?>
