<?php
require_once("View.php");

class Column extends View {
	
	public $content;
	
	function __construct($content = "", $cssClass = "Column", $id = "") {
		parent::__construct($id,$cssClass);
		$this->content = $content;
	}
	
	public function getHtml() {
		$html = '<th class="'.$this->cssClass.'">'.$this->content.'</th>';
		return $html;
	}
}
