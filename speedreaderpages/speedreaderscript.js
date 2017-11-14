var g = {};

function determineFocusLetter(wordLength, frontElementsLength){
  var focusElements;
  var spaceCount;
  switch (true) {
    case wordLength == 1:
      focusElements = [0];
      spaceCount = 4;
      break;
    case wordLength >= 2 && wordLength <= 5:
      focusElements = [1];
      spaceCount = 3;
      break;
    case wordLength >= 6 && wordLength <= 9:
      focusElements = [2];
      spaceCount = 2;
      break;
    case wordLength >= 10 && wordLength <= 13:
      focusElements = [3];
      spaceCount = 1;
      break;
    default:
      focusElements = [4];
      spaceCount = 0;
  }

  var totalSpaces = spaceCount - frontElementsLength;
  var spaces = "";
  for(var i = 1; i <= totalSpaces; i++){
    spaces += "&nbsp;";
  }
  focusElements.push(spaces);
  return focusElements;
}

function buildWord(word){
  var frontElements = extractNonLettersFront(word);
  var backElements = extractNonLettersBack(word);
  var wordElement = extractWordFromStr(word);

  /*if(frontElements === wordElement){
    frontElements = "";
  }

  if(backElements === wordElement){
    backElements = "";
  }*/

  var focusElements = determineFocusLetter(wordElement.length, frontElements.length);
  var formattedWord = focusElements[1] + frontElements;
  /*for(var i = 0; i < wordElement.length; i++){
    if(i == focusElements[0]){
      formattedWord += '<span class="focus">' + wordElement.charAt(focusElements[0]) + "</span>";
    } else {
      formattedWord += wordElement.charAt(i);
    }
  }*/
  formattedWord += wordElement.substring(0, focusElements[0]);
  formattedWord += '<span class="focus">' + wordElement.charAt(focusElements[0]) + "</span>";
  //if(focusElements[0]+1 <= wordElement.length){
    formattedWord += wordElement.substring(focusElements[0]+1);
  //}
  formattedWord += backElements;
  return formattedWord;
}

function extractWordFromStr(word){
  return word.substring(g.nonLetterIndexFront, g.nonLetterIndexBack);
}

function extractNonLettersFront(word){
  var nonLetters = "";
  g.nonLetterIndexFront = 0;
  for(var i = 0; i < word.length; i++){
    if(word.charAt(i).match(/[a-zA-Z]/)){
      break;
    }
    nonLetters += word.charAt(i);
    g.nonLetterIndexFront = i + 1;
  }
  if(nonLetters === word){
    return "";
  } else {
    return nonLetters;
  }
}

function extractNonLettersBack(word){
  var nonLetters = "";
  g.nonLetterIndexBack = word.length;
  for(var i = word.length - 1; i >= 0; i--){
    if(word.charAt(i).match(/[a-zA-Z]/)){
      break;
    }
    nonLetters += word.charAt(i);
    g.nonLetterIndexBack = i;
  }
  if(nonLetters.length > 0){
    var nonLettersArr = nonLetters.split("");
    var nonLettersArr = nonLettersArr.reverse();
    nonLetters =  nonLettersArr.join("");
  }

  if(nonLetters === word){
    return "";
  } else {
    return nonLetters;
  }
}

//https://codepen.io/easymac/pen/GgwEgL?editors=0010
function calculatePauseLength(speed){
  var wordsPerSecond = Math.round(speed / 60);
  var pause = Math.round(1000 / wordsPerSecond);
  return pause;
}

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

function displaySpeed(speed){
  for (var i = 0; i < g.wpmSelect.options.length; i++) {
    if (g.wpmSelect.options[i].text == speed) {
      g.wpmSelect.options[i].selected = true;
      return;
    }
  }
}

function updateSpeed(){
  var selectedSpeed = g.wpmSelect.value;
  if(selectedSpeed.match(/^\d+$/) && g.speedArr.indexOf(selectedSpeed) !== -1){
    if(parseInt(selectedSpeed) >= 50 && parseInt(selectedSpeed) <= 2000){
      var req = new XMLHttpRequest();
      req.open("POST", "speedreaderajax.php", true);
      req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
      req.send('selectedSpeed=' + selectedSpeed);
    }
  }
}

function retrieveInitialLineAndSpeed(){
  //console.log("Initial line and speed set up....");
  retrieveLineAndSpeedFromDb('initial');
}

function retrieveNextLineAndSpeed(){
//  console.log("Retrieving next line....");
  retrieveLineAndSpeedFromDb('next');
}

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
    }
  };
  req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
  req.send('request=' + request);
}

function addEvent(obj, type, fn) {
  if (obj && obj.addEventListener) {
    obj.addEventListener(type, fn, false);
  }
  else if (obj && obj.attachEvent) {
    obj.attachEvent("on"+type, fn);
  }
}

function logoutSession(){
  clearInterval(g.wordLoop);
  var req = new XMLHttpRequest();
  req.open("POST", "speedreaderajax.php", true);
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      window.location.reload(false);
    }
  };
  req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
  req.send('request=logout');
}

function init(){
  g.wpmSelect = document.getElementById("wpmSelect");
  g.wordField = document.getElementById("wordField");
  g.logout = document.getElementById("logout");
  g.nonLetterIndexFront = 0;
  g.nonLetterIndexBack = 0;
  g.wordLoop = null;
  var counter = 50;
  var max = 2000;
  g.speedArr = [];
  for(var i = 50; i <= 2000; i = i + 50){
    g.speedArr.push(i+"");
  }

  addEvent(g.wpmSelect, "change", updateSpeed);
  addEvent(g.logout, "click", logoutSession);
  retrieveInitialLineAndSpeed();
}

window.onload = init;
