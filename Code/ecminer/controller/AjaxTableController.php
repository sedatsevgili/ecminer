<?php
include_once(PATH."view/Table.php");
include_once(PATH."view/Column.php");
include_once(PATH."view/Cell.php");
include_once(PATH."view/Row.php");
include_once(PATH."controller/Pager.php");

class AjaxTableController {
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
	
	public $addUrl;
	public $editUrl;
	public $deleteModel;
	public $path;
	
	public $checkBoxName;
	public $radioButtonName;

	function __construct($db, $tableId, $fields, $fieldMasks, $tables, $joins = "", $conditions = "", $order = "", $limit = "", $header = "", $footer = "", $addUrl = "", $editUrl = "", $deleteModel = "", $path = "", $radioButtonName = "", $checkBoxName = "") {
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
		$this->limit = $limit != "0,0" ? $limit : "";
		$this->header = $header;
		$this->footer = $footer;
		$this->addUrl = $addUrl;
		$this->editUrl = $editUrl;
		$this->deleteModel = $deleteModel;
		$this->path = $path == "" ? PATH : $path;
		$this->checkBoxName = $checkBoxName;
		$this->radioButtonName = $radioButtonName;
		
		$_SESSION["Table"][$tableId]["fields"] = $fields;
		$_SESSION["Table"][$tableId]["fieldMasks"] = $fieldMasks;
		$_SESSION["Table"][$tableId]["tables"] = $tables;
		$_SESSION["Table"][$tableId]["joins"] = $joins;
		$_SESSION["Table"][$tableId]["conditions"] = $conditions;
		$_SESSION["Table"][$tableId]["header"] = $header;
		$_SESSION["Table"][$tableId]["footer"] = $footer;
		$_SESSION["Table"][$tableId]["addUrl"] = $addUrl;
		$_SESSION["Table"][$tableId]["editUrl"] = $editUrl;
		$_SESSION["Table"][$tableId]["deleteModel"] = $deleteModel;
		$_SESSION["Table"][$tableId]["checkBoxName"] = $checkBoxName;
		$_SESSION["Table"][$tableId]["radioButtonName"] = $radioButtonName;
	}
	
	public static function createFromSession($db,$tableId,$order,$limit,$path) {
		if(!isset($_SESSION["Table"][$tableId])) {
			//ExceptionController::throwException("View","ERROR_TABLE_COULDNT_LOAD_FROM_SESSION");
		}
		foreach($_SESSION["Table"][$tableId] as $key=>$val) {
			$$key = $val;
		}
		unset($_SESSION["Table"][$tableId]);
		return new AjaxTableController($db,$tableId,$fields,$fieldMasks,$tables,$joins,$conditions,$order,$limit,$header,$footer,$addUrl,$editUrl,$deleteModel,$path,$radioButtonName,$checkBoxName);
	}
	
