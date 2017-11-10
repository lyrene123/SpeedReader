<?php
class DAOManager
{

  private $serverName;
  private $user;
  private $password;
  private $dbname;

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
  }

  function createTables(){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $query = 'DROP TABLE IF EXISTS user_reader;
      DROP TABLE IF EXISTS book;
      CREATE TABLE book(
        line serial UNIQUE,
        book_line varchar(255)
      );
      CREATE TABLE user_reader (
        userid varchar(60) NOT NULL PRIMARY KEY,
        password varchar(255) NOT NULL,
        loginattempts integer,
        last_line integer references book(line),
        speed integer
      );';
      $pdo->exec($query);
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  function createBlockedUsersTable(){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $query = 'DROP TABLE IF EXISTS blockeduser_reader;
      CREATE TABLE blockeduser_reader(
        userid varchar(60) NOT NULL PRIMARY KEY,
        timeblocked timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
      );';
      $pdo->exec($query);
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

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
        $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $query = $pdo -> prepare('INSERT INTO book (book_line) VALUES (?)');
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

  function isUserExists($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT * FROM user_reader WHERE userid = ?");
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

  function addUser($userid, $pwd){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("INSERT INTO user_reader(userid, password,
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

  function getLoginAttempts($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT loginattempts FROM user_reader WHERE userid = ?");
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

  function getHash($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT password FROM user_reader WHERE userid = ?");
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

  function incrementLoginAttempts($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("UPDATE user_reader SET loginattempts = loginattempts + 1 WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  function resetLoginAttempts($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("UPDATE user_reader SET loginattempts = 0 WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  function addBlockedUser($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("INSERT INTO blockeduser_reader(userid,timeblocked)
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

  function isBlockedUserExists($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT * FROM blockeduser_reader WHERE userid = ?");
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

  function isUserStillBlocked($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT * FROM blockeduser_reader WHERE userid = ?
      AND timeblocked > (now() - interval '5 minute')");
      $query->bindValue(1,$userid);
      $query->execute();
      $result = $query->fetchAll();
      if(count($result) > 0){
        return true; //still blocked
      } else {
        return false;
      }
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  function unblockUser($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("DELETE FROM blockeduser_reader WHERE userid = ?");
      $query->bindValue(1,$userid);
      $query -> execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  function retrieveBookLine($line){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT book_line FROM book WHERE line = ?");
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

  function retrieveUserRecord($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT * FROM user_reader WHERE userid = ?");
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

  function updateUserBookLine($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $totalRecords = $this->getTotalRecordCount();
      $currentLine = $this->getUserCurrentLine($userid);
      $query = "";
      if($currentLine + 1 > $totalRecords){
        $query = $pdo -> prepare("UPDATE user_reader SET last_line = 1 WHERE userid = ?");
      } else {
        $query = $pdo -> prepare("UPDATE user_reader SET last_line = last_line + 1 WHERE userid = ?");
      }
      $query->bindValue(1, $userid);
      $query->execute();
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  private function getTotalRecordCount(){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT * FROM book");
      $query->execute();
      $result = $query->fetchAll();
      return count($result);
    } catch (PDOException $e){
      echo $e->getMessage();
    } finally {
      unset($pdo);
    }
  }

  private function getUserCurrentLine($userid){
    try{
      $pdo=new PDO("pgsql:dbname=$this->dbname;host=$this->serverName",$this->user,$this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $query = $pdo -> prepare("SELECT last_line FROM user_reader WHERE userid = ?");
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
