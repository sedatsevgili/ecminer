<?php
require_once("../utils/config.php");

require_once (PATH."utils/Runner.php");

class ClassifierRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Classifier");
	}
	
	public function addClassifier() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$classifier = new Classifier($this->db);
		$classifier->name = $classifiername;
		if(isset($_FILES["filename"])) {
			$classifier->fileName = $_FILES["filename"]["name"];
		}
		try {
			$classifier->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("classifier/add.php");
		}
		Lib::redirect("classifier/list.php");
	}
	
	public function updateClassifier() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$classifier = new Classifier($this->db);
		try {
			$classifier->load($id);
			$classifier->name = $classifiername;
			if(isset($_FILES["filename"])) {
				$classifier->fileName = $_FILES["filename"]["name"];
			}
			$classifier->update(get_object_vars($classifier));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("classifier/edit.php?id=".$id);
		}
		Lib::redirect("classifier/list.php");
	}
}

$classifierRunner = new ClassifierRunner($db);
$classifierRunner->run();