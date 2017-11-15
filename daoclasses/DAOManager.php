<?php
/**
* Encapsulates the properties and behavior of a Database Access Object helper
* class in the Speed Reader web application. Contains access to database credentials
* and methods to retrieve specific records from the database.
*/
class DAOManager
{
  private $serverName;
  private $user;
  private $password;
  private $dbname;
  private $port;

  /**
  * Constructor to initialize the database credentials and register
  * class path files to automatically recognize class names when declared
  * for the first time.
  */
  public function __construct() {
    spl_autoload_register(function($class){
      $file = 'daoclasses/' . $class . '.php';
      if (file_exists($file))
      {
        require($file);
      }
    });
    spl_autoload_register(function($class){
      $file = '../daoclasses/' . $class . '.php';
      if (file_exists($file))
      {
        require($file);
      }
    });

    $dbConfig = new DbConfig();
    $this->serverName = $dbConfig->getServerName();
    $this->user = $dbConfig->getUser();
    $this->password = $dbConfig->getPassword();
    $this->dbname = $dbConfig->getDbName();
    $this->port = $dbConfig->getPort();
  }

  /**
  * Creates the tables necessary for the web application which are the book table,
  * the user_reader table, and the blockeduser_reader table. They dropped if existing
  * before recreated.
  */
  function createTables(){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $query = "DROP TABLE IF EXISTS user_reader;
      DROP TABLE IF EXISTS book;
      DROP TABLE IF EXISTS blockeduser_reader;
      CREATE TABLE book(
        line serial UNIQUE,
        book_line varchar(255) NOT NULL DEFAULT ''
      );
      CREATE TABLE user_reader (
        userid varchar(60) NOT NULL PRIMARY KEY,
        password varchar(255) NOT NULL,
        loginattempts integer NOT NULL DEFAULT 0,
        last_line integer references book(line),
        speed integer NOT NULL DEFAULT 100
      );
      CREATE TABLE blockeduser_reader(
        userid varchar(60) NOT NULL PRIMARY KEY,
        timeblocked timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
      );";
      $pdo->exec($query);
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Fill the book table with lines of a book taken from the link
  * http://www.textfiles.com/etext/FICTION/wizrd_oz
  */
  function fillBookTable(){
    $myfile = fopen("http://www.textfiles.com/etext/FICTION/wizrd_oz", "r");
    $prevline = "";
    while(!feof($myfile)){
      $line = trim(fgets($myfile));
      if(empty($line) && empty($prevline)){
        $prevline = $line;
        continue;
      }
      try{
        $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $query = $pdo->prepare('INSERT INTO book (book_line) VALUES (?)');
        $query->bindValue(1,$line);
        $query->execute();
      } catch (PDOException $e){
        echo $e->getMessage();
      } finally {
        unset($pdo);
      }
      $prevline = $line;
    }
  }

  /**
  * Check if a username exists in the database.
  *
  * @param userid - a username to check in the database
  * @return boolean true or false if username is found in the database
  */
  function isUserExists($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT * FROM user_reader WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query->execute();
      $result = $query->fetchAll();
      if(count($result) > 0){
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Adds a user in the user_reader table.
  *
  * @param userid - a username
  * @param password - a password
  * @return boolean true or false if record added successfully
  */
  function addUser($userid, $pwd){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("INSERT INTO user_reader(userid, password,
      loginattempts, last_line, speed) VALUES (?,?,?,?,?)");
      if($query -> execute(["$userid","$pwd", 0, 1, 100])){
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Returns the amount of login attempts of a user.
  *
  * @param userid - a username
  * @return amount of login attempts for a user
  */
  function getLoginAttempts($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT loginattempts FROM user_reader WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
      $result = $query->fetchAll();
      return $result[0][0];
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Returns the password hash of a user
  *
  * @param userid - a username
  * @return a password hash
  */
  function getHash($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT password FROM user_reader WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
      $result = $query->fetchAll();
      return $result[0][0];
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Increments the login attempts for a user
  *
  * @param userid - a username
  */
  function incrementLoginAttempts($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("UPDATE user_reader SET loginattempts = loginattempts + 1 WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Resets the login attempts for a user to 0
  *
  * @param userid - a username
  */
  function resetLoginAttempts($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("UPDATE user_reader SET loginattempts = 0 WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Adds a blocked user in the blockeduser_reader table
  *
  * @param userid - a username
  * @return boolean if record was added
  */
  function addBlockedUser($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("INSERT INTO blockeduser_reader(userid,timeblocked)
      VALUES (?,CURRENT_TIMESTAMP)");
      if($query -> execute(["$userid"])){
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Checks if a user exists in the blockeduser_reader table
  *
  * @param userid - a username
  * @return boolean if user was found in the blockeduser_reader table
  */
  function isBlockedUserExists($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT * FROM blockeduser_reader WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query->execute();
      $result = $query->fetchAll();
      if(count($result) > 0){
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Checks if a user is still blocked based on the timeout interval (5 minutes)
  *
  * @param userid - a username
  * @return boolean is user is still blocked
  */
  function isUserStillBlocked($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT * FROM blockeduser_reader WHERE userid = ?
      AND timeblocked > (now() - interval '5 minute')");
      $query->bindValue(1,$userid);
      $query->execute();
      $result = $query->fetchAll();
      if(count($result) > 0){
        return true; //still blocked, still in the 5 minutes timeout
      } else {
        return false; //no longer blocked, 5 minutes timeout have passed
      }
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Unblocks the user by removing its record from the blockeduser_reader table
  *
  * @param userid - a username
  */
  function unblockUser($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("DELETE FROM blockeduser_reader WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Retrieves the book line based on a number
  *
  * @param line - a book line number
  * @return the book line itself
  */
  function retrieveBookLine($line){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT book_line FROM book WHERE line = ?");
      $query->bindValue(1, $line);
      $query->execute();
      $result = $query->fetchAll();
      return $result[0][0];
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Retrieve a full user record
  *
  * @param userid - a username
  * @return a Reader instance
  */
  function retrieveUserRecord($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT * FROM user_reader WHERE userid = ?");
      $query->bindValue(1, $userid);
      $query->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Reader');
      $query->execute();
      $result = $query->fetch();
      return $result;
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Updates the user's current book line
  *
  * @param userid - a username
  */
  function updateUserBookLine($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $totalRecords = $this->getTotalRecordCount();
      $currentLine = $this->getUserCurrentLine($userid);
      $query = "";
      if($currentLine + 1 > $totalRecords){
        $query = $pdo->prepare("UPDATE user_reader SET last_line = 1 WHERE userid = ?");
      } else {
        $query = $pdo->prepare("UPDATE user_reader SET last_line = last_line + 1 WHERE userid = ?");
      }
      $query->bindValue(1, $userid);
      $query->execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Updates the user's wpm speed
  *
  * @param userid - username
  * @param speed - a wpm speed selection
  * @return boolean if record was updated
  */
  function updateUserSpeed($userid, $speed){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $query = $pdo->prepare("UPDATE user_reader SET speed = ? WHERE userid = ?");
      $query->bindValue(1, $speed);
      $query->bindValue(2, $userid);
      if($query->execute()){
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e){
      return false;
    } finally {
      unset($pdo);
    }
  }

  /**
  * Returns the total number of lines of the book
  *
  * @return total book lines
  */
  private function getTotalRecordCount(){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT * FROM book");
      $query->execute();
      $result = $query->fetchAll();
      return count($result);
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  /**
  * Returns the user's current book line
  *
  * @param userid - username
  * @return user's current book line
  */
  private function getUserCurrentLine($userid){
    try{
      $pdo = new PDO("pgsql:dbname=$this->dbname;host=$this->serverName;port=$this->port;sslmode=require",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo->prepare("SELECT last_line FROM user_reader WHERE userid = ?");
      $query->bindValue(1, $userid);
      $query->execute();
      $result = $query->fetchAll();
      return $result[0][0];
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }
}
?>
