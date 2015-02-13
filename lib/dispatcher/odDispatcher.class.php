<?php
class odDispatcher extends odObserver {
	// constructor
	public function __construct() {
	   $this->set_observer_name("dispatcher");
	}

	// dispatch request to the appropriate controller/method
	public function update(odObservable $observable) {
	   // explosion de l'url
		$uri = trim($_SERVER['REQUEST_URI'], '/');
		$url = explode('/', $uri);
    
		// construction du referer
		$referer = "http://".$_SERVER['HTTP_HOST'];
      
		$this->trigger_route($url, "request.initial", $referer);
	}
	 
	public function trigger_route($url, $request_name, $referer) {
		// construction de la requête
		$request = new odRequest();
		$request->set_referer($referer);
		
		//recherche du controller
		$controller_found = $request->find_controller($url);
		
		// mémorisation de la requête dans le contexte
		odContext::getInstance()->add_request($request_name, $request);

      // suppression du controller dans l'uri
		if ($controller_found) array_shift($url);
      
    if (count($url) == 1) {
       $url[1] = $url[0];
       $url[0] = "/";
    }
		$rule = $request->decode_url($url);
      
		// rule is ok, so we call the good action
		$action = new odAction($rule[0]);
		$action->activate($rule[1]);
	}
}
?>