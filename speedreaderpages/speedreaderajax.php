<?php
session_start();
session_regenerate_id();
if(isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
  require "../daoclasses/DAOManager.php";
  require "../readerclasses/JSONResponse.php";
  $user = $_SESSION['user'];
  $daoManager = new DAOManager();

  if(isset($_POST['request']) && $_POST['request'] == 'initial'){
    $userRecord = $daoManager->retrieveUserRecord($user);
    $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    while(empty($bookline)){
      $daoManager->updateUserBookLine($user);
      $userRecord = $daoManager->retrieveUserRecord($user);
      $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    }
    $lineResponse = new JSONResponse($bookline, $userRecord['speed']);
    header('Content-Type: application/json');
    echo json_encode($lineResponse, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  }

  if(isset($_POST['request']) && $_POST['request'] == 'next'){
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
    echo json_encode($lineResponse, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  }

  if(isset($_POST['selectedSpeed'])){
    if(validateSpeedSelection()){
      $daoManager->updateUserSpeed($user, $_POST['selectedSpeed']);
    }
    echo json_encode("updated", JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  }
} else {
  header('Location: ../index.php');
  exit;
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
