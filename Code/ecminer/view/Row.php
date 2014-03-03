<?php
require_once("View.php");

class Row extends View{
	public $cells;
	public $cellCount;
	
	function __construct($cells,$cssClass = "Row", $id = "") {
		parent::__construct($id,$cssClass);
		if(!is_array($cells)) {
			ExceptionController::throwException("View","ERROR_CELL_ARRAY_IS_NOT_VALID");
		}
		$this->cells = $cells;
		$this->cellCount = count($cells);
	}
	
	public function getHtml() {
		$html = '<tr '.($this->id != '' ? 'id="'.$this->id.'"' : '').' class="Row">';
		for($i=0;$i<$this->cellCount;$i++) {
			$cell = $this->cells[$i];
			$html .= '<td class="'.$cell->cssClass.'">'.$cell->getHtml().'</td>';
		}
		$html .= '</tr>';
		return $html;
	}
}