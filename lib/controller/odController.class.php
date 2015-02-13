<?php
class odController extends odObservable {
	public function __construct() {}

	public function forward($url, $request_name, $referer) {
    $observers = $this->get_observers();
    foreach ($observers as $observer) {
      try {
        if ($observer->get_observer_name() == "dispatcher")
        	$observer->trigger_route($url, $request_name, $referer);
      } catch(Exception $e) {
        die($e->getMessage());
      }
    }
	}

}
?>