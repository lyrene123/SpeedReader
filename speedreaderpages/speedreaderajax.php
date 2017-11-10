<?php
session_start();
if(isset($_SESSION['user'])){
  require "../daoclasses/DAOManager.php";
  require "../readerclasses/JSONResponse.php";
  $user = $_SESSION['user'];
  $daoManager = new DAOManager();

  if(isset($_SESSION['line'])){
    $userRecord = $daoManager->retrieveUserRecord($user);
    $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    while(empty($bookline)){
      $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    }
    $lineResponse = new JSONResponse($bookline, $userRecord['speed']);
    unset($_SESSION['line']);

    header('Content-Type: application/json');
    echo json_encode($lineResponse, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  }
} else {
  header('Location: ../index.php');
  exit;
}

?>
