<?php
/**
* Encapsulates the properties and the credentials used to access the database.
*/
class DbConfig{
  private $serverName ='ec2-54-235-150-134.compute-1.amazonaws.com';
  private $user ='lbkfjioznvsyea';
  private $password ='6c497695ae66e599a411865fd8afc3713d2a1d39459ef9729b798a32ec0b6ef3';
  private $dbname ='d11ug94f9d0v0d';
  private $port = 5432;

  /**
  * Returns the server name
  *
  * @return the server name
  */
  function getServerName(){
    return $this->serverName;
  }

  /**
  * Returns the db username
  *
  * @return database username
  */
  function getUser(){
    return $this->user;
  }

  /**
  * Returns the db password
  *
  * @return the db password
  */
  function getPassword(){
    return $this->password;
  }

  /**
  * Returns the database name
  *
  * @return the database name
  */
  function getDbName(){
    return $this->dbname;
  }

  /**
  * Returns the port number
  *
  * @return the port number
  */
  function getPort(){
    return $this->port;
  }
}
?>
