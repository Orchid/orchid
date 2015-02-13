<?php

/*
 __echo_property
*/
function __echo_property($property, $value)
{
	echo sprintf(" %s='%s'", $property, $value);
}

/*
 __return_property
*/
function __return_property($property, $value)
{
	return sprintf(" %s='%s'", $property, $value);
}

/*
 __echo_properties
*/
function __echo_properties($options)
{
	$opts_exp = explode(" ", $options);
	foreach($opts_exp as $opt)
	{
		$opt_exp = explode("=", $opt);
		if ($opt_exp[0] == "confirm")
		{
			__echo_property("onclick", sprintf("return confirm(\"%s\");", str_replace("_", " ", $opt_exp[1])));
			continue;
		}
		if ($opt_exp[0] == "popup")
		{
			if ($opt_exp[1] == "true")
			{
				__echo_property("onclick", "window.open(this.href);return false;");
			}
			else
			{
				$opt1_exp = explode("|", $opt_exp[1]);
				if (count($opt1_exp) != 2)
				{
					// on lève une exception
					throw new odException('The popup option parameter must be contain 2 entries in __echo_properties function.');
					return false;
				}
				__echo_property("onclick", sprintf("window.open(this.href,\"%s\",\"%s\");return false;", $opt1_exp[0], str_replace(":", "=", $opt1_exp[1])));
			}
			continue;
		}
		echo sprintf(" %s", $opt);
	}
}

/*
 __return_properties
*/
function __return_properties($options)
{
   $html = "";
	$opts_exp = explode(" ", $options);
	foreach($opts_exp as $opt)
	{
		$opt_exp = explode("=", $opt);
		if ($opt_exp[0] == "confirm")
		{
			$html .= __return_property(" onclick", sprintf("return confirm(\"%s\");", str_replace("_", " ", $opt_exp[1])));
			continue;
		}
		if ($opt_exp[0] == "popup")
		{
			if ($opt_exp[1] == "true")
			{
				$html .= __return_property(" onclick", "window.open(this.href);return false;");
			}
			else
			{
				$opt1_exp = explode("|", $opt_exp[1]);
				if (count($opt1_exp) != 2)
				{
					// on lève une exception
					throw new odException('The popup option parameter must be contain 2 entries in __echo_properties function.');
					return false;
				}
				$html .= __return_property(" onclick", sprintf("window.open(this.href,\"%s\",\"%s\");return false;", $opt1_exp[0], str_replace(":", "=", $opt1_exp[1])));
			}
			continue;
		}
		$html .= sprintf(" %s", $opt);
	}
}

/*
 __transform_string_into_tab
*/
function __transform_string_into_tab($str)
{
	$tab = array();
	if (!is_array($str))
	{
		$str_exp = explode(" ", $str);
		foreach($str_exp as $s)
		{
			$s_exp = explode("=", $s);
			$tab[$s_exp[0]] = trim($s_exp[1], "'");
		}
	}
	return $tab;
}

/*
 __transform_string_into_tab
*/
function __encode_text($text)
{
	$encoded_text = '';

	for ($i = 0; $i < strlen($text); $i++)
	{
		$char = $text{$i};
		$r = rand(0, 100);

		# roughly 10% raw, 45% hex, 45% dec
		# '@' *must* be encoded. I insist.
		if ($r > 90 && $char != '@')
		{
			$encoded_text .= $char;
		}
		else if ($r < 45)
		{
			$encoded_text .= '&#x'.dechex(ord($char)).';';
		}
		else
		{
			$encoded_text .= '&#'.ord($char).';';
		}
	}

	return $encoded_text;
}


/*
 link_to("click here", "/module/action/param1/param2");
=>
<a href="http://localhost/app_test/module/action/param1/param2">click here</a>
 
option can take this parameters :
"style" => "text-decoration:none"
"class" => "links"
"target" => "_blank"
"onclick" => "code javascript"
"confirm" => "confirm_text"
"popup" => true | array("name_of_popup", "popup_options") note : name in first
"absolute" => true | false
*/
function link_to($content, $route, $options = array())
{
	$format = "<a href='%s'";
	if (strtolower(substr($route, 0, 11)) == "javascript:" or strtolower(substr($route, 0, 7)) == "mailto:")	{
		echo sprintf($format, $route);
	} else {
		//echo sprintf($format, make_url($route, isset($options["absolute"]) ? $options["absolute"] : true));
		$request_referer = odContext::getInstance()->get_request("request.initial")->get_referer();
		//echo $request_referer;
		echo sprintf($format, $request_referer."/".$route);
	}

	if (is_array($options)) // les options sont mises dans un tableau
	{
		if (isset($options["confirm"]))
		{
			__echo_property("onclick", sprintf("return confirm(\"%s\");", str_replace("_", " ", $options["confirm"])));
		}
		if (isset($options["popup"]))
		{
			if (is_array($options["popup"]))
			{
				if (count($options["popup"]) != 2)
				{
					// Lever une exception
					throw new odException('The popup option parameter must be contain 2 entries in link_to helper.');
				}
				else
				{
					__echo_property("onclick", sprintf("window.open(this.href,\"%s\",\"%s\");return false;", $options["popup"][0], $options["popup"][1]));
				}
			}
			else
			{
				__echo_property("onclick", "window.open(this.href);return false;");
			}
		}
		if (isset($options["class"]))
		{
			__echo_property("class", $options["class"]);
		}
		if (isset($options["style"]))
		{
			__echo_property("style", $options["style"]);
		}
		if (isset($options["target"]))
		{
			__echo_property("target", $options["target"]);
		}
		if (isset($options["id"]))
		{
			__echo_property("id", $options["id"]);
		}
	}
	else // les option sont mises dans une chaine de caractères
	{
		__echo_properties($options);
	}

	 
	$format = ">%s</a>";
	echo sprintf($format, $content);
}

