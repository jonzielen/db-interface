<?php
  class login {
    public $username;
    public $password;
    public $settings;
    public $usernamelogin;
    public $userpasswordlogin;
    public $isLoggedIn;

    function __construct($user, $settings) {
      $this->username = $user['name'];
      $this->password = $user['pass'];
      $this->usernamelogin = $settings['usernamelogin'];
      $this->userpasswordlogin = $settings['userpasswordlogin'];


      if (($this->username == $this->usernamelogin && $this->password == $this->userpasswordlogin) || (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)) {
        $this->isLoggedIn = true;
      } else {
        $this->isLoggedIn = false;
      }
    }
  }
