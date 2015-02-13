<?php

/*
 * Abstract class for all object mapping
 */

abstract class odOrm implements arrayaccess {
    private $__data = [];
    protected $_content = [];

    // si $ids n'est pas renseigné, tout le contenu de la table sera chargé
    abstract public function initialize($ids = null);
    
    // si $ids n'est pas renseigné, tout le contenu de la table sera chargé
    abstract public function find($ids = null);
    
    // sauvegarde : correspond à un insert
    abstract public function save();
    
    // sauvegarde : correspond à un update
    abstract public function update();
    
    // constructeur
    public function __construct($ids) {
      $this->initialize($ids);
    }
    
    public function get_content() {
      return $this->_content;
    }
    
    public function get_first() {
      return $this->_content[0];
    }
    
    // si $id n'est pas renseignée, cela correspond à vider la table
    abstract public function delete($id = null);

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
    
    public function getValuesOfDataInString() {
      $values = "";
      foreach($this->offsetGet(null) as $val) {
        if (is_numeric($val)) {
          $values .= $val.",";
        } else {
          $values .= "'".$val."',";
        }
      }
      $values = trim($values, ",");
      return $values;
    }
    
    public function getKeysOfDataInString() {
      $attrnames = array_keys($this->offsetGet(null));
      $names = join(",", $attrnames);
      return $names;
    }
    
    public function getAssociationsInArray() {
      $datas = $this->offsetGet(null);
      $assos = [];
      foreach($datas as $key => $value) {
        if ($key != "key" && $key != "op" && $key != "val") {
          $assos[] = array($key, $value);
        }
      }
      return $assos;
    }
}

?>
