<?php

class odTransform {
	static public $tags = array(
			"otherloop_begin1" => array("/<oloop\s+(\w+?)\s+as\s+(.+?)>/", 
                                 "<?php \$status_\$1['index']=-1; \$status_\$1['count']=count($\$2); foreach($\$2 as \$status_\$1['key']=&gt;$\$1) { \$status_\$1['index']++; ?&gt;"),
			"loop_begin1"     => array("/{oloop\s+(\w+?)\s+as\s+(.+?)}/", 
                                 "<?php \$status_\$1['index']=-1; \$status_\$1['count']=count($\$2); foreach($\$2 as \$status_\$1['key']=\>$\$1) { \$status_\$1['index']++; ?&gt;"),
			"otherloop_begin2" => array("/<ofor\s+(\w+?)\s+as\s+\[(.+?)\.\.(.+?)\]:(.+?)>/",
							                   "<?php if (\$3 > \$2) { \$__begin_\$1=\$2; \$__end_\$1=\$3; \$__sens_\$1=1; } else { \$__begin_\$1=\$3; \$__end_\$1=\$2; \$__sens_\$1=-1; } \$status_\$1['count']=\$__end_\$1-\$__begin_\$1; \$status_\$1['first']=\$__begin_\$1; \$status_\$1['last']=\$__end_\$1; for($\$1=\$__begin_\$1; $\$1<\$__end_\$1; $\$1=$\$1+\$4*\$__sens_\$1) { \$status_\$1['next']=$\$1 + 1; \$status_\$1['prev']=$\$1 - 1; \$status_\$1['index']=$\$1; ?&gt;"),
			"loop_begin2"     => array("/{ofor\s+(\w+?)\s+as\s+\[(.+?)\.\.(.+?)\]:(.+?)}/",
							                   "<?php if (\$3 > \$2) { \$__begin_\$1=\$2; \$__end_\$1=\$3; \$__sens_\$1=1; } else { \$__begin_\$1=\$3; \$__end_\$1=\$2; \$__sens_\$1=-1; } \$status_\$1['count']=\$__end_\$1-\$__begin_\$1; \$status_\$1['first']=\$__begin_\$1; \$status_\$1['last']=\$__end_\$1; for($\$1=\$__begin_\$1; $\$1<\$__end_\$1; $\$1=$\$1+\$4*\$__sens_\$1) { \$status_\$1['next']=$\$1 + 1; \$status_\$1['prev']=$\$1 - 1; \$status_\$1['index']=$\$1; ?&gt;"),
			"otherloop_end"   => array("/<\/(oloop|ofor)>/", "<?php } ?&gt;"),
			"loop_end"        => array("/{(end_oloop|end_ofor)}/", "<?php } ?&gt;"),
			"otherinclude"    => array("/<oinclude\s+(.+?)>/", "<?php include('\$1'); ?&gt;"),
			"include"         => array("/{oinclude\s+(.+?)}/", "<?php include('\$1'); ?&gt;"),
			"otherscript_begin" => array("/<oscript>/", "<?php "),
			"script_begin"    => array("/{oscript}/", "<?php "),
			"otherscript_end" => array("/<\/oscript>/", " ?&gt;"),
			"script_end"      => array("/{end_oscript}/", " ?&gt;"),
			"otherif_begin"   => array("/<oif\s+\((.+?)\)>/", "<?php if (\$1) { ?&gt;"),
			"if_begin"        => array("/{oif\s+\((.+?)\)}/", "<?php if (\$1) { ?&gt;"),
			"otherif_end"     => array("/<\/oif>/", "<?php } ?&gt;"),
			"if_end"          => array("/{end_oif}/", "<?php } ?&gt;"),
			"otherif_else"    => array("/<oelse>/", "<?php } else { ?&gt;"),
			"if_else"         => array("/{oelse}/", "<?php } else { ?&gt;"),
      "otherodtext1"    => array("/<otext\s+(.+?)\s+in\s+(\w+?)\s*>/", "<?php echo $\$2-&gt;t('\$1'); ?&gt;"),
      "odtext1"         => array("/{otext\s+(.+?)\s+in\s+(\w+?)\s*}/", "<?php echo $\$2-&gt;t('\$1'); ?&gt;"),
      "otherodtext2"    => array("/<otext_in_script\s+(.+?)\s+in\s+(\w+?)\s*>/", "$\$2-&gt;t('\$1')"), 
      "odtext2"         => array("/{otext_in_script\s+(.+?)\s+in\s+(\w+?)\s*}/", "$\$2-&gt;t('\$1')"), 
			"otherdump"       => array("/<odump\s+(.+?)\s*>/", "<?php print_r(\$1); ?&gt;"),
			"dump"            => array("/{odump\s+(.+?)\s*}/", "<?php print_r(\$1); ?&gt;"),
			"otherprint"      => array("/<oprint\s+(.+?)\s*>/", "<?php echo \$1; ?&gt;"),
			"print"           => array("/{oprint\s+(.+?)\s*}/", "<?php echo \$1; ?&gt;"),
			"othercomment_begin" => array("/<ocomment>/", "<!--"),
			"comment_begin"   => array("/{ocomment}/", "<!--"),
			"othercomment_end" => array("/<\/ocomment>/", "--&gt;"),
			"comment_end"     => array("/{end_ocomment}/", "--&gt;"),
			"block"           => array("/(<block>|<\/block>)/", ""),
			"otherset"        => array("/<oset\s+(.+?)\s+as\s+(.+)\s*>/", "<?php $\$1=\$2; ?&gt;"),
			"set"             => array("/{oset\s+(.+?)\s+as\s+(.+?)\s*}/", "<?php $\$1=\$2; ?&gt;"),
		  "otherslot"       => array("/<oslot\s+(.+?)\s*>/", "<?php \$temp='tpl:'.\$1; include_partial(\$temp); ?&gt;"),
		  "slot"            => array("/{oslot\s+(.+?)\s*}/", "<?php \$temp='tpl:'.\$1; include_partial(\$temp); ?&gt;"),
		  "otherwebslot"    => array("/<owslot\s+(.+?)\s*>/", "<?php include_partial('url:\$1'); ?&gt;"),
		  "webslot"         => array("/{owslot\s+(.+?)\s*}/", "<?php include_partial('url:\$1'); ?&gt;"),
      "otherodsetlang"  => array("/<osetbundle\s+(\w+?)\s+in\s+(.+?)\s*>/", "<?php $\$1 = new odLang('\$2'); ?&gt;"),
      "odsetlang"       => array("/{osetbundle\s+(\w+?)\s+in\s+(.+?)\s*}/", "<?php $\$1 = new odLang('\$2'); ?&gt;"),

      "otherodynamic"   => array("/<odynamic\s+with\s+(.+?)\s*>/", "<?php \$context = odContext::getInstance(); \$request = \$context-&gt;get_request('request.initial'); \$rule = \$request-&gt;get_rule(); $\$1 = trim(\$rule['module'].\$rule['action'], '/'); ?&gt;"),
      "odynamic"        => array("/{odynamic\s+with\s+(.+?)}/", "<?php \$context = odContext::getInstance(); \$request = \$context-&gt;get_request('request.initial'); \$rule = \$request-&gt;get_rule(); $\$1 = trim(\$rule['module'].\$rule['action'], '/'); ?&gt;"), 

      "otheromodule"    => array("/<omodule\s+(\w+?)\s+in\s+(.+?)\s+with\s+format\s+(.+?)\s*>/", "<?php odTransform::add_tag_entry('\$1', '/<\$1\$3>/', '\$2'); ?&gt;"),
      "omodule"         => array("/{omodule\s+(\w+?)\s+in\s+(.+?)\s+with\s+format\s+(.+?)\s*}/", "<?php odTransform::add_tag_entry('\$1', '/{\$1\$3}/', '\$2'); ?&gt;")
	);
	
	public function __construct() {}
	
	public function get_all_tags() {
      return self::$tags;
	}
	
	public function get_named_tag($tagname) {
		return self::$tags[$tagname];
	}
	
	static function add_tag_entry($tagname, $format, $contentfilename) {
	   $format = str_replace("word+", "(\w+?)", $format);
	   $format = str_replace("word*", "(\w*?)", $format);
	   $format = str_replace("string+", "(.+?)", $format);
	   $format = str_replace("string*", "(.*?)", $format);
	   $format = str_replace("space+", "\s+", $format);
	   $format = str_replace("space*", "\s*", $format);
	   $content = file_get_contents("templates/".$contentfilename.".tpl");
		 foreach(self::$tags as $odtag) {
			 $content = preg_replace($odtag[0], $odtag[1], $content);
		 }
	   self::$tags[$tagname] = array($format, $content);
   }
}
?>