function html_link_to($content, $route, $options = array())
{
   $html = "";
	$format = "<a href='%s'";
	if (strtolower(substr($route, 0, 11)) == "javascript:" or strtolower(substr($route, 0, 7)) == "mailto:")	{
		$html .= sprintf($format, $route);
	} else {
		//echo sprintf($format, make_url($route, isset($options["absolute"]) ? $options["absolute"] : true));
		$request_referer = odContext::getInstance()->get_request("request.initial")->get_referer();
		//echo $request_referer;
		$html .= sprintf($format, $request_referer."/".$route);
	}

	if (is_array($options)) // les options sont mises dans un tableau
	{
		if (isset($options["confirm"]))
		{
			$html .= __return_property("onclick", sprintf("return confirm(\"%s\");", str_replace("_", " ", $options["confirm"])));
		}
		if (isset($options["popup"]))
		{
			if (is_array($options["popup"]))
			{
				if (count($options["popup"]) != 2)
				{
					// Lever une exception
					throw new odException('The popup option parameter must be contain 2 entries in link_to helper.');
				}
				else
				{
					$html .= __return_property("onclick", sprintf("window.open(this.href,\"%s\",\"%s\");return false;", $options["popup"][0], $options["popup"][1]));
				}
			}
			else
			{
				$html .= __return_property("onclick", "window.open(this.href);return false;");
			}
		}
		if (isset($options["class"]))
		{
			$html .= __return_property("class", $options["class"]);
		}
		if (isset($options["style"]))
		{
			$html .= __return_property("style", $options["style"]);
		}
		if (isset($options["target"]))
		{
			$html .= __return_property("target", $options["target"]);
		}
		if (isset($options["id"]))
		{
			$html .= __return_property("id", $options["id"]);
		}
	}
	else // les option sont mises dans une chaine de caractères
	{
		$html .= __return_properties($options);
	}
	 
	$format = ">%s</a>";
	$html .= sprintf($format, $content);
	
	return $html;
}

/*
 link_to_if
*/
function link_to_if($condition, $content, $route, $options = array())
{
	if (!isset($condition))
	{
		// Lever une exception
		throw new odException('The condition parameter is null in link_to_if helper.');
	}
	else
	{
		if ($condition)
		{
			link_to($content, $route, $options);
		}
		else
		{
			// la condition n'est pas remplie donc on affiche <span>content</span>
			$options_tab = $options;
			if (!is_array($options))
			{
				$options_tab = __transform_string_into_tab($options);
				unset($options_tab["target"]);
				unset($options_tab["confirm"]);
				unset($options_tab["popup"]);
				unset($options_tab["absolute"]);
			}
			tag("span", $content, $options_tab);
		}
	}
}

/*
 anchor_to
*/
function anchor_to($content, $name)
{
	tag("a", $content, array("name" => "#".$name));
}

/*
 button_to
*/
function button_to($value, $route, $options = array())
{
	if (is_array($options))
	{
		$options_tab = $options;
	}
	else
	{
		$options_tab = __transform_string_into_tab($options);
	}

	unset($options_tab["confirm"]);
	unset($options_tab["popup"]);
	unset($options_tab["target"]);
	unset($options_tab["onclick"]);

	$opt = array("value" => $value,
			"onclick" => "document.location.href=\"".make_url($route)."\";",
			"type" => "button");
	$opt = array_merge($opt, $options_tab);
	 
	tag("input", "", $opt);
}

/*
 button_to_if
*/
function button_to_if($condition, $value, $route, $options = array())
{
	if (!isset($condition))
	{
		// Lever une exception
		throw new odException('The condition parameter is null in button_to_if helper.');
	}
	else
	{
		if (is_array($options))
		{
			$options_tab = $options;
		}
		else
		{
			$options_tab = __transform_string_into_tab($options);
		}
			
		if ($condition)
		{
			$opt = array("value" => $value,
					"onclick" => "document.location.href=\"".make_url($route)."\";",
					"type" => "button");
		}
		else
		{
			$opt = array("value" => $value,
					"onclick" => "document.location.href=\"".make_url($route)."\";",
					"disabled" => "disabled",
					"type" => "button");
		}

		unset($options_tab["confirm"]);
		unset($options_tab["popup"]);
		unset($options_tab["target"]);
		unset($options_tab["onclick"]);

		$opt = array_merge($opt, $options_tab);

		tag("input", "", $opt);
	}
}

/*
 mail_to
*/
function mail_to($email, $name = "", $options = array(), $default_value = array())
{
	if ($name == "")
	{
		$name = $email;
	}

	$options_tab = array();
	if (!is_array($options))
	{
		$options_tab = __transform_string_into_tab($options);
	}
	else
	{
		$options_tab = $options;
	}

	$def_vals = "";
	if ($default_value != null)
	{
		$def_vals = "?";
		foreach($default_value as $key => $value)
		{
			$def_vals .= sprintf("%s=%s&", $key, $value);
		}
		$def_vals = trim($def_vals, "&");
	}
	 
	if (isset($options["encode"]) and $options["encode"] == true)
	{
		$email_encoded = __encode_text("mailto:".$email.$def_vals);
		unset($options_tab["encode"]);
	}
	else
	{
		$email_encoded = "mailto:".$email.$def_vals;
	}
	 
	$options_tab = array_merge(array("href" => $email_encoded), $options_tab);

	tag("a", $name, $options_tab);
}

function staticUrl($uri) {
		$request_referer = odContext::getInstance()->get_request("request.initial")->get_referer();
		return $request_referer."/".$uri;
}

?>