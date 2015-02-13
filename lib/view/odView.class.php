<?php
class odView
{
	public $template_name = "",
	$args = null,
	$datas = null;
	 
	public function __construct($datas = null, $args = null)
	{
		$this->datas = $datas;
		$this->args = $args;
	}
	 
	public function pre_show() {
	}
	public function post_show() {
	}
	public function show_default() {
	}
	 
	public function set_args($args)
	{
		$this->args = $args;
	}

	public function get_args()
	{
		return $this->args;
	}

	public function set_template_name($template_name)
	{
		$this->template_name = $template_name;
	}

	public function get_template_name()
	{
		return $this->template_name;
	}

	public function set_datas($datas)
	{
		$this->datas = $datas;
	}

	public function get_datas()
	{
		return $this->datas;
	}

	public function show()
	{
		$this->pre_show();
		if ($this->template_name != "")
		{
			$template = new odTemplate($this->template_name, $this->datas, $this->args);
			$template->parse_template();
		}
		else
		{
			$this->show_default();
		}
		$this->post_show();
	}
}
?>