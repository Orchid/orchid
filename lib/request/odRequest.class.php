<?php
/*

Décode une requete du type :

http://nom_de_domaine/controller/module/action
ou
http://nom_de_domaine/module/action

Dans le cas ou le controler n'est pas spécifié, c'est le controller par défaut qui est invoqué.

*/
class odRequest {
	protected $controller = null,
            $rule = null,
	          $referer = "";

	public function __construct()	{}


	/*
	 Decode all the url and retrieve the good routing rule
	*/
	public function decode_url($url) {
    $suppr_get_param = explode("?", $url[1]);
    $url[1] = $suppr_get_param[0];
    
    $rule = array("action" => $url[1], "module" => $url[0]);
    $rule_obj = new odRule("current", $rule);
    $rule_found = $rule_obj->rules_contains();
    
    if ($rule_found) {
      $rule_params = array();
      // look for post parameters
      if ($_POST) $rule_params = $_POST;
      // look for get parameters
      if ($_GET) $rule_params = array_merge($rule_params, $_GET);
    } else {
      // Error : no routing rule found
      throw new odRuleException('odRequest decode_url : No routing rule found');
    }
    
    $this->rule = $rule_found;
    return array($rule_found, $rule_params);
	}

	/*
	 Find controller in url
	*/
	public function find_controller($url) {
    // get the application config
    $appcontext = odContext::getInstance()->get_app_config();
    
    $controller = null;
    $controller_found = false;
    foreach($appcontext["controllers"] as $app_key => $app_val) {
      if ($url[0] == $app_key) {
        // we have found a known controller
        $controller = $app_val;
        $controller_found = true;
        break;
      }
    }
    if (!$controller_found) {
      // we don't found any controller so we take the default controller
      $controller = $appcontext["controllers"]["default"];
    }
    $this->controller = $controller;
    return $controller_found;
	}


	public function get_controller() {
		return $this->controller;
	}

	public function get_referer() {
		return $this->referer;
	}

	public function get_rule() {
		return $this->rule;
	}

	public function set_referer($referer) {
		$this->referer = $referer;
	}
}

?>