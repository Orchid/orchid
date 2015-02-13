<?php
class odDatabase {
  protected
  $server_name = "",
  $user_name = "",
  $user_password = "",
  $database_name = "",
  $table_prefix = "";
	 
	public function __construct($server_name, $database_name, $user_name, $user_password) {
		$this->server_name = $server_name;
		$this->user_name = $user_name;
		$this->user_password = $user_password;
		$this->database_name = $database_name;
	}
	 
	public function setPrefixTable($prefix) {
		$this->table_prefix = $prefix;
	}
	 
	public function getPrefixTable() {
		return $this->table_prefix;
	}
	 
	public function connect() {
		$link = mysqli_connect($this->server_name, $this->user_name, $this->user_password, $this->database_name);
		if (!$link) {
			// erreur
			throw new odException("odDatabase : connect() : connection not possible");
		}
    return $link;
	}
}
?>