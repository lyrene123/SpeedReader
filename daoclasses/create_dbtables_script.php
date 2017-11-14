<?php
  /**
  * The following console script application is used to create the database tables
  * needed for the Speed Reader wb application and fills up the book table with
  * all lines of a book
  */
  require 'daoclasses/DAOManager.php';
  $daoManager = new DAOManager();
  $daoManager->createTables();
  $daoManager->fillBookTable();
?>
