<?php
require_once("View.php");

class Cell extends View{
	public $column;
	public $content;
	
	function __construct($column,$content = "",$cssClass = "Cell", $id="") {
		parent::__construct($id,$cssClass);
		if(!($column instanceof Column)) {
			var_dump($column);
			exit();
			ExceptionController::throwException("View","ERROR_COLUMN_IS_NOT_VALID_OBJECT");
		}
		$this->column = $column;
		$this->cssClass = $cssClass;
		$this->content = $content;
	}
	
	public function getHtml() {
		return $this->content;
	}
}