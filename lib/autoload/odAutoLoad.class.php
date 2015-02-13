<?php

class odAutoload {
	static protected $instance = null;

	protected $baseDir = null,
	          $appDir = null,
             $namespaces = null;
	protected $classes = array(
  			"odObserver" => "observer/odObserver.class.php",
  			"odObservable" => "observer/odObservable.class.php",
  			"odException" => "exception/odException.class.php",
  			"odRuleException" => "exception/odRuleException.class.php",
  			"odClassException" => "exception/odClassException.class.php",
  			"odDatabase" => "database/odDatabase.class.php",
  			"odDatabaseRequest" => "database/odDatabaseRequest.class.php",
  			"odAction" => "action/odAction.class.php",
  			"odDispatcher" => "dispatcher/odDispatcher.class.php",
  			"odModel" => "model/odModel.class.php",
  			"odView" => "view/odView.class.php",
  			"odController" => "controller/odController.class.php",
  			"odContext" => "context/odContext.class.php",
  			"odRequest" => "request/odRequest.class.php",
  			"odRule" => "request/odRule.class.php",
  			"odTemplate" => "template/odTemplate.class.php",
  			"odTransform" => "template/odTransform.class.php",
  			"odOrm" => "mapping/odOrm.class.php",
        "odLogger" => "logs/odLogger.class.php",
        "odFile" => "site_helpers/odFile.class.php",
        "odZip" => "site_helpers/odZip.class.php",
        "odObject" => "site_helpers/odObject.class.php",
        "odImportExport" => "mapping/odImportExport.class.php");

	public function __construct($application_path) {
		$this->baseDir = realpath(dirname(__FILE__).'/..');
		$this->appDir = $application_path;

    require_once($this->baseDir."/helpers/PartialHelper.php");
    require_once($this->baseDir."/helpers/UrlHelper.php");
    require_once($this->baseDir."/helpers/AssetHelper.php");
    require_once($this->baseDir."/helpers/TagHelper.php");
    require_once($this->baseDir."/site_helpers/CookieHelper.php");
    require_once($this->baseDir."/site_helpers/UrlHelper.php");
    require_once($this->baseDir."/site_helpers/LangHelper.php");
    require_once($this->baseDir."/site_helpers/Tools.php");
		
		// préparation du chemin des classes du framework
		foreach($this->classes as $key => $entry)	{
			$this->classes[$key] = $this->baseDir."/".$entry;
		}
		
		// enregistrement des namespaces que l'on souhaite voir scrutés
		$this->namespaces = array();
		$this->__getModules($this->appDir."/access/", $this->namespaces);
		$this->__getModules($this->appDir."/web/", $this->namespaces);
		$this->__getModules($this->appDir."/mapping/", $this->namespaces);
		$this->__getModules($this->appDir."/lib/", $this->namespaces);
    $this->namespaces = array_unique($this->namespaces);
      
      // ajout des classes qui se trouvent dans les namespaces
		foreach($this->namespaces as $entryPath) {
			if ($handle = opendir($entryPath)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
					   $path_info = pathinfo($entry);
						if (isset($path_info['extension']) && $path_info['extension'] == "php") {
						   $this->setClassPath($path_info['filename'], $entryPath.$entry);
						}
					}
				}
				closedir($handle);
			}
		}
		spl_autoload_register(array($this, 'loadClass'));
	}

	public function loadClass($class) {
		// class already exists
		if (class_exists($class, false) || interface_exists($class, false)) {
			return true;
		}

		// we have a class path, let's include it
		if (isset($this->classes[$class])) {
			require_once $this->classes[$class];
			return true;
		}
		return false;
	}

	public function setClassPath($class, $path) {
		$this->classes[$class] = $path;
	}

	public function getClassPath($class) {
		if (!$this->classes[$class]) {
			return null;
		}
		return $this->classes[$class];
	}
	
	private function __getModules($directory, &$files = array(), $exempt = array('.','..')) {
      $handle = opendir($directory);
      while(false !== ($resource = readdir($handle))) {
         if (!in_array(strtolower($resource), $exempt)) {
            if (is_dir($directory.$resource)) {
               self::__getModules($directory.$resource.'/', $files, $exempt);
            } else {
               array_push($files, $directory);
            }
         }
      }
      closedir($handle);
      return $files;
   }
	 
}

?>