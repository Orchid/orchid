<?php
class odDatabaseRequest
{
	/*
	 constantes dans les requetes
	*/
	const insert_tag = "INSERT IGNORE INTO",
      	select_tag = "SELECT",
      	delete_tag = "DELETE",
      	update_tag = "UPDATE",
      	from_tag = "FROM",
      	where_tag = "WHERE",
      	orderby_tag = "ORDER BY",
      	asc_tag = "ASC",
      	desc_tag = "DESC",
      	limit_tag = "LIMIT",
      	values_tag = "VALUES",
      	set_tag = "SET",
      	now_tag = "NOW()",
      	and_tag = "AND",
      	or_tag = "OR",
        createdb_tag = "CREATE DATABASE",
        ifnotexists_tag = "IF NOT EXISTS",
        default_tag = "DEFAULT",
        character_set_tag = "CHARACTER SET",
        collate_tag = "COLLATE",
        createtab_tag = "CREATE TABLE",
        ai_tag = "AUTO_INCREMENT";

   protected $_op = array("E" => "=", "GT" => ">", "LT" => "<", "GTE" => ">=", "LTE" => "<=", "NE" => "!=", "LIKE" => "like", "IN" => "in");
   
	/*
	 attributs protégés
	*/
	static protected $instance = null,
	$request = "";

	/*
	 constructeur
	*/
	public function __construct() {
		self::$instance = $this;
	}
	 
