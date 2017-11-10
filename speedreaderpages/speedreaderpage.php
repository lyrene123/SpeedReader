
<?php
/*
1) retrieve the user record from db
2) retrieve the user's line from db.
3) split the line into words
4) display speed and each word with an interval(same as speed)
5) when line is finished, stop interval. Retrieve next line.
6) etc.


2 types of AJAX request : request for a new line + request to save new speed

*/
session_start();
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
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
  <link href="../styles/mystyles.css" rel="stylesheet">
</head>
<body class="masthead" onload="retrieveLineAndSpeedFromDb()">
  <script type="text/javascript">
  function retrieveLineAndSpeedFromDb(){
    <?php $_SESSION['line'] = 'line'; ?>
    console.log("Retrieving line and speed....");
    var req = new XMLHttpRequest();
    req.open("GET", "speedreaderajax.php", true);
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
    req.send();
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
    var wordLoop = setInterval(function(){
      console.log("Displaying word: " + wordsArr[counter]);
      wordField.value = wordsArr[counter];
      counter++;
      if(counter === wordsArr.length) {
        console.log("Stopping word loop");
        clearInterval(wordLoop);
      }
    }, speed);

  }
  </script>

  <p>Hello <span id="user"><?php if(isset($user)) echo $user; ?></span></p>
  <input id="wordField" class="word" type="text" name="word" placeholder="word" readonly>
  <select name=wpmArr[] id="wpmSelect">
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
