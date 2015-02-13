<?php

class odException extends Exception
{
	// Red�finissez l'exception ainsi le message n'est pas facultatif
	public function __construct($message, $code = 0, Exception $previous = null) {
		// assurez-vous que tout a �t� assign� proprement
		parent::__construct($message, $code, $previous);
	}

	// cha�ne personnalis�e repr�sentant l'objet
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>