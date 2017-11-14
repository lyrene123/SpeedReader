<?php
/**
* Validate whether or not the user is logged in and the request is a POST.
* If not, then redirect to the index page. If valid, then proceed to process
* the request.
*/
session_start();
session_regenerate_id();
if(isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
  require "../daoclasses/DAOManager.php";
  require "../readerclasses/JSONResponse.php";
  $user = $_SESSION['user'];
  $daoManager = new DAOManager();
  $userRecord;
  $bookline;

  //if requesting for the initial book line and speed of the user from the db
  if(isset($_POST['request']) && $_POST['request'] === 'initial'){
    $userRecord = $daoManager->retrieveUserRecord($user);
    $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
    //keep on retrieving a line with words if line is empty
    while(empty($bookline)){
      retrieveNextLine();
    }
    sendJSONResponse(null);
  } else if(isset($_POST['request']) && $_POST['request'] === 'next'){
    //if requesting for the next book line, do the following
    retrieveNextLine();
    //keep on retrieving a line with words if line is empty
    while(empty($bookline)){
      retrieveNextLine();
    }
    sendJSONResponse(null);
  } else{
    //if requesting to update the user speed with a new speed
    if (isset($_POST['selectedSpeed'])){
       $daoManager->updateUserSpeed($user, $_POST['selectedSpeed']);
    }
  }
} else {
  header('Location: ../index.php');
  exit;
}

/**
* Builds the JSON Response to echo back and sends it.
*
* @param data - data to be sent with the reponse.
*/
function sendJSONResponse($data){
  global $bookline, $userRecord;
  header('Content-Type: application/json');
  if($data == null){
    $lineResponse = new JSONResponse($bookline, $userRecord['speed']);
    echo json_encode($lineResponse, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  } else {
    echo json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
  }
}

/**
* Retrieves the next line in the database for the user logged in. Updates the
* book line for that user.
*/
function retrieveNextLine(){
 global $daoManager, $userRecord, $bookline, $user;
 $daoManager->updateUserBookLine($user);
 $userRecord = $daoManager->retrieveUserRecord($user);
 $bookline = $daoManager->retrieveBookLine($userRecord['last_line']);
}
?>
