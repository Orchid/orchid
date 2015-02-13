<?php
class odTemplate {
	public $file_path = null,   // string représentant le chemin vers le fichier template
		     $file_content = "",  // string qui recevra le contenu du fichier template après lecture
	       $datas = null,       // tableau contenant les données de la page
	       $args = null;        // tableau contenant les arguments passés à la page
	 
	public function __construct($file_path, $datas = null, $args = null) {
		$this->file_path = $file_path;
		$this->datas = $datas;
		$this->args = $args;
	}
	 
	 
	public function read_template() {
    // Lecture du template
    $xml = file_get_contents($this->file_path);
    
    // suppression des tags {start_block} et {end_block}
    $xml_formated = str_replace('{start_block}', '<!-- Orchid block begin -->', utf8_decode(html_entity_decode($xml)));
    $xml_formated = str_replace('{end_block}', '<!-- Orchid block end -->', $xml_formated);
    
    $this->file_content .= $xml_formated;
  }
      	
	public function read_module() {
    // Si le fichier module.tpl existe...
    if (file_exists("templates/modules.tpl")) {
      // Lecture du fichier module.tpl
      $xml = file_get_contents("templates/modules.tpl");
      
      // suppression des tags {start_block} et {end_block}
      $xml_formated = str_replace('{start_block}', '', utf8_decode(html_entity_decode($xml)));
      $xml_formated = str_replace('{end_block}', '', $xml_formated);
      
      $this->file_content .= $xml_formated;
    }
  }
   
	public function parse_template() {
  
    // On passe au tpl de la page les 2 variables nécessaires à son bon fonctionnement
		$args = $this->args;
		$datas = $this->datas;

    // On instancie la table des tags par défaut
		$transformateur = new odTransform();

    // On lit le contenu de module.tpl
    $this->read_module();

    // S'il y a quelque chose dans module.tpl, il ne doit y avoir que des tag omodule
 		if (strlen($this->file_content) != 0) {
      // On va chercher le tag omodule
      $module_tag = $transformateur->get_named_tag("otheromodule");
      
      // On le transforme par l'instruction d'ajout dans la table des tags de odTransform
			$this->file_content = preg_replace($module_tag[0], html_entity_decode($module_tag[1]), $this->file_content);

      // même chose pour le tag block
      $module_tag = $transformateur->get_named_tag("block");
			$this->file_content = preg_replace($module_tag[0], html_entity_decode($module_tag[1]), $this->file_content);
      
      // On supprime cette variable pour ne pas la retrouver dans les variables définies
      unset($module_tag);
      
      // Et on exécute l'instruction d'ajout dans la table des tags
      eval(stripSlashes("?\>".utf8_encode($this->file_content)));
    }
    
    // On lit le template de la page
		$this->read_template();
		
    // S'il y a quelque chose dans le template
		if (strlen($this->file_content) != 0) {
      // On transforme tous les tags trouvés par leur valeur définie dans la table des tags
			foreach($transformateur->get_all_tags() as $odtag) {
				$this->file_content = preg_replace($odtag[0], html_entity_decode($odtag[1]), $this->file_content);
			}
      
      // On supprime les variables que l'on ne doit pas voir
			unset($odtag);
			unset($transformateur);
      
			//echo $this->file_content;
			
      // On évalue le code obtenu de la page
      eval(stripSlashes("?\>".utf8_encode($this->file_content)));
		}
	}
}
?>