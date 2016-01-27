

function generateRandomVotes(n) {
  var order = [];
  for (var i = 0; i < listOfCandidates.length; i++) {
    order.push(i);
  }
  votes = [];
  for (i = 0; i < n; i++) {
    var vote = {};
    shuffle(order);
    for (var j = 0; j < listOfCandidates.length; j++) {
      vote[listOfCandidates[j]] = order[j];
    }
    votes.push(vote);
  }
}

function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex;
  while (0 !== currentIndex) {
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }
  return array;
}

function getDistances(listOfCandidates, votes) {
  var distances = [];

  for (var i = 0; i < listOfCandidates.length; i++) {
    var row = [];
    for (var j = 0; j < listOfCandidates.length; j++) {
      var distance = 0;
      for (var k = 0; k < votes.length; k++) {
        if (votes[k][listOfCandidates[i]] < votes[k][listOfCandidates[j]]) {
          distance++;
        }
      }
      row.push(distance);
    }
    distances.push(row);
  }
  return distances;
}

function schulze(listOfCandidates, votes) {
  var distances = getDistances(listOfCandidates, votes);
  var paths = [];
  for (var i = 0; i < listOfCandidates.length; i++) {
    var row = [];
    for (var j = 0; j < listOfCandidates.length; j++) {
      if (distances[i][j] > distances[j][i]) {
        row.push(distances[i][j]);
      }else {
        row.push(0);
      }
    }
    paths.push(row);
  }
  for (i = 0; i < listOfCandidates.length; i++) {
    for (j = 0; j < listOfCandidates.length; j++) {
      if (i !== j) {
        for (var k = 0; k < listOfCandidates.length; k++) {
          if (k !== i && k !== j) {
            paths[j][k] = Math.max(paths[j][k], Math.min(paths[j][i], paths[i][k]));
          }
        }
      }
    }
  }
  var sortFunction = function sort(a, b) {
    if (paths[a][b] > paths[b][a]) {
      return -1;
    }
    if (paths[b][a] > paths[a][b]) {
      return 1;
    }
    return 0;
  };

  var mapped = [];
  for (i = 0; i < listOfCandidates.length; i++) {
    mapped.push(i);
  }
  mapped.sort(sortFunction);
  var result = mapped.map(function(i) {
    return {film: listOfCandidates[i], index: i};
  });
  result[0].rank = 1;
  for (i = 1; i < listOfCandidates.length; i++) {
    if (paths[result[i].index][result[i - 1].index] === paths[result[i - 1].index][result[i].index]) {
      result[i].rank = result[i - 1].rank;
    }else {
      result[i].rank = i + 1;
    }
  }
  return result;
}

function copeland(listOfCandidates, votes) {
  var distances = getDistances(listOfCandidates, votes);
  var scores = [];
  for (var i = 0; i < listOfCandidates.length; i++) {
    var score = 0;
    for (var j = 0; j < listOfCandidates.length; j++) {
      if (i !== j) {
        if (distances[i][j] > distances[j][i]) {
          score++;
        }else if (distances[i][j] < distances[j][i]) {
          score--;
        }
      }
    }
    scores.push({film: listOfCandidates[i], 'score': score, rank: i});
  }
  scores.sort(function(a, b) {return b.score - a.score;});
  scores[0].rank = 1;
  for (i = 1; i < listOfCandidates.length; i++) {
    if (scores[i].score === scores[i - 1].score) {
      scores[i].rank = scores[i - 1].rank;
    }else {
      scores[i].rank = i + 1;
    }
  }
  return scores;
}

function minimax(listOfCandidates, votes) {
  var distances = getDistances(listOfCandidates, votes);
  var scores = [];
  for (var i = 0; i < listOfCandidates.length; i++) {
    var highestScore = 0;
    for (var j = 0; j < listOfCandidates.length; j++) {
      if (distances[j][i] > distances[i][j] && distances[j][i] > highestScore) {
        highestScore = distances[j][i];
      }
    }
    scores.push({film: listOfCandidates[i], score: highestScore, rank: i});
  }
  scores.sort(function(a, b) { return a.score - b.score;});
  scores[0].rank = 1;
  for (i = 1; i < listOfCandidates.length; i++) {
    if (scores[i].score === scores[i - 1].score) {
      scores[i].rank = scores[i - 1].rank;
    }else {
      scores[i].rank = i + 1;
    }
  }
  return scores;
}

