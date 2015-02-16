<?php

/*
 * Abstract class for all object mapping
 */

abstract class odObject implements arrayaccess {
    private $__data = [];
    
    // constructeur
    public function __construct() {}

    abstract public function create();
    abstract public function save();
    abstract public function delete();
    abstract public function count();

    public function &__get($key) {
      return $this->__data[$key];
    }

    public function __set($key,$value) {
      $this->__data[$key] = $value;
    }

    public function __isset($key) {
      return isset($this->__data[$key]);
    }

    public function __unset($key) {
      unset($this->__data[$key]);
    }

    public function offsetSet($offset,$value) {
      if (is_null($offset)) $this->__data[] = $value;
      else $this->__data[$offset] = $value;
    }

    public function offsetExists($offset) {
      return isset($this->__data[$offset]);
    }

    public function offsetUnset($offset) {
      if ($this->offsetExists($offset)) unset($this->__data[$offset]);
    }

    public function offsetGet($offset) {
      if ($offset == null) {
        return $this->__data;
      } else {
        return $this->offsetExists($offset) ? $this->__data[$offset] : null;
      }
    }
}

?>
