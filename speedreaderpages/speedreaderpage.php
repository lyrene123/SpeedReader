<?php
/**
* Validate whether or not the user has logged in.
* If yes, then proceed to display the speed reader page.
* If not, then redirect to the index page.
*/
session_start();
session_regenerate_id();
if(isset($_SESSION['user'])){
  $user = $_SESSION['user'];
} else {
  header('Location: ../index.php');
  exit;
}
?>

<!-- The following html page will display the speed reader activity. Contains
      the display for the words of a book line, a select drop down list for the
      user to choose a speed, the title and author of the book, the source text,
      and a log out button. -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Speed Reader</title>
  <link href="../bootstrapstyles/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../bootstrapstyles/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Dekko:400,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Permanent+Marker:400,700,400italic,700italic" rel="stylesheet" type="text/css">
  <link href="../styles/mystyles.css" rel="stylesheet">
  <script src="./speedreaderscript.js" type="text/javascript"></script>
</head>
<body class="masthead">
  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
      <span class="navbar-brand">Speed Reader</span>
      <div class="col-xs-3 pull-rights">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <form action="../index.php" method="get">
              <input type="submit" name="submit" class="btn btn-purple" value="Logout" />
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="masthead" id="speedReaderBody">
    <p class="greetings">Hello <span id="user"><?php if(isset($user)) echo $user; ?>,</span>
        You're currently reading :</p>
    <p class="story">The Wizard Of Oz by L. Frank Baum</p>
    <p class="credits">Taken from
      <a href="http://www.textfiles.com/etext/FICTION/wizrd_oz">here</a> based on Project Gutenberg</p>
    <div id="wordField" class="word">loading...</div>
    <div class="speedSelect">
      <select name=wpmArr[] id="wpmSelect">
        <?php
        //construct the options of the select by adding values between 50 to 20000 interval of 50
        $counter = 50;
        $max = 2000;
        for($i = 50; $i <= 2000; $i = $i + 50){
          echo "<option value=" . $i . ">" . $i . "</option>";
        }
        ?>
    </select>
    </div>
    <span>speed(wpm)</span>
  </div>
  <p class="errorMessage" id="speedReaderError"></p>
</body>
</html>
