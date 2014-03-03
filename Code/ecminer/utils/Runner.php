<?php
class Runner {
	
	public $do;
	public $modelName;
	public $db;
	public $importModel;
	
	function __construct($db,$modelName,$importModel = false) {
		if(!isset($_GET["do"])) {
			Lib::redirectWithError("Lütfen bir eylem seçiniz");
		}
		$this->modelName = str_replace(".","",$modelName);
		$this->modelName = str_replace("/","",$this->modelName);
		$this->importModel = $importModel;
		$this->do = $_GET["do"];
		require_once (PATH."bean/".($importModel ? "import/" : "").$this->modelName.".php");
		
		$this->db = $db;
	}
	
	function run() {
		if(!method_exists(get_class($this),$this->do)) {
			ExceptionController::throwException("Runner","ERROR_METHOD_NOT_FOUND");
		}
		$this->{$this->do}();
	}
	
}