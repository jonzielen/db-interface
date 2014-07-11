<?php
  class dbConnect {
    /*protected $_host;
    protected $_username;
    protected $_password;
    protected $_database;
    protected $_table;
    protected $_link;*/
    $_host;
    $_username;
    $_password;
    $_database;
    $_table;
    $_link;

    function __construct($host, $username, $password, $database, $table) {
      $this->_host = $host;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database = $database;
      $this->_table = $table;
      $this::dbConnect($this->_host, $this->_username, $this->_password, $this->_database);
    }

    function dbConnect($_host, $_username, $_password, $_database) {
      $this->_link = mysqli_connect($this->_host, $this->_username, $this->_password, $this->_database);
      // if error connecting
      if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
      }
    }

    function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }
?>
