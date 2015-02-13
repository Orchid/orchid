<?php

class odUrl {
   public function __construct() {}
   
   /*
    * redirect helper
    */
   public function redirect($route) {
      header("Location:".$route);
   }
  
   /*
    * redirect helper
    */
   public function staticRedirect($route) {
      $request_referer = odContext::getInstance()->get_request("request.initial")->get_referer();
      header("Location:".$request_referer."/".$route);
   }
}


/*
 * redirect helper
 */
function redirect($route) {
   header("Location:".$route);
}

/*
 * redirect helper
 */
function staticRedirect($route) {
   $request_referer = odContext::getInstance()->get_request("request.initial")->get_referer();
   header("Location:".$request_referer."/".$route);
}

?>