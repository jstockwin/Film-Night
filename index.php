<?php include 'setup.php';?>
<?php include 'head.php'; head('Film Night');?>
<body>
<?php include 'top-nav.php'; ?>
<?php if($permission != FALSE): ?>
  <p>You are currently signed in as <?php echo $_SESSION["Name"]; ?> (<?php echo $_SESSION['ID']; ?>)</p>
    <a href="#" onclick="signOut();">Sign out</a><br>

  <?php
  require $root.'../../database.php';



  // Create connection
  $conn = new mysqli($host, $username, $password, "films");

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  echo "Connected to sql database successfully";
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
</body>
</html>
