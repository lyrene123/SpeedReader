//global namespace variable g that will hold necessary global variables
var g = {};

/**
* Determines the position of the focus letter along with the number of spaces
* to append at the front of the word based on the word/token length.
*
* @return Array containing the focus letter position and the string spaces to
*         to append at the front of the word.
*/
function determineFocusLetter(wordLength){
  var focusElements;
  switch (true) {
    case wordLength == 1:
      focusElements = [0, "&nbsp;&nbsp;&nbsp;&nbsp;"];
      break;
    case wordLength >= 2 && wordLength <= 5:
      focusElements = [1, "&nbsp;&nbsp;&nbsp;"];
      break;
    case wordLength >= 6 && wordLength <= 9:
      focusElements = [2, "&nbsp;&nbsp;"];
      break;
    case wordLength >= 10 && wordLength <= 13:
      focusElements = [3, "&nbsp;"];
      break;
    default:
      focusElements = [4, ""];
  }
  return focusElements;
}

/**
* Builds and formats the word to be displayed. The word will contain the
* right amount of spaces appended at the front of the word, and the word will
* have the focus letter in red.
*
* @return The formatted word containing the appended spaces and the focus letter
*         in red.
*/
function buildWord(word){
  var focusElements = determineFocusLetter(word.length);
  var formattedWord = focusElements[1];
  for(var i = 0; i < word.length; i++){
    if(i == focusElements[0]){
      formattedWord += '<span class="focus">' + word.charAt(focusElements[0]) + "</span>";
    } else {
      formattedWord += word.charAt(i);
    }
  }
  return formattedWord;
}

/**
* Calculates the pause length based on the wpm speed selected by the user.
* Solution taken from https://codepen.io/easymac/pen/GgwEgL?editors=0010
*
* @return the calculated pause length
*/
function calculatePauseLength(speed){
  var wordsPerSecond = Math.round(speed / 60);
  var pause = Math.round(1000 / wordsPerSecond);
  return pause;
}

/**
* Displays a book line by displaying word by word based on the wpm speed
* selected by the user.
*
* @param {String} line
*         A book line
* @param {String} speed
*         Wpm speed selected by user in String format
*/
function displayLine(line, speed){
  var wordsArr = line.split(' ');
  var counter = 0;
  var pauseLength = calculatePauseLength(speed);
  g.wordLoop = setInterval(function(){
    g.wordField.innerHTML = buildWord(wordsArr[counter]);
    counter++;
    if(counter === wordsArr.length) {
      clearInterval(g.wordLoop);
      retrieveNextLineAndSpeed();
    }
  }, pauseLength);
}

/**
* Displays the speed retrieved from the database on the select drop down list
*
* @param {String} speed
*         Speed selected by user in String format
*/
function displaySpeed(speed){
  for (var i = 0; i < g.wpmSelect.options.length; i++) {
    if (g.wpmSelect.options[i].text == speed) {
      g.wpmSelect.options[i].selected = true;
      return;
    }
  }
}

/**
* Event handler for the select onchange event.
* Updates the speed with the new speed selected by the user by making an ajax
* request to the database to update the user's speed in the table.
*/
function updateSpeed(){
  var selectedSpeed = g.wpmSelect.value;
  //validates first if the selected speed is a number between 50 - 20000
  if(selectedSpeed.match(/^\d+$/) && g.speedArr.indexOf(selectedSpeed) !== -1){
    var req = new XMLHttpRequest();
    req.open("POST", "speedreaderajax.php", true);
    req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    req.send('selectedSpeed=' + selectedSpeed);
  }
}

/**
* Retrieves the initial line and speed from the database when user first logs in.
*/
function retrieveInitialLineAndSpeed(){
  retrieveLineAndSpeedFromDb('initial');
}

/**
* Retrieves the next line and speed from the database.
*/
function retrieveNextLineAndSpeed(){
  retrieveLineAndSpeedFromDb('next');
}

/**
* Retrieves a line and speed from the database depending if the request is the
* initial line or the next line in the book. Sends an ajax request with the type
* of request.
*
* @param {String} request
*         Type of request to be sent in the ajax message for the initial line
          or the next line in the book.
*/
function retrieveLineAndSpeedFromDb(request){
  var req = new XMLHttpRequest();
  req.open("POST", "speedreaderajax.php", true);
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      var jsonResponse = JSON.parse(req.responseText);
      if(jsonResponse !== null){
        var line = jsonResponse.book_line;
        var speed = jsonResponse.speed;
        displaySpeed(speed);
        displayLine(line+"", speed);
      }
    } else if (req.readyState == 4) {
      g.speedReaderError.innerText = "Something bad happened. Problem displaying words.";
    }
  };
  req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
  req.send('request=' + request);
}

/**
* Adds an event with browser compatibility
*/
function addEvent(obj, type, fn) {
  if (obj && obj.addEventListener) {
    obj.addEventListener(type, fn, false);
  }
  else if (obj && obj.attachEvent) {
    obj.attachEvent("on"+type, fn);
  }
}

/**
* Initializes the global variables. Retrieves handles to some html objects such
* as the speed select drop down list, the word field, etc. Initializes
* the array of valid wpm speed to be used to compare with the user's speed selection
* whether or not it's a valid speed that we expect. Attaches the onchange event
* to the select object. Retrieves the initial line and speed.
*/
function init(){
  g.wpmSelect = document.getElementById("wpmSelect");
  g.wordField = document.getElementById("wordField");
  g.speedReaderError = document.getElementById("speedReaderError");
  g.wordLoop = null;
  var counter = 50;
  var max = 2000;
  g.speedArr = [];
  for(var i = 50; i <= 2000; i = i + 50){
    g.speedArr.push(i+"");
  }

  addEvent(g.wpmSelect, "change", updateSpeed);
  retrieveInitialLineAndSpeed();
}

window.onload = init;
