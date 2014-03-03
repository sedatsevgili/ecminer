<?php
require_once("../utils/config.php");
include_once(PATH."headers/admin.php");

Error::write();
require_once (PATH."utils/Runner.php");

class ImportFieldRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"ImportField");
	}
	
	public function addImportField() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$importField = new ImportField($this->db);
		$importField->name = $importFieldname;
		$importField->import_class_id = intval($class_id);
		try {
			$importField->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("importfield/add.php");
		}
		Lib::redirect("importfield/list.php");
	}
	
	public function updateImportField() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$importField = new ImportField($this->db);
		try {
			$importField->load($id);
			$importField->name = $importFieldname;
			$importField->import_class_id = intval($class_id);
			$importField->update(get_object_vars($importField));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("importfield/edit.php?id=".$id);
		}
		Lib::redirect("importfield/list.php");
	}
}

$importFieldRunner = new ImportFieldRunner($db);
$importFieldRunner->run();