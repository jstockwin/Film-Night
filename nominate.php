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
      }
      xhr.send();
    }

    function updateFilms(searchResults){

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
      </div>
    </div>
  <?php endif ?>
  <?php if(!isset($_GET['noheader'])): ?>
  </body>
  </html>
<?php endif ?>
