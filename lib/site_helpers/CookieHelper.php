<?php

class odCookies {
   public function __construct() {}

   /*
    * Calcule le cookie hash
    */
   public function get_cookie_hash() {
      return odContext::getInstance()->get_cookie_hash();
   }
  
   /*
    * Créer les cookies d'authentification
    */
   public function set_auth_cookies($username) {
      setcookie("orchid_cookie_test", "cookie test", time()+ 3600);
      if (!isset($_COOKIE["orchid_cookie_test"])) {
         $url = new odUrl();
         $url->redirect("login?error=cookie_activation");
      }
      setcookie("orchid_logged_in_".$this->get_cookie_hash(), $username, time()+ 3600);
   }
  
   /*
    * Supprime les cookies d'authentification
    */
   public function clear_auth_cookies() {
  	   setcookie("orchid_cookie_test", "", time() - 10);
  	   setcookie("orchid_logged_in_".$this->get_cookie_hash(), "", time() - 10);
   }

   /*
    * Vérifie l'authentification
    */
   public function is_logged_in() {
      return isset($_COOKIE["orchid_logged_in_".$this->get_cookie_hash()]);
   }
}


class odSession {
  protected $_user_struct,
            $_cookie_params;
  public function __construct($user_struct) {
    $this->_user_struct = $user_struct;  
  }

  /*
   * Créer la session php
   */
  public function set_php_session() {
    // start the php session
    session_start();
    $_SESSION["username"] = $this->_user_struct["firstname"]." ".$this->_user_struct["lastname"];
    $_SESSION["userlog"] = $this->_user_struct["log"];
    $_SESSION["userid"] = $this->_user_struct["id"];
  }
  
  
  /*
   * Supprime la session php
   */
  public function clear_php_session() {
    session_start();
    if (ini_get("session.use_cookies")) {
      $this->_cookie_params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 10, $this->_cookie_params["path"], $this->_cookie_params["domain"], $this->_cookie_params["secure"], $this->_cookie_params["httponly"]);
    }
    session_destroy();
  }
   
  public function get_user_struct() {
    return $this->_user_struct;
  }
  
  public function get_cookie_params() {
    return $this->_cookie_params;
  }
}


/* ****************************************************************************
 * Functions pour garder la compatibilité avec les reste de l'application
 * ************************************************************************** */ 

/*
 * Calcule le cookie hash
 */
function get_cookie_hash() {
   return odContext::getInstance()->get_cookie_hash();
}

/*
 * Créer les cookies d'authentification
 */
function set_auth_cookies($username) {
   setcookie("orchid_cookie_test", "cookie test", time()+ 3600);
   if (!isset($_COOKIE["orchid_cookie_test"])) {
      redirect("login?error=cookie_activation");
   }
   setcookie("orchid_logged_in_".get_cookie_hash(), $username, time()+ 3600);
}

/*
 * Supprime les cookies d'authentification
 */
function clear_auth_cookies() {
	   setcookie("orchid_cookie_test", "", time() - 10);
	   setcookie("orchid_logged_in_".get_cookie_hash(), "", time() - 10);
}

/*
 * Créer la session php
 */
function set_php_session($user_struct) {
   // start the php session
   session_start();
   $_SESSION["username"] = $user_struct["firstname"]." ".$user_struct["lastname"];
   $_SESSION["userlog"] = $user_struct["log"];
   $_SESSION["userid"] = $user_struct["id"];
}


/*
 * Supprime la session php
 */
function clear_php_session() {
   session_start();
   if (ini_get("session.use_cookies")) {
       $params = session_get_cookie_params();
       setcookie(session_name(), '', time() - 10, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
   }
   session_destroy();
}

/*
 * Vérifie l'authentification
 */
function is_logged_in() {
   return isset($_COOKIE["orchid_logged_in_".get_cookie_hash()]);
}

?>