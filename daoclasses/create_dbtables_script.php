<?php
  require 'daoclasses/DAOManager.php';
  $daoManager = new DAOManager();
  $daoManager->createTables();
  $daoManager->fillBookTable();
?>
