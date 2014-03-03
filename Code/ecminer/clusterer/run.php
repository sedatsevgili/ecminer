<?php
require_once("../utils/config.php");
require_once (PATH."utils/Runner.php");

class ClustererRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Clusterer");
	}
	
	public function addClusterer() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$clusterer = new Clusterer($this->db);
		$clusterer->name = $clusterername;
		if(isset($_FILES["filename"])) {
			$clusterer->fileName = $_FILES["filename"]["name"];
		}
		try {
			$clusterer->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("clusterer/add.php");
		}
		Lib::redirect("clusterer/list.php");
	}
	
	public function updateClusterer() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$clusterer = new Clusterer($this->db);
		try {
			$clusterer->load($id);
			$clusterer->name = $clusterername;
			if(isset($_FILES["filename"])) {
				$clusterer->fileName = $_FILES["filename"]["name"];
			}
			$clusterer->update(get_object_vars($clusterer));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("clusterer/edit.php?id=".$id);
		}
		Lib::redirect("clusterer/list.php");
	}
}

$clustererRunner = new ClustererRunner($db);
$clustererRunner->run();