<?php
class Ajax {
	public $do;
	public $db;
	
	function __construct($db) {
		if(!isset($_GET["do"])) {
			Error::set("Lütfen bir eylem seçiniz");
			Error::write();
		}
		$this->db = $db;
		$this->do = $_GET["do"];
	}
	
	function run() {
		if(!method_exists(get_class($this),$this->do)) {
			ExceptionController::throwException("Runner","ERROR_METHOD_NOT_FOUND");
		}
		$this->{$this->do}();
	}
}