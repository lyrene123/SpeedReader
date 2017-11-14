<?php
/**
* Encapsulates the properties of a reader in a Speed Reader web application.
* Represents a reader with a userid, a password, the amount of login attempts,
* the last book line read by the user, and the words per minute speed.
*/
class Reader
{
  private $userid;
  private $password;
  private $loginattempts;
  private $last_line;
  private $speed;

  /**
  * Constructor that takes in input to initialize the userid, the password,
  * the login attempts, the last book line read, and the speed instance variables.
  *
  * @param userid - Username of the user
  * @param password - Password of the user
  * @param loginattempts - number of login attempts of the user
  * @param last_line - Last book line read by the user
  * @param speed - Last speed selected by user
  */
  function __construct($userid, $password, $loginattempts, $last_line, $speed){
    $this->userid = $userid;
    $this->password = $password;
    $this->loginattempts = $loginattempts;
    $this->last_line = $last_line;
    $this->speed = $speed;
  }

  /**
  * Returns the userid
  *
  * @return the userid
  */
  function getUserid(){
    return $this->userid;
  }

  /**
  * Sets the userid
  *
  * @param userid - A username
  */
  function setUserid($userid){
    $this->userid = $userid;
  }

  /**
  * Return the password
  *
  * @return the user password
  */
  function getPassword(){
    return $this->password;
  }

  /**
  * Sets the user password
  *
  * @param password - A user password
  */
  function setPassword($password){
    $this->password = $password;
  }

  /**
  * Returns the login attempts of the user
  *
  * @return the user login attempts
  */
  function getLoginAttempts(){
    return $this->loginattempts;
  }

  /**
  * Sets the login attempts
  *
  * @param la - A number of login attempts
  */
  function setLoginAttemps($la){
    $this->loginattempts = $la;
  }

  /**
  * Returns the last book line read by the user
  *
  * @return last book line read by user
  */
  function getLastLine(){
    return $this->last_line;
  }

  /**
  * Sets the last book line read by the user
  *
  * @param line - A book line
  */
  function setLastLine($line){
    $this->last_line = $line;
  }

  /**
  * Returns the reading speed wpm
  *
  * @return The wpm reading speed of the user
  */
  function getSpeed(){
    return $this->speed;
  }

  /**
  * Sets the reading speed of the user
  *
  * @param s - A wpm speed value 
  */
  function setSpeed($s){
    $this->speed = $s;
  }
}
?>
