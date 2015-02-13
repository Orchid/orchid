<?php

class odLogger {
  private static $_logger_instance = NULL;
  
  private function __construct() {}
  
  public function __set($name, $value) {
    switch($name) {
      case 'logfile':
        if (file_exists($value) && !is_writeable($value)) {
          throw new Exception($value." is not a valid file path");
        }
        if (!file_exists($value)) {
          file_put_contents($value, "", FILE_APPEND);
        }
        $this->logfile = $value;
        break;
      
      case 'isEnabled':
        $this->isEnabled = $value;
        break;
      default:
        throw new Exception($name." cannot be set");
    }
  }

  public function error($message, $file = null, $line = null) {
    if ($this->isEnabled) {
      $message = date("d-m-Y H:i:s", time()) .' - [ERROR] - '.$message;
      $message .= is_null($file) ? '' : " in $file";
      $message .= is_null($line) ? '' : " on line $line";
      $message .= "\n";
      return file_put_contents($this->logfile, $message, FILE_APPEND);
    }
  }

  public function info($message) {
    if ($this->isEnabled) {
      $message = date("d-m-Y H:i:s", time()) .' - [INFO] - '.$message;
      $message .= "\n";
      return file_put_contents($this->logfile, $message, FILE_APPEND);
    }
  }

  public function debug($message) {
    if ($this->isEnabled) {
      $message = date("d-m-Y H:i:s", time()) .' - [DEBUG] - '.$message;
      $message .= "\n";
      return file_put_contents($this->logfile, $message, FILE_APPEND);
    }
  }

  public static function getInstance() {
    if (!self::$_logger_instance) {
      self::$_logger_instance = new odLogger();
    }
    return self::$_logger_instance;
  }
  
  private function __clone() {}
}

?>