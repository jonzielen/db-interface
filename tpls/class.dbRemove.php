<?php
  class dbRemove {
    private $_link;
    private $pKey;
    private $data = array();
    private $primaryKey;
    private $postData = array();

    function __get($settings){
      if (array_key_exists($settings, $this->data)){
        return $this->data[$settings];
      }
    }

    function __set($settings, $value){
      $this->data[$settings] = $value;
    }

    function __construct($settings, $postData) {
      $this->data = $settings;
      $this->postData = $postData;
      $this::dbConnect($this->data['host'], $this->data['username'], $this->data['password'], $this->data['database'], $postData);
    }

    private function dbConnect($host, $username, $password, $database, $postData) {
      $this->_link = mysqli_connect($this->data['host'], $this->data['username'], $this->data['password'], $this->data['database']);
      // if error connecting
      if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
      }
      $this::findPrimaryKey($this->_link, $this->data, $postData);
    }

    private function findPrimaryKey($_link, $data, $postData) {
      // find primary key or first auto_increment
      $this->pKey = mysqli_query($this->_link, "SHOW COLUMNS FROM `".$this->data['table']."`");
      while ($obj = mysqli_fetch_object($this->pKey)) {
        // get column name of primary key or first auto_increment
        if ($obj->Key == 'PRI') {
          $this->primaryKey = $obj->Field;
          break;
        } else {
          if ($obj->Extra == 'auto_increment') {
            $this->primaryKey = $obj->Field;
            break;
          }
        }
      }
      $this::dbRemove($this->_link, $this->primaryKey, $this->data, $this->postData);
    }

    private function dbRemove($_link, $primaryKey, $data, $postData) {
      foreach ($this->postData as $key => $value) {
        if ($this->primaryKey == $key) {
          $primaryKeyValue = $value;
        }
      }

      mysqli_query($this->_link, "DELETE FROM ".$this->data['table']." WHERE $this->primaryKey=$primaryKeyValue");
      $this::closeConnection($this->_link);
    }

    private function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }
