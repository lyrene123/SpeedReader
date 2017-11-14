<?php
/**
* The following index page handles the user authentication for the Speed Reader
* website. If the login/registration form has not yet been submitted,
* then the login/registration will be displayed. If form has been submitted,
* then user input will be validated based on if the userid already exists or not.
* If userid provided exists, then a login will be performed and if not, then a
* registration will be performed.
*
* Note: Bootstrap styling taken from https://github.com/BlackrockDigital/startbootstrap-freelancer
*				and form styling taken from https://codepen.io/colorlib/pen/rxddKy
*/
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require "daoclasses/DAOManager.php";
	$user="";
	$password="";
	$messageUser = validateUser();
	$messagePassword = validatePassword();

	//if userid and password a re valid, then login/register
	if(($messageUser == null || empty($messageUser)) &&
	($messagePassword == null || empty($messagePassword))){
		handleUserLoginCreation();
	} else {
		//display error message if userid/password invalid with the form
		$message = $messageUser == null ? $messagePassword : $messageUser;
		include 'loginpages/loginpage.php';
	}
} else {
	//if GET request was made to logout from the sessin, then destroy session.
	if(isset($_GET['submit']) && $_GET['submit'] == 'Logout'){
		session_start();
		$_SESSION = array();
    session_destroy();
	}

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
*
* @param daoManager - DAOManager object
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
*
* @param daoManager - DAOManager object
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
		//if user is already blocked, then check if user is still in the timeout interval
		if($daoManager->isUserStillBlocked($user)){
			header('Location: loginpages/loginpageerror.php');
			exit;
		} else {
			//if user no longer blocked, then reset the login attempts and unblock user, then
			//verify the userid/password combination
			$daoManager->resetLoginAttempts($user);
			$daoManager->unblockUser($user);
			handleUserPasswdVerification($daoManager);
		}
	}
}

/**
* Handles the userid/password combination if it's valid. Retrieves the password
* hash from the database associated to the userid, and compares it with the
* password entered in the form. If valid, then login attempts are reset and user can login.
* If not valid, then user's login attempts are incremented and error message
* is displayed along with the form once again.
*
* @param daoManager - DAOManager object
*/
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

/**
* Handles the user registration and auto login the user once registration
* is done.
*
* @param daoManager - DAOManager object
*/
function handleUserCreation(DAOManager $daoManager){
	global $user, $password;
	$daoManager->addUser($user, password_hash($password, PASSWORD_DEFAULT));
	handleAuthenticatedUser();
}

/**
* Handles the login of an authenticated user. A session is started and redirect
* the user to the speed reader page.
*/
function handleAuthenticatedUser(){
	global $user;
	session_start();
	session_regenerate_id();
	$_SESSION['user'] = $user;
	header('Location: speedreaderpages/speedreaderpage.php');
	exit;
}

/**
* Validate the userid input. Must not be empty
*/
function validateUser(){
	global $user;
	if(!empty($_POST['username'])){
		$user = htmlentities($_POST['username']);
	}else{
		return "Please enter your username and password";
	}
}

/**
* Validates the password input. Must not be empty.
*/
function validatePassword(){
	global $password;
	if(!empty($_POST['password'])){
		$password = htmlentities($_POST['password']);
	}else{
		return "Please enter your username and password";
	}
}
?>
