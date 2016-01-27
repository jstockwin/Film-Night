<?php if(!isset($_GET['noheader'])): ?>
<!DOCTYPE html>
<?php include 'header.php';
require $root.'../../database.php'; ?>
<html>
<body>
  <?php include 'top-nav.php'; ?>
<?php endif ?>
<?php if($permission === 'admin'): ?>
<script type="text/javascript" src="voting-systems.js"></script>
<p id="log"></p>
<div id="container">
  <div id="results">
    <div id="winner-div">
      <h3>This week's winner is</h3>
      <h2 id="winner"></h2>
    </div>
    <div id="utilities">
      <div id="utilities-tables">
        <div id="distances"><table id="distances-table"></table></div>
        <div id="svg"></div>
      </div>
    </div>
    <div id="tables">
      <div id="schulze"></div>
      <div id="copeland"></div>
      <div id="borda"></div>
      <div id="minimax"></div>
      <div id="kemenyYoung"></div>
      <div id="baldwin"></div>
    </div>
  </div>
</div>
<script>

// var listOfCandidates = ['Interstellar','The Lion King','The Dukes of Hazzard','Jurassic World',"Ocean's Eleven"];
// var votes = [{'Jurassic World': 1, 'Interstellar': 1, 'The Lion King': 3, 'The Dukes of Hazzard': 4, "Ocean's Eleven": 5}];

<?php

$conn = new mysqli($host, $username, $password, "films");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM selected_films";
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
echo "\n";
$sql = "SELECT * FROM votes";
$result = $conn->query($sql);

if ($result->num_rows > 0){
  echo "var votes =[";
  while($row = $result->fetch_assoc()){
    echo urldecode($row["Vote"]).",";
  }
  echo "];";
}else{
  echo "// There are no films.";
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

window.onload = runAllAlgorithms(1);

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
  var timeTaken  = Math.round(performance.now() - start)/1000;
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
    log('Baldwin Ranking...');
    var onEnd = function(){
      var distances = getDistances(listOfCandidates, votes);
      document.getElementById('winner').innerHTML = calculateWinner();
      document.getElementById('svg').innerHTML  = drawDirectedGraph(listOfCandidates, distances);
      document.getElementById('distances-table').innerHTML = populateDistances(listOfCandidates, distances);
      setTimeout(closeClapper, 300);
      setTimeout(shrinkHeader, 800);
      setTimeout(hideLog,500);
    }
    setTimeout(createInformationOnAlgorithm,100,'Baldwin', baldwin, listOfCandidates, votes, document.getElementById('baldwin'), onEnd);
  }
}

