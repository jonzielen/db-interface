<?php
  class dbRead {
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
      $this::resultCount($this->_link);
      $this::findPrimaryKey($this->_link, $this->data);
      $this::dbRead($this->_link, $this->itemsPerPage, $this->data);
    }

    private function resultCount($_link) {
      $this->_resultCount = mysqli_query($this->_link, "SELECT * FROM ".$this->data['table']);
      $this::totalRows($this->_resultCount);
    }

    private function totalRows($_resultCount) {
      $this->_totalRows = mysqli_num_rows($this->_resultCount);
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
    }

    private function dbRead($_link, $itemsPerPage, $data) {
      if (isset($this->data['page'])) {
        $val1 = (($this->data['page']-1)*$this->itemsPerPage);
        $val2 = $val1+($itemsPerPage-1);

        $this->result = mysqli_query($this->_link,
          "SELECT * FROM ".$this->data['table']."
          ORDER BY ID
          LIMIT ".$this->itemsPerPage."
          OFFSET $val1");
      } else {
        $val1 = 0;
        $val2 = $val1+($this->itemsPerPage-1);

        $this->result = mysqli_query($this->_link,
          "SELECT * FROM ".$this->data['table']."
          ORDER BY ID
          LIMIT ".$this->itemsPerPage."
          OFFSET $val1");
      }
      $this::closeConnection($this->_link);
    }

    private function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }
