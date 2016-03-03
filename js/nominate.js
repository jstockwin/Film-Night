function textInputFocus(e){e.nextElementSibling.classList.add("label-active")}function textInputBlur(e){0===e.value.length&&e.nextElementSibling.classList.remove("label-active")}function keyDown(e){"keyIdentifier"in e&&"Enter"===e.keyIdentifier?search():"key"in e&&"Enter"===e.key&&search()}function validateYear(e){console.log(/^\+?\d+$/.test(e.value)),e.nextElementSibling.nextElementSibling.style.display=0!==e.value.length&&/^\+?\d+$/.test(e.value)?"none":"block"}function search(){var e=document.getElementById("film-name").value,t=document.getElementById("film-year").value;t.match(/^\d+$/)||(t="");var n="https://www.omdbapi.com/?s="+e+"&y="+t+"&plot=short&r=json&type=movie",r=new XMLHttpRequest;r.open("GET",n),r.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),r.onload=function(){var e=JSON.parse(r.responseText);removeOldFilms(),e.Search?updateFilms(e.Search):(document.getElementById("error-container").style.opacity=1,document.getElementById("error-container").style.backgroundColor="#F47738",document.getElementById("error-message").innerHTML=e.Error,setTimeout(function(){document.getElementById("error-container").style.opacity=0},2e3))},r.send()}function removeOldFilms(){for(var e=document.getElementById("search-results"),t=e.children.length-1;t>0;t--){var n=e.children[t];"false"===n.getAttribute("data-selected")&&n.remove()}}function updateFilms(e){for(var t="",n=document.getElementById("search-results"),r=0;r<e.length;r++){var o=document.createElement("div");n.appendChild(o),"N/A"==e[r].Poster&&(e[r].Poster="assets/icons/nothing.png"),t='<div class="search-result" class="search-result-picture" data-film-name="'+e[r].Title+'" data-film-year="'+e[r].Year+'" data-selected="false" data-veto="false">',t+='<img class="search-result-picture" src="'+e[r].Poster+'" onclick="toggleSelected(this.parentElement)" draggable="false">',t+='<div class="main-icon-container" onclick="toggleSelected(this.parentElement)"><div class="check-background"></div><img src="assets/icons/ic_check.svg" class="icon"></div>',t+='<div class="child-icon-container" title="Suitable for vegetarians" onclick="toggleVetoed(this.parentElement)"><div class="veto-background"></div><span class="veto-v">V</span><span class="veto-eto">eto</span></div>',t+='<div class="info-conatiner">',t+='<h3 class="search-result-title">'+e[r].Title+"</h3>",t+='<h4 class="search-result-year">'+e[r].Year+"</h4>",t+="</div></div>",o.outerHTML=t}}function toggleSelected(e){var t=e.getAttribute("data-selected"),n=e.getElementsByClassName("search-result-picture")[0],r=e.getElementsByClassName("main-icon-container")[0],o=e.getElementsByClassName("child-icon-container")[0];"true"===t?(numberSelected--,0===numberSelected&&(document.getElementById("button-disabler").style.width="100%"),n&&n.classList.remove("search-result-picture-hover"),r.classList.remove("selected-true"),o.classList.remove("slide-down"),e.setAttribute("data-selected","false")):(0===numberSelected&&(document.getElementById("button-disabler").style.width="0"),numberSelected++,n&&n.classList.add("search-result-picture-hover"),o.classList.add("slide-down"),r.classList.add("selected-true"),e.setAttribute("data-selected","true"))}function toggleVetoed(e){var t=e.getAttribute("data-veto"),n=e.getElementsByClassName("child-icon-container")[0];"true"===t?(n.classList.remove("veto-true"),e.setAttribute("data-veto","false")):(n.classList.add("veto-true"),e.setAttribute("data-veto","true"))}function changeInformation(e,t,n){document.getElementById("information").style.backgroundColor=t,document.getElementById("tooltip").innerHTML=e,document.getElementById("action-button").style.opacity=n?"1":"0"}function submitFilms(){removeOldFilms();for(var e=[],t=document.getElementsByClassName("search-result"),n=1;n<t.length;n++){var r=t[n];if("true"===r.getAttribute("data-selected")){var o={};o.Title=r.getAttribute("data-film-name"),o.Year=r.getAttribute("data-film-year"),o.Veto=r.getAttribute("data-veto"),toggleSelected(r),e.push(o)}}numberSelected=0,console.log(e),console.log(JSON.stringify(e));document.getElementById("Submit");document.getElementById("error-container").style.opacity=1,document.getElementById("error-container").style.backgroundColor="#FFBF47",document.getElementById("error-message").innerHTML="Submitting";var s=new XMLHttpRequest;s.open("POST","admin/nominationhandler.php"),s.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),s.onload=function(){console.log(s.responseText),s.responseText.indexOf("Error")>-1?(document.getElementById("error-container").style.backgroundColor="#F47738",document.getElementById("error-message").innerHTML=s.responseText,setTimeout(function(){document.getElementById("error-container").style.opacity=0},2e3)):(document.getElementById("error-container").style.backgroundColor="#00823B",document.getElementById("error-message").innerHTML="Submitted Successfully",setTimeout(function(){document.getElementById("error-container").style.opacity=0,document.getElementById("error-container").style.backgroundColor="#F47738"},2e3))},s.send("nominations="+JSON.stringify(e))}var numberSelected=0;