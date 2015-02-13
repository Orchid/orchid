<?php

/*
 * Classe permettant l'internationalisation d'un site
 * Les messages doivent être contenus dans un fichier .prop sous la forme 
 * de paire nom=valeur unique (1 par ligne) 
 */
class odLang {
   protected $_langFileContent = null;
   
   public function __construct($filename) {
      if (isset($_SESSION["lang"])) {
         $lang = $_SESSION["lang"];
      } else {
         if (isset($_GET["lang"])) {
            $lang = $_GET["lang"];
         } else {
            if (isset($_POST["lang"])) {
               $lang = $_POST["lang"];
            } else {
               $lang = "fr_FR";
            }
         }
      }
      $context = odContext::getInstance();
      $app_path = $context->get_application_path();
      
      $nameoffile = $app_path."/ressources/".$filename."_".$lang.".prop";
      if (file_exists($nameoffile)) {
         $fileContent = file($nameoffile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
         foreach($fileContent as $line) {
            if (substr($line, 0, 2) == "##") continue;
            $lineexp = explode("=", $line);
            if (count($lineexp) < 2) {
               throw new Exception("odLang constructor : property '".$line."' doesnt complete");
            }
            $messtab = array_slice($lineexp, 1);
            $message = implode("=", $messtab);
            $this->_langFileContent[$lineexp[0]] = $message;
         }
         unset($fileContent);
      } else {
         throw new Exception("odLang constructor : file '".$nameoffile."' doesnt exists");
      }
   }
   
   public function t($message) {
      return call_user_func_array('sprintf', array_merge((array)$this->_langFileContent[$message], func_get_args())); 
   }
}
   
?>