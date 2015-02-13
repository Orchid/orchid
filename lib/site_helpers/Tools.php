<?php

class odTools {
   public function __construct() {}
   
   /*
    * Transforme un tableau en une chaine de caractères représentant la création du tableau
    */
   public function serialize_array($name, $arr) {
      $tmp = json_encode($arr);
      $tmp = str_replace(":{", " => [", $tmp);
      $tmp = str_replace("{", "[", $tmp);
      $tmp = str_replace("},", "],\n\t", $tmp);
      $tmp = str_replace("}", "]", $tmp);
      $tmp = str_replace(":", " => ", $tmp);
      $tmp = str_replace('"', "'", $tmp);
      $tmp = "\$".$name." = ".stripSlashes($tmp).";";
      return $tmp;
   }

   /*
    * Supprime un répertoire récursivement
    */ 
   public function recursive_delete($dir) {
      $files = array_diff(scandir($dir), array('.','..'));
      foreach ($files as $file) {
        (is_dir("$dir/$file")) ? $this->recursive_delete("$dir/$file") : unlink("$dir/$file");
      }
      return rmdir($dir);
   }

   /*
    * Retourne les fichiers contenus dans un répertoire
    */ 
   public function get_files($dir) {
      $files = array();
      if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
           while ($file = readdir($dh)) {
              if ($file != "." && $file != ".." && !is_dir($dir.$file)) {
                 $files[] = $file;
              } 
           }
           closedir($dh);
        }
      }
      return $files;
   }

   /*
    * Obtenir le path du projet repéré par son id
    */    
   public function get_app_path_from_project_id($project_id) {
      $request = odDatabaseRequest::getInstance();
      $request->makeSelectRequest(['*'], ['orchid_bo_projects'], [['id', 'E', $project_id]]);
      $result = $request->runQuery();
      $row = $request->nextRow($result);
      return $row["project_path"];
   }

   /*
    * Insère un caractère du nombre de fois souhaité
    */    
   public function insert_chr($chrcode, $count = 1) {
      // $chrcode = 9 --> caractère tab
      // $chrcode = 13 --> caractère retour à la ligne
      $return_txt = "";
      for($i = 0; $i < $count; $i++) {
        $return_txt .= chr($chrcode);
      }
      return $return_txt;
   }

   /*
    * Méthodes de confort
    */    
   public function nl() { return insert_chr(13); }
   public function nl2() { return insert_chr(13, 2); }
  
   public function tab() { return insert_chr(9); }
   public function tab2() { return insert_chr(9, 2); }
   public function tab3() { return insert_chr(9, 3); }
   public function tab4() { return insert_chr(9, 4); }

}



/*
 * Transforme un tableau en une chaine de caractères représentant la création du tableau
 */
function serialize_array($name, $arr) {
  $tmp = json_encode($arr);
  $tmp = str_replace(":{", " => [", $tmp);
  $tmp = str_replace("{", "[", $tmp);
  $tmp = str_replace("},", "],\n\t", $tmp);
  $tmp = str_replace("}", "]", $tmp);
  $tmp = str_replace(":", " => ", $tmp);
  $tmp = str_replace('"', "'", $tmp);
  $tmp = "\$".$name." = ".stripSlashes($tmp).";";
  return $tmp;
}

/*
 * Supprime un répertoire récursivement
 */ 
function recursive_delete($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? recursive_delete("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

/*
 * Retourne les fichiers contenus dans un répertoire
 */ 
function get_files($dir) {
   $files = array();
   if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
         while ($file = readdir($dh)) {
            if ($file != "." && $file != ".." && !is_dir($dir.$file)) {
               $files[] = $file;
            } 
         }
         closedir($dh);
      }
   }
   return $files;
}

function get_app_path_from_project_id($project_id) {
   $request = odDatabaseRequest::getInstance();
   $request->makeSelectRequest(['*'], ['orchid_bo_projects'], [['id', 'E', $project_id]]);
   $result = $request->runQuery();
   $row = $request->nextRow($result);
   return $row["project_path"];
}

   
function insert_chr($chrcode, $count = 1) {
   // $chrcode = 9 --> caractère tab
   // $chrcode = 13 --> caractère retour à la ligne
   $return_txt = "";
   for($i = 0; $i < $count; $i++) {
      $return_txt .= chr($chrcode);
   }
   return $return_txt;
}

function nl() { return insert_chr(13); }
function nl2() { return insert_chr(13, 2); }

function tab() { return insert_chr(9); }
function tab2() { return insert_chr(9, 2); }
function tab3() { return insert_chr(9, 3); }
function tab4() { return insert_chr(9, 4); }

?>