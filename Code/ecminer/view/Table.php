<?php
require_once("View.php");

class Table extends View{
	public $header;
	public $footer;
	public $width;
	public $cellPadding;
	public $cellSpacing;
	public $align;
	
	public $cols;
	public $columnCount;
	public $rows;
	public $rowCount;
	
	public $pagerHeader;
	public $pagerFooter;
	
	function __construct($id, $cssClass = "Table", $header= "", $footer = "", $width="100%", $cellPadding="0", $cellSpacing="0", $align="center") {
		parent::__construct($id, $cssClass);
		$this->header = $header;
		$this->footer = $footer;
		$this->width = $width;
		$this->cellPadding = $cellPadding;
		$this->cellSpacing = $cellSpacing;
		$this->align = $align;
		$this->cols = array();
		$this->rows = array();
		$this->columnCount = 0;
		$this->rowCount = 0;
	}
	
	public function addColumn($column) {
		if($this->rowCount>0) {
			ExceptionController::throwException("View","ERROR_ROW_ADDED_TO_TABLE");
		}
		if(!($column instanceof Column)) {
			ExceptionController::throwException("View","ERROR_COLUMN_IS_NOT_VALID_OBJECT");
		}
		$this->cols[] = $column;
		$this->columnCount++;
	}
	
	public function addRow($row) {
		if($this->columnCount == 0) {
			ExceptionController::throwException("View","ERROR_THERE_IS_NO_COLUMN");
		}
		if(!($row instanceof Row)) {
			ExceptionController::throwException("View","ERROR_ROW_IS_NOT_VALID_OBJECT");
		}
		if($row->cellCount != $this->columnCount) {
			ExceptionController::throwException("View","ERROR_COLUMN_COUNT_DOESNT_MATCH");
		}
		$this->rows[] = $row;
		$this->rowCount++;
	}
	
	public function getHtml() {
		$html = '
		<table id="'.$this->id.'" class="'.$this->cssClass.'" width="'.$this->width.'" cellpadding="'.$this->cellPadding.'" cellspacing="'.$this->cellSpacing.'" align="'.$this->align.'">';
		if(!empty($this->header)) { $html .= '<tr><td class="Header" colspan="'.$this->columnCount.'">'.$this->header.'</td></tr>'; }
		$html .= $this->pagerHeader;
		$html .= '<tr>';
		for($i=0;$i<$this->columnCount;$i++) {
			$column = $this->cols[$i];
			$html .= $column->getHtml();
		}
		$html .= '</tr>';
		for($i=0;$i<$this->rowCount;$i++) {
			$row = $this->rows[$i];
			$html .= $row->getHtml();
		}
		$html .= $this->pagerFooter;
		if(!empty($this->footer)) { $html .= '<tr><td class="Footer" colspan="'.$this->columnCount.'">'.$this->footer.'</td></tr>'; }
		$html .= '</table>';
		return $html;
	}
	
	
}