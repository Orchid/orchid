<?php

class odFile {
  protected $_filename,
            $_filedescriptor;
  
  public function __construct($filename) {
    $this->_filename = $filename;
    $this->_filedescriptor = fopen($filename, 'a');
    if (!$this->_filedescriptor) {
      throw new odException("odFile constructor : error during opening ".$this->_filename);
    }
  }
   
  /*
  * binary write file helper
  */
  public function write($str) {
    if ($this->_filedescriptor) {
      fwrite($this->_filedescriptor, $str);
    } else {
      throw new odException("odFile write : file descriptor of ".$this->_filename." doesnt exists");
    }
  }
   
  /*
  * binary read file helper
  */
  public function read() {
    if ($this->_filedescriptor) {
      return fread($this->_filedescriptor, filesize($this->_filename));
    } else {
      throw new odException("odFile read : file descriptor of ".$this->_filename." doesnt exists");
    }
  }
   
  /*
  * close file helper
  */
  public function close() {
    if ($this->_filedescriptor) {
      fclose($this->_filedescriptor);
    } else {
      throw new odException("odFile close : file descriptor of ".$this->_filename." doesnt exists");
    }
  }
   
  /*
  * file_exists file helper
  */
  public function file_exists() {
    return file_exists($this->_filename);
  }
   
  /*
  * file_in_array file helper
  */
  public function file_in_array() {
    $content = file($this->_filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$content) {
      throw new odException("odFile file_in_array : error during reading ".$this->_filename);
    }
    return $content;
  }
   
  /*
  * file_in_string file helper
  */
  public function file_in_string() {
    $content = file_get_contents($this->_filename);
    if (!$content) {
      throw new odException("odFile file_in_string : error during reading ".$this->_filename);
    }
    return $content;
  }
  
}
?>