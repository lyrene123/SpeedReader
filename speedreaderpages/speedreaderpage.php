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
  <script src="./speedreaderscript.js" type="text/javascript"></script>
</head>
<body class="masthead">
  <p>Hello <span id="user"><?php if(isset($user)) echo $user; ?></span></p>
  <div id="wordField" class="word"></div>
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
    Copyright &copy; Lyrene Labor 2017
  </footer>
</body>
</html>
