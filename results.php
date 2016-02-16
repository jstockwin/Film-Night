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
          <div id="distances"></div>
          <div id="svg"></div>
        </div>
      </div>
      <div>
        <h2>Rankings</h2>
        <div>
          <div id="schulze">
            <h2 class="algorithm-name">Schulze</h2>
            <div class="algorithm">
              <p class="algorithm-description">The Schulze method (also known as the Beatpath Method) is a voting system developed in 1997 by Markus Schulze. It is used by several organisations including Debian, Ubuntu and Wikimedia. <br><br> Define \(d(X,Y)\)  to be the number of votes that rank \(X\) over \(Y\)  and let \(G\) be the wieghted, directed graph which has an edge from \(X\) to \(Y\) with weight \(w\) if and only if \(w = d(X,Y) > d(Y,X)\). A film \(X\) is better (according to the Schulze method) than a film \(Y\) if the strongest path (in \(G\)) from \(X\) to \(Y\) is stronger than the strongest path (in \(G\)) from \(Y\) to \(X\).</p>
              <div id="schulze-graph" class="algorithm-graphic"></div>
              <div id="schulze-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="copeland">
            <h2 class="algorithm-name">Copeland's</h2>
            <div class="algorithm">
              <p class="algorithm-description">Copeland's method (also known as Copeland's pairwise aggregation method) is a Condorcet voting method.<br><br>We say film \(X\) beats film \(Y\) if the number of people who ranked \(X\) higher than \(Y\) is greater than the number of people who ranked \(Y\) higher than \(X\). The score for a film \(X\) is then the number of films \(X\) beats minus the number of films that beat \(X\).  </p>
              <div id="copeland-graph" class="algorithm-graphic"><table id="distances-table"></table></div>
              <div id="copeland-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="borda">
            <h2 class="algorithm-name">Borda</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="borda-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="minimax">
            <h2 class="algorithm-name">Minimax</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="minimax-graph" class="algorithm-graphic"><table id="distances-table"></table></div>
              <div id="minimax-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="kemenyYoung">
            <h2 class="algorithm-name">Kemeny-Young</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="kemenyYoung-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="baldwin">
            <h2 class="algorithm-name">Baldwin</h2>
            <div class="algorithm">
              <p class="algorithm-description">The Baldwin method is a form of runoff voting developed by Joseph M. Baldwin<br><br>In each round the films are ranked according to a Borda count. The film with the lowest score is then eliminated. The films are then ranked according to how long they remained in the process.</p>
              <div id="baldwin-graph" class="algorithm-graphic"></div>
              <div id="baldwin-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="nanson">
            <h2 class="algorithm-name">Nanson</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="nanson-graph" class="algorithm-graphic"></div>
              <div id="nanson-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="first-past-the-post"></div>
          <div id="first-past-the-post">
            <h2 class="algorithm-name">First-Past-the-Post</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="first-past-the-post-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="av">
            <h2 class="algorithm-name">Alternative Voting</h2>
            <div class="algorithm">
              <p class="algorithm-description">Alternative Voting (also known as Instant-Runoff Voting) is a widely used single-winner voting method. It is often used to elect politicians with examples including the leaders of the Labour Party and the Liberal Democrats, the charimen of select committees and the Speaker of the House of Lords. First-past-the-post is still used to elect MPs after 67.9% of voters voted against adopting AV in a referendum on May 5 2011.<br><br>The number of first votes for each candidate are counted. If a candidate has a majority, they are declared the winner, else the candidates with the lowest score are eliminated and the process is repeated.</p>
              <div id="av-graph" class="algorithm-graphic"></div>
              <div id="av-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="anti-plurality">
            <h2 class="algorithm-name">Anti-Plurality</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="anti-plurality-table" class="algorithm-table"></div>
            </div>
          </div>
          <div id="coombs">
            <h2 class="algorithm-name">Coomb's</h2>
            <div class="algorithm">
              <p class="algorithm-description"></p>
              <div id="coombs-graph" class="algorithm-graphic"><table id="distances-table"></table></div>
              <div id="coombs-table" class="algorithm-table"></div>
            </div>
          </div>
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

window.addEventListener("load", updateAllTables);

