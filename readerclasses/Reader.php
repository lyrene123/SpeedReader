<?php
class Reader implements JsonSerializable
{
  private $userid;
  private $password;
  private $loginattempts;
  private $last_line;
  private $speed;

  function __construct($userid, $password, $loginattempts, $last_line, $speed){
    $this->userid = $userid;
    $this->password = $password;
    $this->loginattempts = $loginattempts;
    $this->last_line = $last_line;
    $this->speed = $speed;
  }

  public function jsonSerialize() {
      return [
          'last_line' => $this->last_line,
          'speed' => $this->speed
      ];
  }

  function getUserid(){
    return $this->userid;
  }

  function setUserid($userid){
    $this->userid = $userid;
  }

  function getPassword(){
    return $this->password;
  }

  function setPassword($password){
    $this->password = $password;
  }

  function getLoginAttempts(){
    return $this->loginattempts;
  }

  function setLoginAttemps($la){
    $this->loginattempts = $la;
  }

  function getLastLine(){
    return $this->last_line;
  }

  function setLastLine($line){
    $this->last_line = $line;
  }

  function getSpeed(){
    return $this->speed;
  }

  function setSpeed($s){
    $this->speed = $s;
  }
}
?>
