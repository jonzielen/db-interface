<?php
  class dbRemove {
    private $_link;
    private $_resultCount;
    private $_totalRows;
    private $itemsPerPage = 30;
    private $pKey;
    private $data = array();
    public $primaryKey;
    public $result;

    function __get($settings){
      if (array_key_exists($settings, $this->data)){
        return $this->data[$settings];
      }
    }

    function __set($settings, $value){
      $this->data[$settings] = $value;
    }

    function __construct($settings) {
      $this->data = $settings;
      $this::dbConnect($this->data['host'], $this->data['username'], $this->data['password'], $this->data['database']);
    }

    private function dbConnect($host, $username, $password, $database) {
      $this->_link = mysqli_connect($this->data['host'], $this->data['username'], $this->data['password'], $this->data['database']);
      // if error connecting
      if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
      }
      $this::findPrimaryKey($this->_link, $this->data);
    }

    private function findPrimaryKey($_link, $data) {
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
      $this::dbRemove($this->_link, $this->primaryKey, $this->data);
    }

    private function dbRemove($_link, $primaryKey, $data) {
      $setNewVals = '';
      foreach ($_POST as $key => $value) {
        if ($this->primaryKey == $key) {
          $primaryKeyValue = $value;
        }
        $setNewVals .= $key.'="'.mysqli_real_escape_string($this->_link, $value).'", ';
      }

      $setNewVals = rtrim($setNewVals, ", ");

      mysqli_query($link, "DELETE ".$this->data['table']." WHERE $primaryKey=$primaryKeyValue");
      $this::closeConnection($this->_link);
    }

    private function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }
