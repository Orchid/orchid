<?php
class odObservable {
	private $__observers = array();

	public function __construct() {}
	
	public function attach(odObserver $observer) {
		$this->__observers[] = $observer;
		return $this;
	}

	public function detach(odObserver $observer) {
		if (is_int($key = array_search($observer, $this->__observers, true))) {
			unset($this->__observers[$key]);
		}
		return $this;
	}

	public function notify() {
		foreach ($this->__observers as $observer) {
			try{
				$observer->update($this);
			} catch (Exception $e){
				die($e->getMessage());
			}
		}
	}
	
	public function get_observers() {
	   return $this->__observers;
	}
	
}
?>