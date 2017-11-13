<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Speed Reader</title>
  <link href="../styles/bootstrap.min.css" rel="stylesheet">
  <link href="/styles/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Dekko:400,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Permanent+Marker:400,700,400italic,700italic" rel="stylesheet" type="text/css">
  <link href="../styles/mystyles.css" rel="stylesheet">
</head>
<body class="masthead">
  <div class="container">
    <div class="intro-text">
      <span class="name">Welcome to Speed Reader</span>
      <hr class="star-light">
      <span class="login">Login to start reading!</span>
    </div>
  </div>
  <p class="errorMessage"><?php if(isset($message)) echo $message; ?></p>
  <div class="login-page">
    <div class="form">
      <form action="" method="post" class="login-form">
        <input class="loginField" type="text" name="username" placeholder="username" value="<?php if(isset($user)) echo $user; ?>" />
        <input class="loginField" type="password" name="password" placeholder="password" value="<?php if(isset($password)) echo $password; ?>" />
        <input class="loginField" type="submit" name="submit" placeholder="login"/>
        <p class="message">Not registered? Simply enter a username and a
          password and you will be automatically registered!</p>
        </form>
      </div>
  </div>
  <footer>
    Copyright &copy Lyrene Labor 2017
  </footer>
  </body>
</html>
