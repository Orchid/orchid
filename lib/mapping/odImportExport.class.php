<?php

class odImportExport {
  protected $_basename;
  
  public function __construct($basename) {
    $this->_basename = $basename;
  }
  
  /*****************************************************************************
   * import_database tool : importe la base
   ****************************************************************************/   
  public function import_file($filename, $filetype = "xml", $destdir = "") {
    // test sur validité du paramètre filename et type
    if ($filename == "" || $filename == null) {
		   throw new odException("Class : odImportExport - Method : import_file - Error : give a good filename to import");
    }
    if (!file_exists($filename)) {
		   throw new odException("Class : odImportExport - Method : import_file - Error : ".$filename." does'nt exists");
    }
    if ($filetype == null || $filetype == "") {
		   throw new odException("Class : odImportExport - Method : import_file - Error : give a good filename type to import");
    }

    // cas des fichiers xml
    switch($filetype) {
      case "xml" :
        $xml = simplexml_load_file($filename, null, LIBXML_NOCDATA);
        $json = json_encode($xml);
        $xml_tree = json_decode($json,TRUE);

        if (isset($xml_tree["drops"])) $drops = $xml_tree["drops"]["drop_request"];
        else $drops = array();
        if (isset($xml_tree["creates"])) $creates = $xml_tree["creates"]["create_request"];
        else $creates = array();
        if (isset($xml_tree["deletes"])) $deletes = $xml_tree["deletes"]["delete_request"];
        else $deletes = array();
        if (isset($xml_tree["inserts"])) $inserts = $xml_tree["inserts"]["insert_request"];
        else $inserts = array();
        
        $all_requests = array();
        $all_requests = array_merge($all_requests, $drops);
        $all_requests = array_merge($all_requests, $deletes);
        $all_requests = array_merge($all_requests, $creates);
        $all_requests = array_merge($all_requests, $inserts);
    
        // Instanciation de l'outil de fabrication des requêtes
        $request = odDatabaseRequest::getInstance();

        // Utilisation de la base concernée
        $request->makeRequest("USE ".$this->_basename);
        $result = $request->runQuery();
        
        foreach ($all_requests as $req) {
          $request->makeRequest($req);
        }
        break;
      case "zip" :
        odZip::unZip($filename, $destdir);
        break;
      default:
		    throw new odException("Class : odImportExport - Method : import_file - Error : bad type (".$type.") for filename : ".$filename);
        
    }
  }
  
  /*****************************************************************************
   * export_database_struct : exporte la structure de la base
   ****************************************************************************/   
  public function export_database_struct($filename, $tables, $option = "drops") {
  
    // Instanciation de l'outil de fabrication des requêtes
    $request = odDatabaseRequest::getInstance();
    
    $filename .= ".xml";
    
    // Utilisation de la base concernée
    $request->makeRequest("USE ".$this->_basename);
    $result = $request->runQuery();
    
    //get all of the tables
    if($tables == '*') {
    	$tables = array();
      $request->makeRequest("USE ".$this->_basename."; SHOW TABLES FROM ".$this->_basename);
      $result = $request->runQuery();
      while($row = $request->nextRow($result)) {
         array_push($tables, $row['Tables_in_'.$this->_basename]);
      }
    } else {
    	$tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    
    $file = new odFile($filename);
    $file->write("<?xml version='1.0' encoding='utf-8'?".chr(62));
    $file->write('<base_definition>');
    $file->write('<model>mysql</model>');
    if ($option == "drops") {
      $file->write('<drops>');
      foreach($tables as $table) {
        $file->write('<drop_request>DROP TABLE '.$table.';</drop_request>');
      }
      $file->write('</drops>');
    }
    $file->write('<creates>');
    foreach($tables as $table) {
      $file->write('<create_request>');
      $request->makeRequest("SHOW CREATE TABLE ".$table);
      $result = $request->runQuery();
      $row = $request->nextRow($result);
      $file->write($row['Create Table']);
      $file->write('</create_request>');
    }
    $file->write('</creates>');
    $file->write('</base_definition>');
    $file->close();
  }
  
  /*****************************************************************************
   * export_database_datas : exporte les données contenues dans la base
   ****************************************************************************/   
  public function export_database_datas($filename, $tables, $option = "drops") {
  
    // Instanciation de l'outil de fabrication des requêtes
    $request = odDatabaseRequest::getInstance();
    
    $filename .= ".xml";
    
    // Utilisation de la base concernée
    $request->makeRequest("USE ".$this->_basename);
    $result = $request->runQuery();
    
    //get all of the tables
    if($tables == '*') {
    	$tables = array();
      $request->makeRequest("USE ".$this->_basename."; SHOW TABLES FROM ".$this->_basename);
      $result = $request->runQuery();
      while($row = $request->nextRow($result)) {
         array_push($tables, $row['Tables_in_'.$this->_basename]);
      }
    } else {
    	$tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    
    $file = new odFile($filename);
    $file->write("<?xml version='1.0' encoding='utf-8'?".chr(62));
    $file->write('<base_definition>');
    $file->write('<model>mysql</model>');
    if ($option == "drops") {
      $file->write('<deletes>');
      foreach($tables as $table) {
        $file->write('<delete_request>DELETE FROM '.$table.';</delete_request>');
      }
      $file->write('</deletes>');
    }
    $file->write('<inserts>');
    foreach($tables as $table) {
      $request->select_all_on($table);
      $result = $request->runQuery();
      $field_count = $request->fieldCount();
      
      while($row = $request->nextRowArray($result)) {
        $file->write('<insert_request>');
        $file->write('<![CDATA[INSERT INTO '.$table.' VALUES(');
        for ($i = 0; $i < $field_count; $i++) {
        		$row[$i] = addslashes($row[$i]);
        		$row[$i] = preg_replace("/\n/", "\\n", $row[$i]);
        		if (isset($row[$i])) {
              $file->write('"'.utf8_encode($row[$i]).'"'); 
            } else { 
              $file->write('""'); 
            }
        		if ($i < ($field_count-1)) $file->write(',');
        }
        $file->write(');]]>'); 
        $file->write('</insert_request>');
      }

    }
    $file->write('</inserts>');
    $file->write('</base_definition>');
    $file->close();
  }
  
  public function import_static_images() {
  }
  
  /*****************************************************************************
   * export_static_images : exporte les images de votre site
   * Cette méthode créé une archive zip   
   ****************************************************************************/   
  public function export_static_images($dir, $filename) {
    $filename .= ".zip";
    odZip::zipDir($dir, $filename);
  }
  

}
?>