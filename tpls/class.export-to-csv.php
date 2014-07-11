<?php
  class exportToCSV {
    private $_link;
    private $data = array();
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
      $this::buildCSV($this->_link, $this->itemsPerPage, $this->data);
    }

    private function buildCSV($_link, $itemsPerPage, $data) {
      $this->result = mysqli_query($this->_link, "SELECT * FROM ".$this->data['table']);
      $output = '';

      $i = 0;
      while($row = mysqli_fetch_array($this->result, MYSQLI_ASSOC)) {
        if ($i == 0) {
          foreach ($row as $key => $value) {
            $output .= $key.',';
          }
          $output .= "\n";
        }
        $i++;
        foreach ($row as $key => $value) {
          $output .= $value.',';
        }
        $output .= "\n";
      };

      $filename = $this->data['table'].".csv";
      header('Content-type: application/csv');
      header('Content-Disposition: attachment; filename='.$filename);

      echo $output;

      $this::closeConnection($this->_link);
    }

    private function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }
