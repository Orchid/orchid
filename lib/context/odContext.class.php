<?php
class odContext {
	static protected $_instance = null;

	protected $_routing_rules = array(),
			    $_app_config = array(),
			    $_modules = array(),
			    $_request = array(),
			    $_current_controller = null,
			    $_views = array(),
			    $_cookie_hash = "",
			    $_dblink = null;

	private function __construct($application_path)	{
		require($application_path."/config/modules.cfg");
		$this->_modules = $modules;

		require($application_path."/config/routing_rules.cfg");
		$this->_routing_rules = $routing_rules;
		foreach($this->_routing_rules as $r) {
         ksort($r);
      }
      
		require($application_path."/config/app.cfg");
		$this->_app_config = $app_config;

		$this->_app_config["application_path"] = $application_path;
      
    // init de la connexion à la base
    $dblink = new odDatabase($app_config["database"]["server_name"], 
                             $app_config["database"]["database_name"],
                             $app_config["database"]["user_name"], 
                             $app_config["database"]["user_password"]);
    $this->_dblink = $dblink->connect();
    
    $this->_cookie_hash = md5($this->_app_config["application_name"]);
      
		self::$_instance = $this;
	}

	static public function initialize($application_path) {
		if (!isset(self::$_instance)) {
			self::$_instance = new odContext($application_path);
			return self::$_instance;
		}
	}

	static public function getInstance() {
		return self::$_instance;
	}

	public function find_rule_by_name($name) {
		$rules = $this->_get_routing_rules();
		if (array_key_exists($name, $rules)) {
			return new odRule($name, $rules[$name]);
		} else {
			// Error : no routing rule found
			throw new odRuleException('odContext find_rule_by_name : No routing rule found');
		}
	}
	 
	public function find_rule_by_module($parameters) {
		$rr = $this->get_routing_rules();
		foreach($rr as $rule_key => $rules_content) {
			if ($parameters === $rules_content["parameters"]) {
				$rule = new odRule($rule_key, $rr[$rule_key]);
				$rule_key_found = $rule_key;
				break;
			}
		}
		if ($rule) {
			return $rule;
		} else {
			// Error : no routing rule found
			throw new odRuleException('odContext find_rule_by_module : No routing rule found');
		}
	}
	 
	public function get_modules() {
		return $this->_modules;
	}
	 
	public function get_routing_rules() {
		return $this->_routing_rules;
	}

	public function get_app_config() {
		return $this->_app_config;
	}

	public function get_application_name() {
		return $this->_app_config["application_name"];
	}

	public function get_orchid_path() {
		return $this->_app_config["orchid_path"];
	}

	public function get_application_path() {
		return $this->_app_config["application_path"];
	}

	public function get_database_parameters() {
		return $this->_app_config["database"];
	}

	public function add_request($name, $request) {
		$this->_request[$name] = $request;
	}

	public function get_request_count() {
		return count($this->_request);
	}

	public function get_request($name) {
		return $this->_request[$name];
	}

	public function set_current_controller($controller) {
		$this->_current_controller = $controller;
	}
	 
	public function get_current_controller() {
		return $this->_current_controller;
	}
	
	public function add_view($name, $view) {
	   $this->_views[$name] = $view;
	}
	
	public function get_view($name) {
	   return $this->_views[$name];
	}
	
	public function get_views_count() {
	   return count($this->_views);
	}
	 
	public function get_cookie_hash() {
	   return $this->_cookie_hash;
	}
	
	/*
	 * retourne la connection à la base
	 */
	public function get_dblink() {
	   return $this->_dblink;
	}
	
	public function __clone() {
		throw new Exception('odContext __clone : No clone authorized for application context');
	}

}
?>