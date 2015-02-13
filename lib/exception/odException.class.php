<?php

class odException extends Exception
{
	// Redéfinissez l'exception ainsi le message n'est pas facultatif
	public function __construct($message, $code = 0, Exception $previous = null) {
		// assurez-vous que tout a été assigné proprement
		parent::__construct($message, $code, $previous);
	}

	// chaîne personnalisée représentant l'objet
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

?>