function createInformationOnAlgorithm(algorithmName, algorithm, listOfCandidates, votes, divToPopulate, onFinish){
  var start = performance.now();
  try{
    var result = algorithm(listOfCandidates, votes);
    results.push(result);
    divToPopulate.innerHTML = '<table class="results-table" id="' + algorithmName + 'Table">' + populateTable(result) + '</table>';
  }catch(err){
    divToPopulate.innerHTML ='<p class="error-message">'+err+'</p>';
  }
  var timeTaken  = (performance.now() - start)/1000;
  timeTaken = timeTaken.toFixed(4);
  //document.getElementById(algorithmName + 'Time').innerHTML = timeTaken + 's';
  log(algorithmName + '...' + timeTaken + 's', true);
  onFinish();
}

function runAllAlgorithms(i) {
  if( i === 1){
    // generateRandomVotes(1000);
    log('Schulze Ranking...');
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

function updateAllTables() {
  var algorithms = [{Name: 'Schulze', 'Function': schulze, 'TableID': 'schulze-table' },{Name: 'Copeland', 'Function': copeland, 'TableID': 'copeland-table' }, {Name: 'AV', 'Function': av, 'TableID': 'av-table' },{Name: 'Borda', 'Function': borda, 'TableID': 'borda-table' },{Name: 'Minimax', 'Function': minimax, 'TableID': 'minimax-table' },{Name: 'Kemeny-Young', 'Function': kemenyYoung, 'TableID': 'kemenyYoung-table' },{Name: 'Baldwin', 'Function': baldwin, 'TableID': 'baldwin-table' },{Name: 'Nanson', 'Function': nanson, 'TableID': 'nanson-table' },{Name: 'First-Past-The-Post', 'Function': plurality, 'TableID': 'first-past-the-post-table' },{Name: 'Anti-Plurality', 'Function': antiPlurality, 'TableID': 'anti-plurality-table' },{Name: 'Coombs\'', 'Function': coombs, 'TableID': 'coombs-table' }]

  log(algorithms[0].Name + '...');

    function updateAllTablesStep(index) {
      var start = performance.now();
      try{
        var result = algorithms[index].Function(listOfCandidates, votes);
        results.push(result);
        document.getElementById(algorithms[index].TableID).innerHTML = '<table class="results-table" id="' + algorithms[index].Name + 'Table">' + populateTable(result) + '</table>';
      }catch(err){
        log(err);
      }
      var timeTaken  = (performance.now() - start)/1000;
      timeTaken = timeTaken.toFixed(4);
      //document.getElementById(algorithmName + 'Time').innerHTML = timeTaken + 's';
      log(algorithms[index].Name + '...' + timeTaken + 's', true);
      if (index < algorithms.length - 1) {
        log(algorithms[index + 1].Name + '...');
        setTimeout(updateAllTablesStep,100, index + 1);
      }else{
        then();
      }
    }
    function then(){
      log('Drawing Graphs...');
      setTimeout(drawGraphs);
    }

    updateAllTablesStep(0);
}

function drawGraphs(){
  MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
  var distances = getDistances(listOfCandidates, votes);
  document.getElementById('winner').innerHTML = calculateWinner();
  document.getElementById('schulze-graph').innerHTML  = drawDirectedGraph(listOfCandidates, distances);
  var pluralityResults = plurality(listOfCandidates, votes);
  var initialScores = {};
  for (var i = 0; i < pluralityResults.length; i++) {
    initialScores[pluralityResults[i].film] = pluralityResults[i].score;
  }
  document.getElementById('av-graph').innerHTML = drawRunOffGraph(listOfCandidates, initialScores, avChanges(listOfCandidates, votes));
  var bordaResults = borda(listOfCandidates, votes);
  var initialScores = {};
  for (var i = 0; i < bordaResults.length; i++) {
    initialScores[bordaResults[i].film] = bordaResults[i].score;
  }
  document.getElementById('baldwin-graph').innerHTML = drawRunOffGraph(listOfCandidates, initialScores, baldwinChanges(listOfCandidates, votes));
  document.getElementById('schulze-graph').innerHTML  = drawDirectedGraph(listOfCandidates, distances);
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

 
