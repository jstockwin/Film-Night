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
      var href = 'https://www.omdbapi.com/?s=' + filmName +'&y=' + filmYear + '&plot=short&r=json';
      var xhr = new XMLHttpRequest();
      xhr.open('GET',href);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function(){
        var results = JSON.parse(xhr.responseText);
        removeOldFilms();
        if(results.Search){
          updateFilms(results.Search);
        }else{
          document.getElementById('error-container').style.opacity = 1;
          document.getElementById('error-message').innerHTML = results.Error;
          setTimeout(function(){document.getElementById('error-container').style.opacity = 0;}, 2000);
        }
      }
      xhr.send();
    }

    function removeOldFilms(){
      var results = document.getElementById('search-results');
      for(var i = results.children.length-1; i > 0; i--){
        var child = results.children[i];
        if(child.getAttribute('data-selected') === "false"){
          child.remove();
        }
      }
    }

    function updateFilms(searchResults){
      var html = "";
      var searchResultsDiv = document.getElementById('search-results');
      for(var i = 0; i < searchResults.length; i++){
        var div = document.createElement('div');
        searchResultsDiv.appendChild(div);
        html = '<div class="search-result" class="search-result-picture" data-film-name="' + searchResults[i].Title + '" data-film-year="' + searchResults[i].Year + '" data-selected="false" data-veto="false">';
        html += '<img class="search-result-picture" src="' + searchResults[i].Poster + '" onclick="toggleFilm(this)" draggable="false">';
        html += '<div class="main-icon-container"><div class="check-background"></div><img src="/assets/icons/ic_check.svg" class="icon"></div>'
        html += '<div class="child-icon-container" title="Suitable for vegetarians"><div class="veto-background"></div><span class="veto-v">V</span><span class="veto-eto">eto</span></div>'
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
      var veto = img.parentElement.getAttribute('data-veto')
      if(selected === "true" && veto === "true"){
        numberSelected--;
        if(numberSelected === 0){
          document.getElementById('button-disabler').style.width = "100%";
        }
        img.classList.remove('search-result-picture-hover');
        img.nextElementSibling.classList.remove('selected-true');
        img.nextElementSibling.nextElementSibling.classList.remove('veto-true');
        img.nextElementSibling.nextElementSibling.classList.remove('slide-down');
        img.parentElement.setAttribute('data-selected', 'false');
        img.parentElement.setAttribute('data-veto', 'false');
      }else if(selected === 'true'){
          img.nextElementSibling.nextElementSibling.classList.add('veto-true');
          img.parentElement.setAttribute('data-veto', 'true');
      }else{
        if(numberSelected === 0){
          document.getElementById('button-disabler').style.width = '0';
        }
        numberSelected++;
        img.classList.add('search-result-picture-hover');
        img.nextElementSibling.nextElementSibling.classList.add('slide-down');
        img.nextElementSibling.classList.add('selected-true');
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

    function submitFilms(){
      removeOldFilms();
      var selectedFilms = [];
      var results = document.getElementById('search-results');
      for(var i = 1; i < results.children.length; i++){
        var child = results.children[i];
        if(child.getAttribute('data-selected') === "true"){
          var film = {};
          film.Title = child.getAttribute('data-film-name');
          film.Year = child.getAttribute('data-film-year');
          film.Veto = child.getAttribute('data-veto');
          if(child.getAttribute('data-veto') === "false"){
            child.children[0].click();
          }
          child.children[0].click();
          selectedFilms.push(film);
        }
      }
      numberSelected = 0;
      console.log(selectedFilms);
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
          <img class="warning-image" src="/assets/icons/ic_warning.svg" alt="Not a valid value.">
        </div>
        <div class="input-wrapper">
          <button type="button" class="solid-button" onclick="search()">Search</button>
        </div>
      </div>
      <div id="search-results">
        <div class="search-result" id="information">
          <div class="tooltip-conatiner">
            <h3 class="tooltip">Search</h3>
            <h3 class="tooltip">Select</h3>
            <div style="position:relative; margin: 5% 0;">
              <button type="button" id="submit-films" onclick="submitFilms()">Submit</button>
              <div id="button-disabler">
                <button type="button" class="disabled">Submit</button>
              </div>
            </div>
          </div>
          <div id="error-container">
            <div class="tooltip-conatiner">
              <h3 class="tooltip" id="error-message">Error</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif ?>
  <?php if(!isset($_GET['noheader'])): ?>
  </body>
  </html>
<?php endif ?>
