<?php
  session_start();
  require_once('tpls/settings.php');

  if (!empty($_POST)) {
    $user['name'] = $_POST['username'];
    $user['pass'] = $_POST['password'];
    unset($_POST);
  } else {
    $user['name'] = '';
    $user['pass'] = '';
  }

  require_once('tpls/login.php');
  $loggedIn = new login($user, $settings);

  if ($loggedIn->isLoggedIn) {
    $_SESSION['loggedin'] = true;
    $filenamelist = explode('/', $_SERVER['SCRIPT_FILENAME']);
    $filename = end($filenamelist);
    $string = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
    $pattern = '/'.$filename.'/i';
    $homepage = preg_replace($pattern, '', $string);
    header("Location: $homepage", 302);
    exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
<?php if (!$loggedIn->isLoggedIn): ?>
  <form action="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" method="post">
    <label for="username">Username:</label><br />
    <input type="text" name="username" id="username" /><br />
    <label for="password">Password:</label><br />
    <input type="password" name="password" id="password" /><br />
    <button>Go!</button>
  </form>
<?php endif; ?>
</body>
</html>
