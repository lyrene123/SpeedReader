<?php
session_start();
if(isset($_SESSION['user'])){
  require "../daoclasses/DAOManager.php";
  require "../readerclasses/JSONResponse.php";
  $user = $_SESSION['user'];
  $daoManager = new DAOManager();

  if(isset($_SESSION['initialline'])){
    $userRecord = $daoManager->retrieveUserRecord($user);
    $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    while(empty($bookline)){
      $daoManager->updateUserBookLine($user);
      $userRecord = $daoManager->retrieveUserRecord($user);
      $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    }
    $lineResponse = new JSONResponse($bookline, $userRecord['speed']);
    unset($_SESSION['initialline']);
    header('Content-Type: application/json');
    sendJsonReponse($lineResponse);
  } else if(isset($_SESSION['nextline'])){
    $daoManager->updateUserBookLine($user);
    $userRecord = $daoManager->retrieveUserRecord($user);
    $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    while(empty($bookline)){
      $daoManager->updateUserBookLine($user);
      $userRecord = $daoManager->retrieveUserRecord($user);
      $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    }
    $lineResponse = new JSONResponse($bookline, $userRecord['speed']);
    header('Content-Type: application/json');
    sendJsonReponse($lineResponse);
  }

} else {
  header('Location: ../index.php');
  exit;
}

function sendJsonReponse($lineResponse){
  echo json_encode($lineResponse, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
}
?>
