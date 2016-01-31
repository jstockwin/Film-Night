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
        console.log(JSON.parse(xhr.responseText).Search);
        updateFilms(JSON.parse(xhr.responseText).Search);
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

    function toggleFilm(img){
      var selected = img.parentElement.getAttribute('data-selected');
      if(selected === "true"){
        img.classList.remove('search-result-picture-hover');
        img.nextElementSibling.style.transform = "scale(0)";
        img.parentElement.setAttribute('data-selected', 'false');
      }else{
        img.classList.add('search-result-picture-hover');
        img.nextElementSibling.style.transform = "scale(1)";
        img.parentElement.setAttribute('data-selected', 'true');
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
          <button type="submit" class="solid-button" onclick="search()">Search</button>
        </div>
      </div>
      <div id="search-results">
        <div class="search-result">
          <img src="https://ia.media-imdb.com/images/M/MV5BMTY2ODkzMDgwM15BMl5BanBnXkFtZTcwMDA1Mjg1OA@@._V1_SX300.jpg" class="search-result-picture">
          <div class="info-conatiner">
            <h3 class="search-result-title">The Guard</h3>
            <h4 class="search-result-year">The Year</h4>
          </div>
          <img src="ic_check.svg" class="select-check">
        </div>
      </div>
    </div>
  <?php endif ?>
  <?php if(!isset($_GET['noheader'])): ?>
  </body>
  </html>
<?php endif ?>
