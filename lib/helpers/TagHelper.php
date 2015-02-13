<?php

/*
 tag
*/
function tag($name, $content = '', $options = null)
{
	echo sprintf("<%s", $name);
	foreach($options as $key => $value)
	{
		__echo_property($key, $value);
	}
	if ($content == '')
	{
		echo sprintf("></%s>", $name);
	}
	else
	{
		echo sprintf(">%s</%s>", $content, $name);
	}
}

/*
 html_tag
*/
function html_tag($name, $content = '', $options = null)
{
   $html = "";
	$html .= sprintf("<%s", $name);
	foreach($options as $key => $value)
	{
		$html .= __return_property($key, $value);
	}
	if ($content == '')
	{
		$html .=  sprintf("></%s>", $name);
	}
	else
	{
		$html .=  sprintf(">%s</%s>", $content, $name);
	}
	return $html;
}

/*
 simple_tag
*/
function simple_tag($name, $options = null)
{
	echo sprintf("<%s", $name);
	foreach($options as $key => $value)
	{
		__echo_property($key, $value);
	}
	echo "/>";
}

/*
 html_simple_tag
*/
function html_simple_tag($name, $options = null)
{
   $html = sprintf("<%s", $name);
	foreach($options as $key => $value)
	{
		$html .= __return_property($key, $value);
	}
	$html .= "/>";
	return $html;
}

/*
 cdata
*/
function cdata($content)
{
	return "<![CDATA[".$content."]]>";
}

/*
 comment_if
*/
function comment_if($condition, $content)
{
	return "<!--[if ".$condition."]>".$content."<![endif]-->";
}

/*
 comment
*/
function comment($content)
{
	return "<!--".$content."-->";
}

/*
 escape_js
*/
function escape_js($js = '')
{
	$js = preg_replace('/\r\n|\n|\r/', "\\n", $js);
	$js = preg_replace('/(["\'])/', '\\\\\1', $js);

	return $js;
}

?>