<?php
class odRule {
	protected $rule = array(),
            $_rules = array(),
            $_modules = array(),
	          $name = "";

	public function __construct($name, $rule)	{
		$this->rule = $rule;
		$this->name = $name;
    $this->_rules = odContext::getInstance()->get_routing_rules();
    $this->_modules = odContext::getInstance()->get_modules();
    foreach($this->_rules as $r_key => $r_val) {
      if (in_array($r_val["module"], array_keys($this->_modules))) {
        $this->_rules[$r_key]["module"] = $this->_modules[$this->_rules[$r_key]["module"]];
      }
    }
	}

	public function get_rule() {
		return $this->rule;
	}

	public function get_module() {
		return $this->rule["module"];
	}

	public function get_action() {
		return $this->rule["action"];
	}

	public function get_rule_name()	{
		return $this->name;
	}

	public function set_module($module)	{
		$this->rule["module"] = $module;
	}

	public function set_action($action)	{
		$this->rule["action"] = $action;
	}

	public function set_rule_name($name) {
		$this->name = $name;
	}

  public function rule_equals($rule) {
     if ($rule->get_action() == $this->rule["action"] && $rule->get_module() == $this->rule["module"] && $rule->get_rule_name() == $this->name) {
        return true;
     } else {
        return false;
     }
  }
  
  public function rule_soft_equals($rule) {
     if ($rule["action"] == $this->rule["action"] && $rule["module"] == $this->rule["module"]) {
        return true;
     } else {
        return false;
     }
  }
  
  public function rules_contains() {
     foreach ($this->_rules as $rule) {
        if ($this->rule_soft_equals($rule)) {
          return $rule;
        }
     }
     return false;
  }
}

?>