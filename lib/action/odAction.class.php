<?php
class odAction {

	public $model = null,
        	 $view = null,
        	 $method = "",
        	 $module = "";
	 
	public function __construct($rule) {
		$this->method = $rule["action"];
		$this->module = $rule["module"];

		$modelclass = $this->module.$this->method."Model";
		$modelclass = trim($modelclass, "/");

		$this->model = new $modelclass($this->method);
	}
	 
	public function getModel() { return $this->model; }
	public function getView() { return $this->view;	}

	public function activate($args) {
	   $modelresult = $this->getModel()->load($this->module, $this->method, $args);
		$viewclass = $this->module.$this->method."View";
		$viewclass = trim($viewclass, "/");
		
		if (class_exists($viewclass) && !isset($_POST['form'])) {
   		$this->view = new $viewclass($modelresult, $args);
   		$context = odContext::getInstance();
   		$context->add_view("view.".$context->get_views_count(), $this->view);
   		$this->view->show();
		}
	}
} 
?>