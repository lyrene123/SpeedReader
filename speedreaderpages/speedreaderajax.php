<?php
session_start();
session_regenerate_id();
if(isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
  require "../daoclasses/DAOManager.php";
  require "../readerclasses/JSONResponse.php";
  $user = $_SESSION['user'];
  $daoManager = new DAOManager();
  $userRecord;
  $bookline;
  if(isset($_POST['request']) && $_POST['request'] === 'initial'){
    $userRecord = $daoManager->retrieveUserRecord($user);
    $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    while(empty($bookline)){
      retrieveNextLine();
    }
    sendJSONResponse();
  }

  if(isset($_POST['request']) && $_POST['request'] === 'next'){
    retrieveNextLine();
    while(empty($bookline)){
      retrieveNextLine();
    }
    sendJSONResponse();
  }

  if(isset($_POST['request']) && $_POST['request'] === 'logout'){
    $_SESSION = array();
    session_destroy();
    header('Content-Type: application/json');
    echo json_encode("logout", JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  }

  if(isset($_POST['selectedSpeed'])){
    //if(validateSpeedSelection()){
      $daoManager->updateUserSpeed($user, $_POST['selectedSpeed']);
  //  }
  }
} else {
  header('Location: ../index.php');
  exit;
}

function sendJSONResponse(){
  global $bookline, $userRecord;
  $lineResponse = new JSONResponse($bookline, $userRecord['speed']);
  header('Content-Type: application/json');
  echo json_encode($lineResponse, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
}

function retrieveNextLine(){
 global $daoManager, $userRecord, $bookline, $user;
 $daoManager->updateUserBookLine($user);
 $userRecord = $daoManager->retrieveUserRecord($user);
 $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
}

function validateSpeedSelection(){
  $speedArr = buildSpeedArr();
  $selection = $_POST['selectedSpeed'];
  if(is_numeric($selection) && in_array($selection, $speedArr)){
    return true;
  } else {
    return false;
  }
}

function buildSpeedArr(){
  $counter = 50;
  $max = 2000;
  $speedArr = array();
  for($i = 50; $i <= 2000; $i = $i + 50){
    $speedArr[] = $i;
  }
  return $speedArr;
}
?>
