<?php
require_once("../utils/config.php");
require_once (PATH."utils/Runner.php");

class SiteRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Site");
	}
	
	public function addSite() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$site = new Site($this->db);
		$site->account_id = $account_id;
		$site->create_time = date("Y-m-d H:i:s",time());
		$site->status = ($status == "on" ? 1 : 0);
		$site->url = $url;
		try {
			$site->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("site/add.php");
		}
		Lib::redirect("site/list.php");
	}
	
	public function updateSite() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$site = new Site($this->db);
		try {
			$site->load($id);
			$site->account_id = $account_id;
			$site->url = $url;
			$site->status = $status;
			$site->update(get_object_vars($site));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("site/edit.php?id=".$id);
		}
		Lib::redirect("site/list.php");
	}
	
}

$siteRunner = new SiteRunner($db);
$siteRunner->run();