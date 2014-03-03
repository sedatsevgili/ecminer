<?php
require_once("../utils/config.php");
require_once(PATH."utils/Ajax.php");

class StaticAjax extends Ajax {
	
	function __construct($db) {
		parent::__construct($db);
	}
	
	public function getTable() {
		require_once(PATH."controller/AjaxTableController.php");
		foreach($_GET as $key=>$val) {
			$$key = $val;
		}
		try {
			$tc = AjaxTableController::createFromSession($this->db,$tableId,$order,$limit,$path);
			$tc->run(true);
		} catch (Exception $exception) {
			echo $exception->getMessage();
			Error::unsetError();
		}
		exit();
	}
	
	public function deleteRow() {
		foreach($_GET as $key=>$val) {
			$$key = $val;
		}
		if(!empty($deleteModel)) {
			require_once(PATH."bean/".$deleteModel.".php");
			$model = new $deleteModel($this->db);
			$model->load($rowId);
			$model->delete();
			echo 1;
			exit();
		}
		echo intval($this->db->query("DELETE FROM ".$tables." WHERE id=".$rowId));
		exit();
	}
}

$staticAjax = new StaticAjax($db);
$staticAjax->run();