function populateTable(results){
  var html = "";
  if(results && results[0].score){
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
    html = html + "<th>" + listOfCandidates[i] + "</th>";
  }
  html = html + "</tr>";
  for(var i = 0; i < listOfCandidates.length; i++){
    html = html + "<tr><th>" + listOfCandidates[i] + "</th>";
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

function drawDirectedGraph(listOfCandidates, distances){
  var nodeColors = ['#F44336', '#9C27B0', '#3F51B5', '#009688', '#FF5722', '#795548'];
  var coldColor = "1B28BF";
  var warmColor = "BF1B1B";

  var graphRadius = 100;
  var nodeRadius = 10;
  var numberOfIndicatorSquares = 10;
  var size = graphRadius/numberOfIndicatorSquares;

  var svgStart = '<svg id="graph" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="'+ -graphRadius + ' '+ -graphRadius +  ' '+ (2*graphRadius + nodeRadius + 3*size + 25 ) + ' '+ 2*graphRadius + '" style="overflow:visible; height: 300 !important; width: 300 !important;">'
  var svg = "";
  var defsHTML = "<defs>";
  var coordinates = [];
  for( var i = 0; i < listOfCandidates.length; i++){
    var x = graphRadius * Math.sin( - i / listOfCandidates.length * 2 *Math.PI + Math.PI);
    var y = graphRadius * Math.cos( - i / listOfCandidates.length * 2 * Math.PI + Math.PI);
    coordinates.push({'x': x, 'y': y});
  }
  var min = Number.POSITIVE_INFINITY;
  var max = 0;
  for( var i =0; i< listOfCandidates.length; i++){
    for(var j = 0; j < listOfCandidates.length; j++){
      if(distances[i][j] > distances[j][i]){
        min = Math.min(min, distances[i][j]);
        max = Math.max(max, distances[i][j]);
      }
    }
  }
  for( var i =0; i< listOfCandidates.length; i++){
    for(var j = 0; j < listOfCandidates.length; j++){
      if(distances[i][j] < distances[j][i]){
        var differenceX = coordinates[i].x - coordinates[j].x;
        var differenceY = coordinates[i].y - coordinates[j].y;
        var length = Math.sqrt(differenceX*differenceX + differenceY*differenceY);
        var targetX = coordinates[j].x + differenceX*(length-10)/length;
        var targetY = coordinates[j].y + differenceY*(length-10)/length;
        var color = interpolateColors(coldColor, warmColor, (distances[j][i] - min)/Math.max(max - min, 1));
        var newMarker = '<marker id="arrow'+color+'" markerWidth="10" markerHeight="10" refx="6" refy="2" orient="auto" markerUnits="strokeWidth" fill="#'+color+'"><path d="M0,0 L0,4 L6,2 z"/></marker>'
        defsHTML = defsHTML + newMarker;
        var line = '<line x1="' + coordinates[j].x + '" y1 = "'+ coordinates[j].y+ '" x2="'+  targetX +'" + y2="'+ targetY +'" stroke-width="2" stroke="#'+color+'" marker-end="url(#arrow'+color+')"/>';
        svg = svg + line;
      }
    }
  }
  for(var i = 0; i < listOfCandidates.length; i++){
    var node = '<circle cx="'+ coordinates[i].x + '" cy="'+ coordinates[i].y + '" r="'+nodeRadius+'" fill="'+nodeColors[i % nodeColors.length] + '"/>';
    var text = '<text x="'+ coordinates[i].x + '" y="'+ coordinates[i].y + '" text-anchor="middle"  fill="white" font-size="' + nodeRadius * 1.5 + '" style="alignment-baseline:central" font-family="Open Sans">' + String.fromCharCode(65 + i) + '</text>';
    svg = svg + node + text;
  }
  for(var i =0; i < numberOfIndicatorSquares; i++ ){

    var rect = '<rect x="'+ (graphRadius + nodeRadius + size) + '" y="'+(-graphRadius + i*size*2)+'" width="'+size+'" height="'+size+'" fill="#'+interpolateColors(coldColor, warmColor, 1 - i/numberOfIndicatorSquares)+'"/>';
    var text = '<text x="'+(graphRadius + nodeRadius + 3 * size) + '" y="'+(-graphRadius + i*size*2 + size/2)+ '" font-size="' + size + '" style="alignment-baseline:central" font-family="Open Sans">' + Math.round((max - (max - min)*i/numberOfIndicatorSquares)) + '</text>';
    svg = svg + rect + text;

  }
  defsHTML = defsHTML + '</defs>';
  return svgStart + defsHTML + svg + '</svg>';
}

function interpolateColors(hex1, hex2, t){
  var rgb1 = hexToRgb(hex1);
  var rgb2 = hexToRgb(hex2);
  return rgbToHex(Math.round((1-t)*rgb1.r + t*rgb2.r), Math.round((1-t)*rgb1.g + t*rgb2.g), Math.round((1-t)*rgb1.b + t*rgb2.b));

}

function componentToHex(c) {
  var hex = c.toString(16);
  return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
  return componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function hexToRgb(hex) {
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function(m, r, g, b) {
    return r + r + g + g + b + b;
  });

  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}
</script>
<?php endif ?>
<?php if(!isset($_GET['noheader'])): ?>
</body>
</html>
<?php endif ?>
