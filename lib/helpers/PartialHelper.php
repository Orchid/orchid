<?php
/*
 include_partial
*/
function include_partial($url) {
  $url_exp = explode(":", $url);
  
  $context = odContext::getInstance();
  
  if (strtolower($url_exp[0]) == "tpl") {
    $view = new odView($context->get_view("view.0")->get_datas(), 
                       $context->get_view("view.0")->get_args());
    $view->set_template_name("templates/".$url_exp[1].".tpl");
    $view->show();
  }
  if (strtolower($url_exp[0]) == "url") {
   	$url = explode('/', $url_exp[1]);
    if ($url[0] == "") {
      $url[0] = "/";
    } else {
      $url[1] = $url[0];
      $url[0] = "/";
    }
    
   	$request_initial = $context->get_request("request.initial");
   	$referer = $request_initial->get_referer();
   	$request_count = $context->get_request_count();
   	$request_name = "partial.request.".$request_count;
   
    // lancement de forward du controller
   	$controller = $context->get_current_controller();
    $controller->forward($url, $request_name, $referer);
  }
}

?>