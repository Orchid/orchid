<?php
abstract class odModel
{
	protected $model_name = "";

	public function __construct($model_name) {
		$this->model_name = $model_name;
	}
	 
	public function get_model_name() {
		return $this->model_name;
	}
	 
	public function load($module, $method, $args) {
     $app_name = odContext::getInstance()->get_application_name();
	  $orchid_path = odContext::getInstance()->get_orchid_path();
     if (isset($_POST["form"])) {
       if (method_exists($this, "perform")) {
         ${$this->get_model_name()} = $this->perform($args);
       } else {
         throw new odException("You have to define the method perform in ".$method." model class");
       }
     } else {
   	 ${$this->get_model_name()} = $this->action($args);
     }

	  return ${$this->get_model_name()};
	}
	
	public function getifPostParameter($parameterName, $value) {
    if (isset($_POST[$parameterName]) && $_POST[$parameterName] == $value) {
      return $_POST[$parameterName];
    } else {
      return false;
    }
  }
   
  public function getifGetPost($parameterName) {
    if (isset($_POST[$parameterName])) {
      return $_POST[$parameterName];
    } else {
      if (isset($_GET[$parameterName])) {
        return $_GET[$parameterName];
      } else {
        return false;
      }
    }
  }
   
  abstract public function action($param);
  abstract public function perform($param);
}
?>