	public function run($inAjax = false) {
		try {
			$this->db->select("SQL_CALC_FOUND_ROWS ".implode(",",array_keys($this->fields)),$this->tables,$this->joins,$this->conditions,$this->order);
			$this->db->select("FOUND_ROWS() AS foundRows");
			$rowCount = $this->db->rows[0]["foundRows"];
			$limitValues = explode(",",$this->limit);
			$offset = intval($limitValues[0]);
			$limit = intval($limitValues[1]);
			$pager = new Pager($this->tableId,$this->order,$rowCount,$offset,$limit,$this->path);
			$this->db->select(implode(",",array_keys($this->fields)),$this->tables,$this->joins,$this->conditions,$this->order,$this->limit);
			$this->header .= '<a style="float: right; margin-right: 10px;" href="javascript: void(0);" onclick="ajaxTable(\''.$this->tableId.'\',\''.$this->order.'\',\''.$this->limit.'\',\''.$this->path.'\')"><img src="'.$this->path.'images/refresh.png" width="16" height="16" valign="middle" border="0" /></a>';
			$this->header .= '<a style="float: right; margin-right: 10px;" href="'.$this->addUrl.'"><img src="'.$this->path.'images/add.png" width="16" height="16" border="0" /></a>';
			$this->header .= '<img id="loadingTable_'.$this->tableId.'" src="'.$this->path.'images/load.gif" width="16" height="16" style="display: none; float: right; margin-right: 10px;" border="0" />';
			$table = new Table($this->tableId, "Table",$this->header,$this->footer,"100%","1","0");
			$columns = array();
			$columnIndex = 0;
			$maskFieldNames = array_keys($this->fieldMasks);
			if(!empty($this->checkBoxName)) {
				$checkBoxColumn = new Column("&nbsp;");
				$table->addColumn($checkBoxColumn);
			}
			if(!empty($this->radioButtonName)) {
				$radioButtonColumn = new Column("&nbsp;");
				$table->addColumn($radioButtonColumn);
			}
			foreach($this->fields as $fieldDbName=>$fieldViewName) {
				$columnContent = $fieldViewName;
				if(empty($this->order) || strpos($this->order,$maskFieldNames[$columnIndex]) === false || strpos($this->order,' desc') === false) {
					//$columnContent = '<a href="javascript: void(0);" onclick="ajaxTable(\''.$this->tableId.'\',\''.$fieldDbName.' desc\',\''.$this->limit.'\')">'.$fieldViewName.'</a>';
					$columnContent = "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($maskFieldNames[$columnIndex],ENT_QUOTES,"UTF-8")." desc\",\"".$this->limit."\",\"".$this->path."\")'>".$fieldViewName."</a>";
				} else {
					//$columnContent = '<a href="javascript: void(0);" onclick="ajaxTable(\''.$this->tableId.'\',\''.$fieldDbName.' asc\',\''.$this->limit.'\')">'.$fieldViewName.'</a>'
					$columnContent = "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($maskFieldNames[$columnIndex],ENT_QUOTES,"UTF-8")." asc\",\"".$this->limit."\",\"".$this->path."\")'>".$fieldViewName."</a>";;
				}
				$column = new Column($columnContent);
				$table->addColumn($column);
				$columns[$maskFieldNames[$columnIndex]] = $column;
				$columnIndex++;
			}
			if(!empty($this->editUrl)) $editColumn = new Column("&nbsp;");
			$deleteColumn = new Column("&nbsp;");
			if(!empty($this->editUrl)) $table->addColumn($editColumn);
			$table->addColumn($deleteColumn);
			foreach($this->db->rows as $dbRow) {
				$cells = array();
				if(!empty($this->checkBoxName)) {
					$cells[] = new Cell($checkBoxColumn,"<input type='checkbox' name='".$this->checkBoxName."[]' value='".$dbRow["id"]."' />");
				}
				if(!empty($this->radioButtonName)) {
					$cells[] = new Cell($radioButtonColumn,"<input type='radio' name='".$this->radioButtonName."' value='".$dbRow["id"]."' />");
				}
				foreach($dbRow as $dbKey=>$dbVal) {
					// $dbKey and $fieldDbName will be equal forever
					$column = $columns[$dbKey];
					if(!empty($this->fieldMasks[$dbKey])) {
						/*echo '$dbVal = '.str_replace('<%'.$dbKey.'%>',"'".$dbVal."'",$this->fieldMasks[$dbKey]);
						exit();*/
						eval('$dbVal = '.str_replace('<%'.$dbKey.'%>',"'".$dbVal."'",$this->fieldMasks[$dbKey]));
					}
					$cells[]  = new Cell($column,$dbVal);
				}
				if(!empty($this->editUrl)) 	$cells[] = new Cell($column,'<a href="'.$this->editUrl.$dbRow["id"].'"><img src="'.$this->path.'images/edit.png" width="24" height="24" border="0" /></a>');
				$deleteTable = explode(" ",$this->tables);
				$deleteTable = $deleteTable ? $deleteTable[0] : $this->tables;
				$cells[] = new Cell($column,'<a href="javascript: void(0);" onclick="ajaxDeleteRow(\''.$this->tableId.'\',\''.$deleteTable.'\',\''.$dbRow["id"].'\','.($this->deleteModel ? '\''.$this->deleteModel.'\'' : '\'\''). ')"><img src="'.$this->path.'images/delete.png" width="24" height="24" border="0" /></a>');
				$table->addRow(new Row($cells,"Row","row_".$dbRow["id"]));
			}
			$pagerHtml = $pager->run();
			$table->pagerHeader = "<tr><td class='PagerHeader' colspan='".$table->columnCount."'>".$pagerHtml."</td></tr>";
			$table->pagerFooter = "<tr><td class='PagerFooter' colspan='".$table->columnCount."'>".$pagerHtml."</td></tr>";
			echo $inAjax ? json_encode($table->getHtml()) : '<span id="'.$this->tableId.'_wrapper">'.$table->getHtml().'</span>';
		} catch (Exception $exception) {
			Lib::redirectWithError($exception->getMessage());
		}
	}
	
}