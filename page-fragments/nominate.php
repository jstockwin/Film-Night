<?php include '../setup.php';?>
<?php require $root.'../../database.php'; ?>
<?php $permission = loginCheck($session); ?>
<?php if($permission != FALSE): ?>
<div id="inputs">
  <div class="input-wrapper"  style="flex-grow: 1;">
    <input type="text" class="text-input" id="film-name" onfocus="textInputFocus(this)" onblur="textInputBlur(this)" onkeydown="keyDown(event)">
    <label for="film-name">Film Name</label>
  </div>
  <div class="input-wrapper">
    <input type="tel" class="text-input" id="film-year" onfocus="textInputFocus(this)" onblur="textInputBlur(this)" onchange="validateYear(this)">
    <label for="film-year">Year</label>
    <img class="warning-image" src="assets/icons/ic_warning.svg" alt="Not a valid value.">
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
<?php endif ?>