function borda(listOfCandidates, votes) {
  var scores = [];
  for (var k = 0; k < listOfCandidates.length; k++) {
    scores.push({film: listOfCandidates[k], score: 0, rank: k});
  }

  for (var i = 0; i < listOfCandidates.length; i++) {
    for (var j = 0; j < votes.length; j++) {
      scores[i].score = scores[i].score - votes[j][scores[i].film] + 1 + listOfCandidates.length;
    }
  }
  scores.sort(function(a, b) {return b.score - a.score;});
  scores[0].rank = 1;
  for (i = 1; i < listOfCandidates.length; i++) {
    if (scores[i].score === scores[i - 1].score) {
      scores[i].rank = scores[i - 1].rank;
    }else {
      scores[i].rank = i + 1;
    }
  }
  return scores;
}

function kemenyYoung(listOfCandidates, votes) {
  var distances = getDistances(listOfCandidates, votes);
  var currentHighest = -1;
  var uniqueHighest = true;
  var rankingOfHighest = [];
  var array = [];
  for (var i = 0; i < listOfCandidates.length; i++) {
    array.push(i);
  }
  var permutations = permutator(array);
  for (i = 0; i < permutations.length; i++) {
    var score = 0;
    for (var j = 0; j < listOfCandidates.length - 1; j++) {
      for (var k = j + 1; k < listOfCandidates.length; k++) {
        score = score + distances[permutations[i][j]][permutations[i][k]];
      }
    }
    if (score === currentHighest) {
      uniqueHighest = false;
    }
    if (score > currentHighest) {
      currentHighest = score;
      rankingOfHighest = permutations[i];
      uniqueHighest = true;
    }
  }
  if (!uniqueHighest) {
    throw 'Draw for the higest score';
  }
  var result = rankingOfHighest.map(function(i, index) {return {film: listOfCandidates[i], rank: index + 1};});
  return result;
}

function permutator(inputArr) {
  var results = [];

  function permute(arr, memo) {
    var cur;
    var memo = memo || [];

    for (var i = 0; i < arr.length; i++) {
      cur = arr.splice(i, 1);
      if (arr.length === 0) {
        results.push(memo.concat(cur));
      }
      permute(arr.slice(), memo.concat(cur));
      arr.splice(i, 0, cur[0]);
    }

    return results;
  }

  return permute(inputArr);
}

function baldwin(listOfCandidates, votes) {
  var currentCandidates = listOfCandidates.slice();
  var currentVotes = JSON.parse(JSON.stringify(votes));
  var results = [];
  while (currentCandidates.length > 0) {
    var bordaResults = borda(currentCandidates, currentVotes);
    if (bordaResults.length > 1 && bordaResults[bordaResults.length - 2].score === bordaResults[bordaResults.length - 1].score) {
      throw 'Draw for bottom place';
    }
    var toEliminate = bordaResults[bordaResults.length - 1].film;
    results.push(toEliminate);
    currentCandidates.splice(currentCandidates.indexOf(toEliminate),1);
    for (var i = 0; i < currentVotes.length; i++) {
      var rankOfEliminate = currentVotes[i].toEliminate;
      for (var j = 0; j < currentCandidates.length; j++) {
        if (currentVotes[i][currentCandidates[j]] < rankOfEliminate) {
          currentVotes[i][currentCandidates[j]]--;
        }
      }
    }
  }
  results.reverse();
  results = results.map(function(film, index) {return {'film': film, rank: index + 1};});
  return results;
}

function bucklin(listOfCandidates, votes) {
  var distances = getDistances(listOfCandidates, votes);
  var voteTotals = [];
  for (var i = 0; i < listOfCandidates.length; i++) {

  }
}

function rankedPairs(listOfCandidates, votes) {
  var distances = getDistances(listOfCandidates, votes);
  var currentPaths = [];
  for (var i = 0; i < listOfCandidates.length; i++) {
    var row = [];
    for (var j = 0; j < listOfCandidates.length; j++) {
      rows.push(0);
    }
    currentPaths.push(row);
  }
  var pairs = [];
  for (i = 0; i < listOfCandidates.length; i++) {
    for (j = i + 1; j < listOfCandidates.length; j++) {
      if (distances[i][j] > distances[j][i]) {

      }else {

      }

    }
  }
}
