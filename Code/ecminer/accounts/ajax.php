<?php
require_once("../utils/config.php");
require_once (PATH."utils/Ajax.php");

class AccountAjax extends Ajax {
	
	function __construct($db) {
		parent::__construct($db);
	}
	
	public function deleteRow() {
		foreach($_GET as $key=>$val) {
			$$key = $val;
		}
		echo intval($this->db->query("DELETE FROM ".$tables." WHERE id=".$rowId));
		exit();
	}
}

$accountAjax = new AccountAjax($db);
$accountAjax->run();