<?php
  session_start();

  if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false) {
    header("Location: login.php", 302);
  }
  require_once('tpls/settings.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
<?php if ($_SESSION['loggedin'] == true): ?>
  <ul>
    <li>search db</li>
    <li>delete row</li>
    <li>edit row</li>
    <li>export database
      <ul>
        <li><a href="tpls/export-to-csv.php">CSV</a></li>
        <li><a href="tpls/export-to-sql.php">SQL</a></li>
      </ul>
    </li>
  </ul>

  <table>
  <?php
    require_once('tpls/class.dbRead.php');
    if (isset($_GET['page'])) {
      $settings['page'] = $_GET['page'];
    }
    $objDBRead = new dbRead($settings);

    for ($i=0; $i < $objDBRead->result->num_rows; $i++) {
      $row = mysqli_fetch_array($objDBRead->result, MYSQLI_ASSOC);
      if ($i == 0) {
        echo '<tr class="row head">';
        foreach ($row as $key => $value) {
          echo '<th>'.$key.'</th>';
        }
        echo '</tr>';
      }
      echo '<tr class="row'.(($i % 2 == 0) ? ' even' : ' odd').'">';
      foreach ($row as $key => $value) {
        if ($objDBRead->primaryKey == $key) {
          $primaryClass = 'class="primary"';
        } else {
          $primaryClass = '';
        }
        echo '<td '.$primaryClass.' data-column="'.$key.'">'.$value.'</td>';
      }
      echo '<td class="edit-row-'.($i+1).'">edit</td>';
      echo '<td class="delete-row-'.($i+1).'">delete</td>';
      echo '</tr>';
    }
  ?>
  </table>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
  <script src="js/main.js"></script>
<?php endif ?>
</body>
</html>
