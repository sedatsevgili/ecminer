<?php
require_once("../../utils/config.php");
require_once (PATH."utils/Runner.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

class SiteRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Site");
	}
	
	public function addSite() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$site = new Site($this->db);
		$site->account_id = intval($_SESSION["MemberId"]);
		$site->create_time = date("Y-m-d H:i:s",time());
		$site->status = intval($status);
		$site->url = $url;
		try {
			$site->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("members/site/add.php");
		}
		Lib::redirect("members/site/list.php");
	}
	
	public function updateSite() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$site = new Site($this->db);
		try {
			if(!$site->load($id)) {
				throw Exception("Site yüklenemedi");
			}
			if($site->account_id!=$_SESSION["MemberId"]) {
				throw Exception("Geçerli izniniz yok");
			}
			$site->url = $url;
			$site->status = $status;
			$site->update(get_object_vars($site));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("members/site/edit.php?id=".$id);
		}
		Lib::redirect("members/site/list.php");
	}
	
}

$siteRunner = new SiteRunner($db);
$siteRunner->run();