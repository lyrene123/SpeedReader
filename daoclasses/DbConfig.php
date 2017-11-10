<?php
class DbConfig{
  private $serverName ='localhost';
  private $user ='homestead';
  private $password ='secret';
  private $dbname ='homestead';

  function getServerName(){
    return $this->serverName;
  }

  function getUser(){
    return $this->user;
  }

  function getPassword(){
    return $this->password;
  }

  function getDbName(){
    return $this->dbname;
  }
}
?>
