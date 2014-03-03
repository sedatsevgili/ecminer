<?php
include_once(PATH."view/Table.php");
include_once(PATH."view/Column.php");
include_once(PATH."view/Cell.php");
include_once(PATH."view/Row.php");

class TableController {
	public $db;
	public $tableId;
	public $fields;
	public $fieldMasks;
	public $tables;
	public $joins;
	public $conditions;
	public $order;
	public $limit;
	
	public $header;
	public $footer;
	
	function __construct($db, $tableId, $fields, $fieldMasks, $tables, $joins = "", $conditions = "", $order = "", $limit = "", $header = "", $footer = "") {
		$this->db = $db;
		$this->tableId = $tableId;
		$this->fields = $fields;
		$this->fieldMasks = $fieldMasks;
		if(count($fields) != count($fieldMasks)) {
			ExceptionController::throwException("View","ERROR_FIELD_MASKS_DONT_MATCH");
		}
		$this->tables = $tables;
		$this->joins = $joins;
		$this->conditions = $conditions;
		$this->order = $order;
		$this->limit = $limit;
		$this->header = $header;
		$this->footer = $footer;
	}
	
	public function run() {
		try {
			$this->db->select(implode(",",array_keys($this->fields)),$this->tables,$this->joins,$this->conditions,$this->order,$this->limit);
			$table = new Table($this->tableId, "Table",$this->header,$this->footer,"100%","1","0");
			$columns = array();
			$columnIndex = 0;
			$maskFieldNames = array_keys($this->fieldMasks);
			foreach($this->fields as $fieldDbName=>$fieldViewName) {
				$columnContent = $fieldViewName;
				$column = new Column($columnContent);
				$table->addColumn($column);
				$columns[$maskFieldNames[$columnIndex]] = $column;
				$columnIndex++;
			}
			foreach($this->db->rows as $dbRow) {
				$cells = array();
				foreach($dbRow as $dbKey=>$dbVal) {
					// $dbKey and $fieldDbName will be equal forever
					$column = $columns[$dbKey];
					if(!empty($this->fieldMasks[$dbKey])) {
						eval('$dbVal = '.str_replace('<%'.$dbKey.'%>',"'".$dbVal."'",$this->fieldMasks[$dbKey]));
					}
					$cells[]  = new Cell($column,$dbVal);
				}
				$table->addRow(new Row($cells,"Row","row_".$dbRow["id"]));
			}
			echo $table->getHtml();
		} catch (Exception $exception) {
			Lib::redirectWithError($exception->getMessage());
		}
	}
}