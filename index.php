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
		//display error message if userid/password invalid
		$message = $messageUser == null ? $messagePassword : $messageUser;
		include 'loginpages/loginpage.php';
	}
} else {
	$message = "";
	include 'loginpages/loginpage.php';
}

function handleUserLoginCreation(){
	global $user, $password;
	$daoManager = new DAOManager();
	if($daoManager->isUserExists($user)){
		handleUserLogin($daoManager);
	} else {
		handleUserCreation($daoManager);
	}
}

function handleUserLogin(DAOManager $daoManager){
	global $user, $password;
	if($daoManager->getLoginAttempts($user) === 3){
		handleBlockedUser($daoManager);
	} else {
		handleUserPasswdVerification($daoManager);
	}
}

function handleBlockedUser(DAOManager $daoManager){
	global $user, $password;
	if(!$daoManager->isBlockedUserExists($user)){
		if($daoManager->addBlockedUser($user)){
			header('Location: loginpages/loginpageerror.php');
			exit;
		}
	} else {
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
