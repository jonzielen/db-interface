<?php
  session_start();

  if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false) {
    $urlExp = explode("/", $_SERVER['PHP_SELF']);
    $baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.$urlExp[1].'/login.php';
    header("Location: $baseUrl", 302);
  } else {
    require_once('settings.php');
    require_once('class.export-to-sql.php');
    $objexportToSQL = new exportToSQL($settings);
  }
?>
