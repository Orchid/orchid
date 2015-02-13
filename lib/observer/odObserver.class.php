<?php

abstract class odObserver {
	private $__name;
	
	public function get_observer_name() {
		return $this->__name;
	}

   public function set_observer_name($name) {
      $this->__name = $name;
   }

	public function execute($parameters) {}
   	 
	abstract public function update(odObservable $observable);
}

?>