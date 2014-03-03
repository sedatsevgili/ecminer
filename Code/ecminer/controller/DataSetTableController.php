<?php
include_once(PATH."view/Table.php");
include_once(PATH."view/Column.php");
include_once(PATH."view/Cell.php");
include_once(PATH."view/Row.php");
include_once(PATH."controller/Pager.php");

class DataSetTableController {
	
	public $db;
	public $tableId;
	public $dataSet;
	public $header;
	public $footer;
	public $path;
	public $radioButtonName;
	public $checkBoxName;
	
	function __construct($db,$tableId,$dataSet,$header = "",$footer = "",$path = "",$radioButtonName = "",$checkBoxName = "") {
		$this->db = $db;
		$this->tableId = $tableId;
		$this->dataSet = $dataSet;
		$this->header = $header;
		$this->footer = $footer;
		$this->path = $path == "" ? PATH : $path;
		$this->checkBoxName = $checkBoxName;
		$this->radioButtonName = $radioButtonName;
		
		//ajax olayı sonra gerekirse
	}
	
	public function run() {
		
		//@TODO: class olan attribute'u kırmızı falan yazdır.
		try {
			$table = new Table($this->tableId, "Table",$this->header,$this->footer,"100%","1","0");
			if(!empty($this->checkBoxName)) {
				$checkBoxColumn = new Column("&nbsp;");
				$table->addColumn($checkBoxColumn);
			}
			if(!empty($this->radioButtonName)) {
				$radioButtonColumn = new Column("&nbsp;");
				$table->addColumn($radioButtonColumn);
			}
			$columns = array();
			$class = json_decode($_SESSION["AnalyzeClass"]);
			$classColumn = new Column("<span style='color:red;'>".$class->name."</span>");
			$table->addColumn($classColumn);
			
			foreach($this->dataSet->attributes as $attribute) {
				$columnContent = $attribute->name;
				$column = new Column($columnContent);
				$table->addColumn($column);
				$columns[$attribute->name] = $column;
			}
			foreach($this->dataSet->samples as $sample) {
				$cells = array();
				if(!empty($this->checkBoxName)) {
					$cells[] = new Cell($checkBoxColumn,"<input type='checkbox' name='".$this->checkBoxName."[]' value='".$sample->id."' />");
				}
				if(!empty($this->radioButtonName)) {
					$cells[] = new Cell($radioButtonColumn,"<input type='radio' name='".$this->radioButtonName."' value='".$sample->id."' />");
				}
				$cells[] = new Cell($classColumn,$sample->class->value);
				foreach($sample->attributes as $attribute) {
					$column = $columns[$attribute->name];
					$cells[] = new Cell($column,$attribute->value != '' ? $attribute->value : '-');
				}
				$table->addRow(new Row($cells,"Row","row_".mt_rand()));
			}
			echo $table->getHtml();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			var_dump($exception->getMessage());
			exit();
			Lib::redirect($this->path."static/error.php");
		}
	}
	
}