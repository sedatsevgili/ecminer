<?php
require_once("../utils/config.php");
require_once (PATH."utils/Runner.php");

class ImportTypeRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"ImportType");
	}
	
	public function addImportType() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$importType = new ImportType($this->db);
		$importType->name = $importTypename;
		try {
			$importType->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("importtype/add.php");
		}
		Lib::redirect("importtype/list.php");
	}
	
	public function updateImportType() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$importType = new ImportType($this->db);
		try {
			$importType->load($id);
			$importType->name = $importTypename;
			$importType->update(get_object_vars($importType));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("importtype/edit.php?id=".$id);
		}
		Lib::redirect("importtype/list.php");
	}
}

$importFieldRunner = new ImportTypeRunner($db);
$importFieldRunner->run();