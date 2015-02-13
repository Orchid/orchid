<?php
/*
 link_tag
*/
function link_tag($url,  $options = array(), $html = false) {
	if (substr($url, 0, 1) == "-") {
		$url_path = substr($url, 1);
	} else {
		$url_path = odContext::getInstance()->get_request("request.initial")->get_referer();
		$url_path .= "/css/".$url;
	}
	$options_tab = array("rel"   => isset($options["rel"]) ? $options["rel"] : "alternate",
			"type"  => isset($options["type"]) ? $options["type"] : "application/".$type."+xml",
			"href"  => $url_path);
	if (isset($option["media"])) {
		$options_tab = array_merge($options_tab, array("media" => $option["media"]));
	}
	
	if ($html) {
	   return html_simple_tag("link", $options_tab);
	} else { 
	   simple_tag("link", $options_tab);
	}
}

/*
 script_tag
*/
function include_script_tag() {
	$args = func_get_args();
	foreach($args as $arg) {
		if (substr($arg, 0, 1) == "-") {
			$url_path = substr($arg, 1);
		} else {
			$url_path = odContext::getInstance()->get_request("request.initial")->get_referer();
			$url_path .= "/js/".$arg;
		}
		$pinfo = pathinfo($arg);
		if (isset($pinfo["extension"]) && $pinfo["extension"] != "js") {
			$url_path .= ".js";
		}

		tag("script", "", array("type" => "text/javascript", "src" => $url_path));
		echo "\n";
	}
}

/*
 image_tag
 
<?php image_tag("338.jpg"); ?>
==> <img src='http://localhost/app_test/images/338.jpg'/>
<?php image_tag("338.jpg", "size=100x100 border=5"); ?>
==> <img src='http://localhost/app_test/images/338.jpg' border='5' width='100' height='100'/>
<?php image_tag("338.jpg", array("size" => "150x150", "border" => "10")); ?>
==> <img src='http://localhost/app_test/images/338.jpg' border='10' width='150' height='150'/>
*/
function image_tag($source, $options = array(), $html = false) {
	$url_path = odContext::getInstance()->get_request("request.initial")->get_referer();
	$url_path .= "/images/".$source;
	if (!is_array($options)) {
		$options_tab = __transform_string_into_tab($options);
	} else {
		$options_tab = $options;
	}
	$options_tab = array_merge(array("src" => $url_path), $options_tab);
	if (array_key_exists("size", $options_tab)) {
		$size_exploded = explode("x", $options_tab["size"]);
		unset($options_tab["size"]);
		if (isset($size_exploded[0])) {
			$options_tab = array_merge($options_tab, array("width" => $size_exploded[0]));
		}
		if (isset($size_exploded[1])) {
			$options_tab = array_merge($options_tab, array("height" => $size_exploded[1]));
		}
	}
	
	if ($html) {
	   return html_simple_tag("img", $options_tab);
	} else {
	   simple_tag("img", $options_tab);
	}
}

?>