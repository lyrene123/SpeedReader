<?php
session_start();
session_regenerate_id();
if(isset($_SESSION['user'])){
  $user = $_SESSION['user'];
} else {
  header('Location: ../index.php');
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Speed Reader</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Dekko:400,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Permanent+Marker:400,700,400italic,700italic" rel="stylesheet" type="text/css">
  <link href="../styles/mystyles.css" rel="stylesheet">
</head>
<body class="masthead" onload="retrieveInitialLineAndSpeed()">
  <script type="text/javascript">
  function retrieveInitialLineAndSpeed(){
    console.log("Initial line and speed set up....");
    retrieveLineAndSpeedFromDb('initial');
  }

  function retrieveNextLineAndSpeed(){
    console.log("Retrieving next line....");
    retrieveLineAndSpeedFromDb('next');
  }

  function retrieveLineAndSpeedFromDb(request){
    console.log("Retrieving line and speed....");
    var req = new XMLHttpRequest();
    req.open("POST", "speedreaderajax.php", true);
    req.onreadystatechange = function() {
      if (req.readyState == 4 && req.status == 200) {
        var jsonResponse = JSON.parse(req.responseText);
        if(jsonResponse !== null){
          var line = jsonResponse.book_line;
          var speed = jsonResponse.speed;
          console.log("current line: " + line + " speed: " + speed);
          displaySpeed(speed);
          displayLine(line+"", speed);
        }
      }
    };
    req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    req.send('request=' + request);
  }

  function updateSpeed(selectedSpeed){
    console.log("Updating speed with " + selectedSpeed);
    var req = new XMLHttpRequest();
    req.open("POST", "speedreaderajax.php", true);
    req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    req.send('selectedSpeed=' + selectedSpeed);
  }

  function displaySpeed(speed){
    var wpmSelect = document.getElementById("wpmSelect");
    for (var i = 0; i < wpmSelect.options.length; i++) {
      if (wpmSelect.options[i].text == speed) {
        wpmSelect.options[i].selected = true;
        return;
      }
    }
  }

  function displayLine(line, speed){
    var wordsArr = line.split(' ');
    var counter = 0;
    var wordField = document.getElementById("wordField");
    var pauseLength = calculatePauseLength(speed);
    var wordLoop = setInterval(function(){
      console.log("Displaying word: " + wordsArr[counter]);
      wordField.innerHTML = buildWord(wordsArr[counter]);
      counter++;
      if(counter === wordsArr.length) {
        console.log("End of sentence");
        clearInterval(wordLoop);
        retrieveNextLineAndSpeed();
      }
    }, pauseLength);
  }

  function determineFocusLetter(wordLength){
    return Math.floor((wordLength + 1) / 3) + 1;
  }

  function buildWord(word){
    var focusPos = determineFocusLetter(word.length);
    var formattedWord = "";
    for(var i = 0; i < word.length; i++){
      if(i === (focusPos - 1)){
        formattedWord += '<span class="focus">' + word.charAt(i) + "</span>";
      } else {
        formattedWord += word.charAt(i);
      }
    }
    return formattedWord;
  }

  //https://codepen.io/easymac/pen/GgwEgL?editors=0010
  function calculatePauseLength(speed){
    var wordsPerSecond = Math.round(speed / 60);
    var pause = Math.round(1000 / wordsPerSecond);
    return pause;
  }
  </script>

  <p>Hello <span id="user"><?php if(isset($user)) echo $user; ?></span></p>
  <div id="wordField" class="word"></div>
  <select name=wpmArr[] id="wpmSelect" onchange="updateSpeed(this.value)">
    <?php
    $counter = 50;
    $max = 2000;
    for($i = 50; $i <= 2000; $i = $i + 50){
      echo "<option value=" . $i . ">" . $i . "</option>";
    }
    ?>
  </select>
  <footer>
    Copyright &copy Lyrene Labor 2017
  </footer>
</body>
</html>
