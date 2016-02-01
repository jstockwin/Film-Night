<?php if(!isset($_GET['noheader'])): ?>
  <!DOCTYPE html>
  <?php include 'header.php';
  require $root.'../../database.php'; ?>
  <html>
  <body>
    <?php include 'top-nav.php'; ?>
  <?php endif ?>
  <?php if(($permission != FALSE && status($root) == "results") || $permission == "admin"): ?>
    <script>
    showContent();

    function textInputFocus(input){
      input.nextElementSibling.classList.add('label-active');
    }

    function textInputBlur(input){
      if(input.value.length === 0){
        input.nextElementSibling.classList.remove('label-active');
      }
    }

    function validateYear(input){
      console.log( /^\+?\d+$/.test(input.value));
      if(input.value.length !== 0 && /^\+?\d+$/.test(input.value)){
        input.nextElementSibling.nextElementSibling.style.display = "none";
      }else{
        input.nextElementSibling.nextElementSibling.style.display = "block";
      }
    }

    function search(){
      var filmName = document.getElementById('film-name').value;
      var filmYear = document.getElementById('film-year').value;
      if(!filmYear.match(/^\d+$/)){
        filmYear = "";
      }
      var href = 'http://www.omdbapi.com/?s=' + filmName +'&y=' + filmYear + '&plot=short&r=json';
      var xhr = new XMLHttpRequest();
      xhr.open('GET',href);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function(){
        var results = JSON.parse(xhr.responseText);
        if(results.Search){
          changeInformation('Select films', '#607D8B', false);
          updateFilms(results.Search);
        }else{
          changeInformation(results.Error,'#F47738', false);
        }
      }
      xhr.send();
    }

    function updateFilms(searchResults){
      var html = "";
      var searchResultsDiv = document.getElementById('search-results');
      for(var i = 0; i < searchResults.length; i++){
        var div = document.createElement('div');
        searchResultsDiv.appendChild(div);
        html = '<div class="search-result" class="search-result-picture" data-film-name="' + searchResults[i].Title + '" data-film-year="' + searchResults[i].Year + '" data-selected="false">';
        html += '<img class="search-result-picture" src="' + searchResults[i].Poster + '" onclick="toggleFilm(this)">';
        html += '<img src="ic_check.svg" class="select-check">'
        html += '<div class="info-conatiner">';
        html += '<h3 class="search-result-title">' + searchResults[i].Title + '</h3>';
        html += '<h4 class="search-result-year">' + searchResults[i].Year + '</h4>';
        html += '</div></div>';
        div.outerHTML = html;
      }
    }

    var numberSelected = 0;

    function toggleFilm(img){
      var selected = img.parentElement.getAttribute('data-selected');
      if(selected === "true"){
        numberSelected--;
        img.classList.remove('search-result-picture-hover');
        img.nextElementSibling.style.transform = "scale(0)";
        img.parentElement.setAttribute('data-selected', 'false');
      }else{
        if(numberSelected === 0){
          changeInformation('Add films','#607D8B', true);
        }
        numberSelected++;
        img.classList.add('search-result-picture-hover');
        img.nextElementSibling.style.transform = "scale(1)";
        img.parentElement.setAttribute('data-selected', 'true');
      }
    }

    function changeInformation(message, color, showImage){
      document.getElementById('information').style.backgroundColor = color;
      document.getElementById('tooltip').innerHTML = message;
      if(showImage){
        document.getElementById('action-button').style.opacity = "1";
      }else{
        document.getElementById('action-button').style.opacity = "0";
      }
    }

    </script>
    <div id="container">
      <div id="inputs">
        <div class="input-wrapper"  style="flex-grow: 1;">
          <input type="text" class="text-input" id="film-name" onfocus="textInputFocus(this)" onblur="textInputBlur(this)">
          <label for="film-name">Film Name</label>
        </div>
        <div class="input-wrapper">
          <input type="tel" class="text-input" id="film-year" onfocus="textInputFocus(this)" onblur="textInputBlur(this)" onchange="validateYear(this)">
          <label for="film-year">Year</label>
          <img class="warning-image" src="ic_warning.svg" alt="Not a valid value.">
        </div>
        <div class="input-wrapper">
          <button type="button" class="solid-button" onclick="search()">Search</button>
        </div>
      </div>
      <div id="search-results">
        <div class="search-result arrow-clip" id="information">
          <div id="button-container">
            <div id="background"></div>
            <div id="ripple"></div>
            <img id="action-button" src="ic_upload.svg">
          </div>
            <p id="tooltip">Search for films</p>
        </div>
      </div>
    </div>
  <?php endif ?>
  <?php if(!isset($_GET['noheader'])): ?>
  </body>
  </html>
<?php endif ?>
