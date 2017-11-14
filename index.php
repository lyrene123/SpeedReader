<?php
/**
* The following index page handles the user authentication for the Speed Reader
* website. If the login/registration form has not yet been submitted,
* then the login/registration will be displayed. If form has been submitted,
* then user input will be validated based on if the userid already exists or not.
* If userid provided exists, then a login will be performed and if not, then a
* registration will be performed.
*/
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require "daoclasses/DAOManager.php";
	$user="";
	$password="";
	$messageUser = validateUser();
	$messagePassword = validatePassword();

	//if userid and password are valid, then login/register
	if(($messageUser == null || empty($messageUser)) &&
	($messagePassword == null || empty($messagePassword))){
		handleUserLoginCreation();
	} else {
		//display error message if userid/password invalid with the form
		$message = $messageUser == null ? $messagePassword : $messageUser;
		include 'loginpages/loginpage.php';
	}
} else {
	//if form is not submitted, display form
	$message = "";
	include 'loginpages/loginpage.php';
}

/**
* Handles the form's submission based on the existance of the userid.
* If the userid exists, then the user will be logged in. If userid doesn't
* exists, then user will be registrated.
*/
function handleUserLoginCreation(){
	global $user, $password;
	$daoManager = new DAOManager();
	if($daoManager->isUserExists($user)){
		handleUserLogin($daoManager);
	} else {
		handleUserCreation($daoManager);
	}
}

/**
* Handles the user login. Checks the amount of login attempts, and blocks user
* if too many login attemps. If login attempts is okay, then verify the user
* password combination.
*/
function handleUserLogin(DAOManager $daoManager){
	global $user, $password;
	if($daoManager->getLoginAttempts($user) === 3){
		handleBlockedUser($daoManager);
	} else {
		handleUserPasswdVerification($daoManager);
	}
}

/**
* Handles the blocking of a user account when too many login attempts has been
* made. If user is blocked, an error page will be displayed.
*/
function handleBlockedUser(DAOManager $daoManager){
	global $user, $password;
	//if user is not yet in the blocked user table, then add user to that table + display error page
	if(!$daoManager->isBlockedUserExists($user)){
		if($daoManager->addBlockedUser($user)){
			header('Location: loginpages/loginpageerror.php');
			exit;
		}
	} else {
		//if user is already in 
		if($daoManager->isUserStillBlocked($user)){
			header('Location: loginpages/loginpageerror.php');
			exit;
		} else {
			$daoManager->resetLoginAttempts($user);
			$daoManager->unblockUser($user);
			handleUserPasswdVerification($daoManager);
		}
	}
}

function handleUserPasswdVerification(DAOManager $daoManager){
	global $user, $password;
	$hash = $daoManager->getHash($user);
	if (!password_verify($password, $hash)){
		$daoManager->incrementLoginAttempts($user);
		$message = "Wrong username and/or password";
		include 'loginpages/loginpage.php';
	} else {
		$daoManager->resetLoginAttempts($user);
		handleAuthenticatedUser();
	}
}

function handleUserCreation(DAOManager $daoManager){
	global $user, $password;
	$daoManager->addUser($user, password_hash($password, PASSWORD_DEFAULT));
	handleAuthenticatedUser();
}

function handleAuthenticatedUser(){
	global $user;
	session_start();
	session_regenerate_id();
	$_SESSION['user'] = $user;
	header('Location: speedreaderpages/speedreaderpage.php');
	exit;
}

function validateUser(){
	global $user;
	if(!empty($_POST['username']) && strlen($_POST['username']) !== 0){
		$user = htmlentities($_POST['username']);
	}else{
		return "Please enter your username and password";
	}
}

function validatePassword(){
	global $password;
	if(!empty($_POST['password']) && strlen($_POST['password']) !== 0){
		$password = htmlentities($_POST['password']);
	}else{
		return "Please enter your username and password";
	}
}
?>
