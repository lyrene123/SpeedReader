<?php
class DbConfig{
  private $serverName ='ec2-54-235-150-134.compute-1.amazonaws.com';
  private $user ='lbkfjioznvsyea';
  private $password ='6c497695ae66e599a411865fd8afc3713d2a1d39459ef9729b798a32ec0b6ef3';
  private $dbname ='d11ug94f9d0v0d';
  private $port = 5432;

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

  function getPort(){
    return $this->port;
  }
}
?>
