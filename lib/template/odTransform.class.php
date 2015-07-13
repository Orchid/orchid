<?php

class odTransform {
	static public $tags = array(
		"loop_begin1"		=> array("/<oloop\s+(\w+?)\s+as\s+(.+?)>/", 
                                 	 "<?php \$status_\$1['index']=-1; \$status_\$1['count']=count($\$2); foreach($\$2 as \$status_\$1['key']=&gt;$\$1) { \$status_\$1['index']++; ?&gt;"),
		"loop_begin2" 		=> array("/<ofor\s+(\w+?)\s+as\s+\[(.+?)\.\.(.+?)\]:(.+?)>/",
							         "<?php if (\$3 > \$2) { \$__begin_\$1=\$2; \$__end_\$1=\$3; \$__sens_\$1=1; } else { \$__begin_\$1=\$3; \$__end_\$1=\$2; \$__sens_\$1=-1; } \$status_\$1['count']=\$__end_\$1-\$__begin_\$1; \$status_\$1['first']=\$__begin_\$1; \$status_\$1['last']=\$__end_\$1; for($\$1=\$__begin_\$1; $\$1<\$__end_\$1; $\$1=$\$1+\$4*\$__sens_\$1) { \$status_\$1['next']=$\$1 + 1; \$status_\$1['prev']=$\$1 - 1; \$status_\$1['index']=$\$1; ?&gt;"),
		"loop_end"			=> array("/<\/(oloop|ofor)>/", "<?php } ?&gt;"),
		"include"    		=> array("/<oinclude\s+(.+?)>/", "<?php include('\$1'); ?&gt;"),
		"script_begin"		=> array("/<oscript>/", "<?php "),
		"script_end" 		=> array("/<\/oscript>/", " ?&gt;"),
		"if_begin"   		=> array("/<oif\s+\((.+?)\)>/", "<?php if (\$1) { ?&gt;"),
		"if_end"     		=> array("/<\/oif>/", "<?php } ?&gt;"),
		"if_else"    		=> array("/<oelse>/", "<?php } else { ?&gt;"),
		"odtext2"    		=> array("/<otext_in_script\s+(.+?)\s+in\s+(\w+?)\s*>/", "$\$2-&gt;t('\$1')"), 
		"odtext1"    		=> array("/<otext\s+(.+?)\s+in\s+(\w+?)\s*>/", "<?php echo $\$2-&gt;t('\$1'); ?&gt;"),
		"dump"       		=> array("/<odump\s+(.+?)\s*>/", "<?php print_r(\$1); ?&gt;"),
		"print"      		=> array("/<oprint\s+(.+?)\s*>/", "<?php echo \$1; ?&gt;"),
		"comment_begin" 	=> array("/<ocomment>/U", "<!--"),
		"comment_end" 		=> array("/<\/ocomment>/", "--&gt;"),
		"block"				=> array("/(<block>|<\/block>)/", ""),
		"set"        		=> array("/<oset\s+(.+?)\s+as\s+(.+)\s*>/", "<?php $\$1=\$2; ?&gt;"),
		"slot"       		=> array("/<oslot\s+(.+?)\s*>/", "<?php \$temp='tpl:'.\$1; include_partial(\$temp); ?&gt;"),
		"webslot"    		=> array("/<owslot\s+(.+?)\s*>/", "<?php include_partial('url:\$1'); ?&gt;"),
		"odsetlang"  		=> array("/<osetbundle\s+(\w+?)\s+in\s+(.+?)\s*>/", "<?php $\$1 = new odLang('\$2'); ?&gt;"),
		"odynamic"   		=> array("/<odynamic\s+with\s+(.+?)\s*>/", "<?php \$context = odContext::getInstance(); \$request = \$context-&gt;get_request('request.initial'); \$rule = \$request-&gt;get_rule(); $\$1 = trim(\$rule['module'].\$rule['action'], '/'); ?&gt;"),
		"omodule"    		=> array("/<omodule\s+(\w+?)\s+in\s+(.+?)\s+with\s+format\s+(.+?)\s*>/", "<?php odTransform::add_tag_entry('\$1', '/<\$1\$3>/', '\$2'); ?&gt;")
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