<?php
  class dbConnect {
    protected $_host;
    protected $_username;
    protected $_password;
    protected $_database;
    protected $_table;
    protected $_link;

    function __construct($settings) {
      $this->_host = $settings['host'];
      $this->_username = $settings['username'];
      $this->_password = $settings['password'];
      $this->_database = $settings['database'];
      $this->_table = $settings['table'];
      $this::dbConnect($this->_host, $this->_username, $this->_password, $this->_database);
    }

    protected function dbConnect($_host, $_username, $_password, $_database) {
      $this->_link = mysqli_connect($this->_host, $this->_username, $this->_password, $this->_database);
      // if error connecting
      if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
      }
    }

    protected function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }

