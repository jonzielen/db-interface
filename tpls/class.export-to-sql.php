<?php
  class exportToSQL {
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
      $this::buildSQL($this->_link, $this->data);
    }

    private function buildSQL($_link, $data) {
      $this->result = mysqli_query($this->_link, "SELECT * FROM ".$this->data['table']);
      $dbInfo = mysqli_query($this->_link, "SHOW COLUMNS FROM `".$this->data['table']."`");

      $output = "--\n-- Table structure for table `".$this->data['table']."`\n--\n\n";
      $output .= "CREATE TABLE IF NOT EXISTS `".$this->data['table']."` (\n";

      $priUni = '';
      while ($obj = mysqli_fetch_object($dbInfo)) {
        $output .=  "\t`".$obj->Field."` ".$obj->Type;
        $output .= (($obj->Null == 'NO') ? ' NOT NULL' : ' NULL');
        $output .= (($obj->Default != '') ? ' '.$obj->Default : '');
        $output .= (($obj->Extra != '') ? ' '.strtoupper($obj->Extra) : '').", \n";

        if ($obj->Key == 'PRI') {
          $priUni .= "\tPRIMARY KEY (`".$obj->Field."`), \n";
        }

        if ($obj->Extra == 'auto_increment') {
          $priUni .= "\tUNIQUE KEY `".$obj->Field."` (`".$obj->Field."`), \n";
        }
      }

      $output .= rtrim($priUni, ", \n");

      $dbStatus = mysqli_query($this->_link, "SHOW TABLE STATUS");
      while ($obj = mysqli_fetch_object($dbStatus)) {
        $output .=  "\n) ENGINE=".$obj->Engine." DEFAULT CHARSET=".$obj->Collation;
        $output .=  ' AUTO_INCREMENT='.$obj->Auto_increment;
      }

      $output .= ";\n\n--\n-- Dumping data for table `".$this->data['table']."`\n--\n\n";

      $i = 0;
      while($row = mysqli_fetch_array($this->result, MYSQLI_ASSOC)) {
        if ($i == 0) {
          $output .= 'INSERT INTO `'.$this->data['table'].'` (';

          $dbCols = '';
          foreach ($row as $key => $value) {
            $dbCols .= '`'.$key.'`, ';
          }
          $output .= rtrim($dbCols, ", ");
          $output .= ") VALUES\n";
        }
        $i++;
        $output .= '(';
        $dbVals = '';
        foreach ($row as $key => $value) {
          $dbVals .= "'".$value."', ";
          //$dbVals .= "'".preg_replace("'", "''", $value)."', ";
          /*$regex = '/[\d\.?]+/';
          $numberCheck = preg_match($regex, $value);

          if ($numberCheck) {
            $dbVals .= $value.', ';
          } else {
            $dbVals .= "'".$value."', ";
          }*/
        }
        $output .= rtrim($dbVals, ", ");
        $output .= "),\n";
      };

      $filename = $this->data['table'].".sql";
      header('Content-type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.$filename);

      echo $output;

      $this::closeConnection($this->_link);
    }

    private function closeConnection($_link) {
      mysqli_close($this->_link);
    }
  }