	/*
	 getInstance() : construit une instance de cette classe
	*/
	static public function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new odDatabaseRequest();
		}

		return self::$instance;
	}

	/*
	 getRequest() : retourne la dernière requète construite
	*/
	public function getRequest() {
		return self::$request;
	}
	 
	/*
	 runQuery() : execute une requète et retourne le descripteur
	*/
	public function runQuery() {
		$result = mysqli_query(odContext::getInstance()->get_dblink(), self::$request);
		if (!$result) {
			// erreur
			throw new odException("runQuery : invalid request (".self::$request.")");
		}
		return $result;
	}
	 
	/*
	 newtRow() : donne la ligne de données suivante d'un descripteur de requète
	*/
	public function nextRow($result) {
		return mysqli_fetch_array($result, MYSQL_ASSOC);
	}
	 
	/*
	 newtRow() : donne la ligne de données suivante d'un descripteur de requète
	*/
	public function nextRowArray($result) {
		return mysqli_fetch_array($result, MYSQLI_NUM);
	}
  
  public function fieldCount() {
    return mysqli_field_count(odContext::getInstance()->get_dblink());
  }

  public function getInsertId() {
    return mysqli_insert_id(odContext::getInstance()->get_dblink());
  }
   /*
    getValues() : retourne un tableau avec les rows de la requete courante
   */
   public function getValues($result) {
     $result_tab = array();
     while($row = $this->nextRow($result)) {
        array_push($result_tab, $row);
     }
     return $result_tab;
   }
   
    	 
	/*
	 select_all_on() : permet de construire toutes les requètes du type : 'select * from table;'
	*/
	public function select_all_on($tables) {
		if (is_array($tables)) {
			$req = self::select_tag." * ".self::from_tag." ".implode(",", $tables);
		} else {
			$req = self::select_tag." * ".self::from_tag." ".$tables;
		}

		self::$request = $req;
	}
	 
	/*
	 makeSelectRequest() : construit une requète select

	SELECT $attributes FROM $tables WHERE $conditions $extras

	$attributes : tous les attributs de la requete
	$tables     : les tables sur lesquelles la requete va s'executer
	$conditions : les conditions de la clause where
	$extras     : order by et/ou limit (syntaxe : array("order", "attr", "asc"|"desc") ou array("limit", deb, fin)

	Si les paramètres de cette méthode ne sont pas des tableaux, une erreur est levée.
	*/
	public function makeSelectRequest($attributes, $tables, $conditions = array(), $extras = array()) {
		if (!is_array($attributes) or !is_array($tables) or !is_array($conditions) or !is_array($extras)) {
			// erreur
			throw new odException("Usage : makeSelectRequest(array attributes, array tables, [array conditions[, array extras]])");
		}

		$req = self::select_tag." ".implode(",", $attributes)." ".self::from_tag." ".implode(",", $tables);

		if ($conditions != null) {
			$req .= " ".self::where_tag." ";
			foreach($conditions as $condition) {
    		   $req .= $condition[0];
    			if (array_key_exists($condition[1], $this->_op)) {
    			   $req .= $this->_op[$condition[1]];
            } else {
               throw new odException("Usage : makeSelectRequest() : bad operator for where clause");
            }
    			if (is_numeric($condition[2]))	{
    			   $req .= $condition[2];
    			} else {
    			   $req .= "'".addSlashes($condition[2])."'";
    			}
    
    		   if (isset($condition[3])) {
    				  if (strtolower($condition[3]) == "and") $req .= " ".self::and_tag." ";
    				  if (strtolower($condition[3]) == "or") $req .= " ".self::or_tag." ";
    			}
			}
		}

		if ($extras != null) {
			foreach($extras as $extra) {
				if ($extra[0] == "order") {
					$req .= " ".self::orderby_tag." ".$extra[1];
					if (strtolower($extra[2]) == "asc") $req .= " ".self::asc_tag;
					if (strtolower($extra[2]) == "desc") $req .= " ".self::desc_tag;
				}
				if ($extra[0] == "limit") {
					$req .= " ".self::limit_tag." ".$extra[1].",".$extra[2];
				}
			}
		}

		self::$request = $req;
	}
	 
	/*
	 makeInsertRequest() : construit une requete insert

	INSERT INTO $table ($attributes) VALUES ($values)
	*/
	public function makeInsertRequest($table, $attributes, $values) {
		if (count($attributes) != count($values)) {
			// erreur
			throw new odException("Usage : makeInsertRequest() : attributes count must be equal with values count");
		}

		$req = self::insert_tag." ".$table." (".implode(",", $attributes).") ".self::values_tag." (";
		foreach ($values as $value) {
			if ($value == self::now_tag) {
				$req .= self::now_tag.",";
			}
			if (is_numeric($value)) {
				$req .= $value.",";
			} else {
        $value = str_replace("(string)", "", $value);
				$req .= "'".addSlashes(trim($value))."',";
			}
		}
		$req = substr($req, 0, strlen($req) - 1);
		$req .= ")";
		self::$request = $req;
	}
	 
	/*
	 makeUpdateRequest() : construit une requète update

	UPDATE $table SET $associations WHERE $conditions
	*/
	public function makeUpdateRequest($table, $associations, $conditions) {
		$req = self::update_tag." ".$table." ".self::set_tag." ";

		if ($associations == null)	{
			// erreur
			throw new odException("Usage : makeUpdateRequest() : associations are required");
		}
		if ($conditions == null) {
			// erreur
			throw new odException("Usage : makeUpdateRequest() : conditions are required");
		}

		foreach ($associations as $association) {
			$req .= $association[0]."=";
			if (strtolower($association[1]) == strtolower(self::now_tag)) {
				$req .= self::now_tag.",";
			} else {
  			 if (is_numeric($association[1])) {
  				  $req .= $association[1].",";
  			 } else {
  				  $req .= "'".addSlashes($association[1])."',";
  			 }
      }
		}
		$req = substr($req, 0, strlen($req) - 1);

		$req .= " ".self::where_tag." ";
		foreach($conditions as $condition) {
			 $req .= $condition[0];
			 if (array_key_exists($condition[1], $this->_op)) {
			    $req .= $this->_op[$condition[1]];
       } else {
          throw new odException("Usage : makeUpdateRequest() : bad operator for where clause");
       }
			 if (is_numeric($condition[2]))	{
				  $req .= $condition[2];
			 } else {
				  $req .= "'".addSlashes($condition[2]);
			 }

		   if (isset($condition[3])) {
				  if (strtolower($condition[3]) == "and") $req .= " ".self::and_tag." ";
				  if (strtolower($condition[3]) == "or") $req .= " ".self::or_tag." ";
			 }
		}
		self::$request = $req;
	}
	 
	/*
	 makeDeleteRequest() : construit une requete delete

	DELETE FROM $table WHERE $conditions
	*/
	public function makeDeleteRequest($table, $conditions = null) {
		$req = self::delete_tag." ".self::from_tag." ".$table;

		if ($conditions != null) {
			$req .= " ".self::where_tag." ";
			foreach($conditions as $condition) {
				$req .= $condition[0];
        if (array_key_exists($condition[1], $this->_op)) {
			     $req .= " ".$this->_op[$condition[1]];
        } else {
           throw new odException("Usage : makeDeleteRequest() : bad operator for where clause");
        }
        $req .= " ".$condition[2];
				if (isset($condition[3])) {
  				   if (strtolower($condition[3]) == "and") $req .= " ".self::and_tag." ";
  				   if (strtolower($condition[3]) == "or") $req .= " ".self::or_tag." ";
				}
			}
		}
		self::$request = $req;
	}

   /*
    * makeCreateDbRequest : création de base
    */    
   public function makeCreateDbRequest($dbname, $charset = "latin1", $collate = "latin1_swedish_ci") {
      $req = self::createdb_tag." ".self::ifnotexists_tag." ".trim($dbname)." ".self::default_tag." ".self::character_set_tag." ".$charset." ".self::collate_tag." ".$collate.";";
      self::$request = $req;
   }
   
   /*
    * makeCreateTableRequest : création de table
    */    
   public function makeCreateTableRequest($table, $table_def, $engine, $charset) {
      $req = self::createtab_tag." ".self::ifnotexists_tag." ".trim($table)." (";
      foreach($table_def["attribute"] as $attr_def) {
        $attr_name = $attr_def["name"];
        $attr_type = $attr_def["type"];
        $attr_prim = "no";
        if (isset($attr_def["primary"])) $attr_prim = $attr_def["primary"];  
        $attr_ai = 0;
        if (isset($attr_def["autoincrement"])) $attr_ai = $attr_def["autoincrement"];
        $req .= trim($attr_name)." ".$attr_type;
        if ($attr_ai != 0) {
          $req .= " ".self::ai_tag.",";
        }
        $req = trim($req, ",");
        if (strtolower(trim($attr_prim)) != "no") {
          $req .= " PRIMARY KEY,";
        }
      }
      $req .= ") ENGINE=".$engine." DEFAULT CHARSET=".$charset;
      if ($attr_ai != 0) {
        $req .= " AUTO_INCREMENT=".$attr_ai;
      }
      $req .= ";";
      self::$request = $req;
   }
   	
	/*
	 * Permet d'écrire soit même ses requêtes
	 */	 
	public function makeRequest($req) {
	   self::$request = $req;
   }
}
?>