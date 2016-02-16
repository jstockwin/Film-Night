<?php include 'setup.php';?>
<?php require $root.'../../database.php'; ?>
<?php include 'head.php'; head('Film Night Results');?>
<body>
<?php include 'top-nav.php';?>
<script type="text/javascript" src="voting-systems.js"></script>
<script type="text/javascript" src="results-graphs.js"></script>
<p id="log"></p>
<div id="container">
  <div id="results">
    <div id="winner-div">
      <h3>This week's winner is</h3>
      <h2 id="winner"></h2>
    </div>
    <div id="utilities">
      <h2>Pairwise Victories</h2>
      <div id="key"></div>
      <div id="utilities-tables">
        <div id="distances"><table id="distances-table"></table></div>
        <div id="svg"></div>
      </div>
    </div>
    <div>
      <h2>Rankings</h2>
      <div id="tables">
        <div id="schulze"></div>
        <div id="copeland"></div>
        <div id="borda"></div>
        <div id="minimax"></div>
        <div id="kemenyYoung"></div>
        <div id="baldwin"></div>
        <div id="nanson"></div>
        <div id="first-past-the-post"></div>
        <div id="av"></div>
        <div id="anti-plurality"></div>
        <div id="coombs"></div>
      </div>
    </div>
  </div>
