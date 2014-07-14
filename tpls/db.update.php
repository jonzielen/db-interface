<?php
  session_start();

  if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false) {
    header("Location: login.php", 302);
  } else {



      require('settings.php');
      $link = mysqli_connect($settings['host'], $settings['username'], $settings['password'], $settings['database']);

      // if error connecting
      if (mysqli_connect_errno()) {
          printf("Connect failed: %s\n", mysqli_connect_error());
          exit();
      };

      // get column info
      $pKey = mysqli_query($link, "SHOW COLUMNS FROM `".$settings['table']."`");

      while ($obj = mysqli_fetch_object($pKey)) {
        // get column name of primary key or first auto_increment
        if ($obj->Key == 'PRI') {
          $primaryKey = $obj->Field;
          break;
        } else {
          if ($obj->Extra == 'auto_increment') {
            $primaryKey = $obj->Field;
            break;
          }
        }
      }

      $setNewVals = '';
      foreach ($_POST as $key => $value) {
        if ($primaryKey == $key) {
          $primaryKeyValue = $value;
        }
        $setNewVals .= $key.'="'.mysqli_real_escape_string($link, $value).'", ';
      }

      $setNewVals = rtrim($setNewVals, ", ");

      mysqli_query($link, "UPDATE ".$settings['table']." SET $setNewVals WHERE $primaryKey=$primaryKeyValue");
      mysqli_close($link);



  }
?>