</div>
<script>
  <?php

  $conn = new mysqli($host, $username, $password, "films");

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


  /*$sql = "SELECT * FROM selected_films";
  $result = $conn->query($sql);
  if ($result->num_rows > 0){
    echo "var listOfCandidates =[";
    while($row = $result->fetch_assoc()){
      echo '"'.urldecode($row["Film"]).'",';
    }
    echo "];";
  }else{
    echo "// There are no selected films.";
  }
  echo "\n"; */
  if (loginCheck($session)=="admin" && status($root)=="voting"){
    $sql = "SELECT * FROM incomingvotes";
  }else{
    $sql = "SELECT * FROM votes";
  }

  $result = $conn->query($sql);

  if ($result->num_rows > 0){
    echo "var votes =[";
    while($row = $result->fetch_assoc()){
      echo urldecode($row["Vote"]).",";
    }
    echo "];\n";
    echo 'var listOfCandidates  = generateListOfCandidates(votes);';
  }else{
    echo "// There are no films. Use defaults \n";
    echo 'var listOfCandidates = ["A", "B", "C", "D", "E", "F"];';
    echo 'var votes = generateRandomVotes(listOfCandidates, 1000)';
  }
  $conn->close();
  ?>

  function hideLog(){
    document.getElementById('log').style.visibility = "hidden";
  }

  var line1 = "";
  var line2 = "";
  var line3 = "";
  function log(string, update){
    var log = document.getElementById('log');
    if(update !== true){
      line1 = line2;
      line2 = line3;
      line3 = string;
    }else{
      line3 = string;
    }
    log.innerHTML = line1 + "<br>" + line2 + "<br>" + line3;
    console.log(string);
  }

  var results= [];

  window.addEventListener("load", runAllAlgorithms(1));

  function createInformationOnAlgorithm(algorithmName, algorithm, listOfCandidates, votes, divToPopulate, onFinish){
    var start = performance.now();
    divToPopulate.classList.add('algorithm');
    divToPopulate.innerHTML = '<h2>' + algorithmName + '</h2> <h3 id="' + algorithmName + 'Time" class="time"></h3>';
    try{
      var result = algorithm(listOfCandidates, votes);
      results.push(result);
      divToPopulate.innerHTML = divToPopulate.innerHTML + '<table class="results-table" id="' + algorithmName + 'Table">' + populateTable(result) + '</table>';
    }catch(err){
      divToPopulate.innerHTML = divToPopulate.innerHTML + '<p class="error-message">'+err+'</p>';
    }
    var timeTaken  = (performance.now() - start)/1000;
    timeTaken = timeTaken.toFixed(4);
    document.getElementById(algorithmName + 'Time').innerHTML = timeTaken + 's';
    log(algorithmName + '...' + timeTaken + 's', true);
    onFinish();
  }

  function runAllAlgorithms(i) {
    if( i === 1){
      // generateRandomVotes(1000);
      log('Schulze Ranking...');
      setTimeout(createInformationOnAlgorithm,0,'Schulze', schulze, listOfCandidates, votes, document.getElementById('schulze'),function(){runAllAlgorithms(2)});
    }else if(i === 2){
      log('Copeland Ranking...');
      setTimeout(createInformationOnAlgorithm,100,'Copeland', copeland, listOfCandidates, votes, document.getElementById('copeland'), function(){runAllAlgorithms(3)});
    }else if(i === 3){
      log('Minimax Ranking...');
      setTimeout(createInformationOnAlgorithm,100,'Minimax', minimax, listOfCandidates, votes, document.getElementById('minimax'), function(){runAllAlgorithms(4)});
    }else if(i === 4){
      log('Borda Ranking...');
      setTimeout(createInformationOnAlgorithm,100,'Borda', borda, listOfCandidates, votes, document.getElementById('borda'), function(){runAllAlgorithms(5)});
    }else if( i === 5){
      log('Kemeny-Young Ranking...');
      setTimeout(createInformationOnAlgorithm,100,'Kemeny-Young Ranking', kemenyYoung, listOfCandidates, votes, document.getElementById('kemenyYoung'), function(){runAllAlgorithms(6)});
    }else if(i === 6){
      log('First-Past-The-Post...')
      setTimeout(createInformationOnAlgorithm,100,'First-Past-The-Post', plurality, listOfCandidates, votes, document.getElementById('first-past-the-post'), function(){runAllAlgorithms(7)});
    }else if(i === 7){
      log('AV...')
      setTimeout(createInformationOnAlgorithm,100,'AV', av, listOfCandidates, votes, document.getElementById('av'), function(){runAllAlgorithms(8)});
    }else if(i === 8){
      log('Nanson...')
      setTimeout(createInformationOnAlgorithm,100,'Nanson', nanson, listOfCandidates, votes, document.getElementById('nanson'), function(){runAllAlgorithms(9)});
    }else if(i === 9) {
      log('Anti-Plurality...')
      setTimeout(createInformationOnAlgorithm,100,'Anti-Plurality', antiPlurality, listOfCandidates, votes, document.getElementById('anti-plurality'), function(){runAllAlgorithms(10)});
    }else if(i === 10) {
      log("Coombs'...")
      setTimeout(createInformationOnAlgorithm,100,"Coombs'", coombs, listOfCandidates, votes, document.getElementById('coombs'), function(){runAllAlgorithms(11)});
    }else if(i === 11) {
      log('Baldwin Ranking...');
      var onEnd = function(){
        log('Drawing Graphs...');
        setTimeout(drawGraphs);
      }
      setTimeout(createInformationOnAlgorithm,100,'Baldwin', baldwin, listOfCandidates, votes, document.getElementById('baldwin'), onEnd);
    }
  }

  function drawGraphs(){
    var distances = getDistances(listOfCandidates, votes);
    document.getElementById('winner').innerHTML = calculateWinner();
    document.getElementById('svg').innerHTML  = drawDirectedGraph(listOfCandidates, distances);
    document.getElementById('distances-table').innerHTML = populateDistances(listOfCandidates, distances);
    document.getElementById('key').innerHTML = generateKey(listOfCandidates);
    log('Finished');
    setTimeout(closeClapper, 300);
    setTimeout(shrinkHeader, 800);
    setTimeout(hideLog,500);
  }

  function populateTable(results){
    var html = "";
    if(results && results[0].hasOwnProperty('score')){
      html = "<thead><tr><th>Rank</th><th>Film</th><th>Score</th></tr></thead>";
      for(var i = 0; i < results.length; i++){
        if(results[i].rank === 1){
          html = html + "<tr class='rank1'><td>" + results[i].rank + "</td><td>" + results[i].film + "</td><td>" + results[i].score +"</td></tr>";
        }else{
          html = html + "<tr><td>" + results[i].rank + "</td><td>" + results[i].film + "</td><td>" + results[i].score +"</td></tr>";
        }
      }
    }else if(results){
      html = "<thead><th>Rank</th><th>Film</th></thead>";
      for(var i = 0; i < results.length; i++){
        if(results[i].rank === 1){
          html = html + "<tr class='rank1'><td>" + results[i].rank + "</td><td>" + results[i].film + "</td></tr>";
        }else{
          html = html + "<tr><td>" + results[i].rank + "</td><td>" + results[i].film + "</td></tr>";
        }
      }
    }
    return html;
  }

  function calculateWinner(){
    var numberWon = {};
    for(var i =0; i < listOfCandidates.length; i++){
      numberWon[listOfCandidates[i]] = 0;
    }
    for(var i = 0; i < results.length; i++){
      for(var j =0; j < listOfCandidates.length; j++){
        if(results[i][j].rank === 1){
          numberWon[results[i][j].film]++;
        }else{
          break;
        }
      }
    }
    var highestScorers = [];
    var highestScore = 0;
    for(var i =0; i < listOfCandidates.length; i++){
      if(numberWon[listOfCandidates[i]] === highestScore){
        highestScorers.push(listOfCandidates[i]);
      }else if(numberWon[listOfCandidates[i]] > highestScore){
        highestScore = numberWon[listOfCandidates[i]];
        highestScorers = [listOfCandidates[i]];
      }
    }
    // TODO: Check which ones they won;
    var winner = "It's a draw";
    if(highestScorers.length === 1){
      winner = highestScorers[0];
    }
    return winner;
  }

  function populateDistances(listOfCandidates, distances){
    var html = "<tbody><tr><th></th>"
    for(var i = 0; i < listOfCandidates.length; i++){
      html = html + '<th><span class="key-node" style="background-color:'+nodeColors[i % nodeColors.length]+'">' +  String.fromCharCode(65 + i) + '</span></th>';
    }
    html = html + "</tr>";
    for(var i = 0; i < listOfCandidates.length; i++){
      html = html + '<tr><th><span class="key-node" style="background-color:'+nodeColors[i % nodeColors.length]+'">' +  String.fromCharCode(65 + i) + '</span></th>';
      for(var j = 0; j < listOfCandidates.length; j++){
        if(i === j){
          html = html + "<td class='null'></td>";
        }else if(distances[i][j] === distances[j][i]){
          html = html + "<td class='equal'>" + distances[i][j] + "</td>";
        }else if(distances[i][j] > distances[j][i]){
          html = html + "<td class='better'>" + distances[i][j] + "</td>";
        }else{
          html = html + "<td class='worse'>" + distances[i][j] + "</td>";
        }
      }
      html = html + "</tr>";
    }
    html = html + "</tbody>";
    return html;
  }

  function generateKey(listOfCandidates){
    var key = "";
    for(var i = 0; i < listOfCandidates.length; i++){
      key = key + '<div><span class="key-node" style="background-color:'+nodeColors[i % nodeColors.length]+'">' +  String.fromCharCode(65 + i) + '</span><span>' + listOfCandidates[i] + '</span></div>';
    }
    return key;
  }


</script>
</body>
</html>
