<?php
require_once("../../utils/config.php");
require_once (PATH."utils/Runner.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

class ImportRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Import");
	}
	
	public function addImport()  {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$import = new Import($this->db);
		require_once(PATH."bean/Site.php");
		$site = new Site($this->db);
		if(!$site->load(intval($site_id))) {
			Error::set("Site yüklenemedi");
			Lib::redirect("members/import/add.php");
		}
		if($site->account_id!=$_SESSION["MemberId"]) {
			Error::set("Geçerli izniniz yok");
			Lib::redirect("members/import/add.php");
		}
		$import->site_id = $site_id;
		$import->import_type_id = $import_type_id;
		$import->import_field_id = $import_field_id;
		$import->create_time = date("Y-m-d H:i:s",time());
		$import->ip_address = $_SERVER["REMOTE_ADDR"];
		try {
			$import->add();	
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("members/import/add.php");
		}
		Lib::redirect("members/import/list.php");
		exit();
		
	}
	
	public function updateImport() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$id = intval($_GET["id"]);
		$import = new Import($this->db);
		try {
			$import->load($id);
			require_once(PATH."bean/Site.php");
			$site = new Site($this->db);
			if(!$site->load($site_id)) {
				Error::set("Site yüklenemedi");
				Lib::redirect("members/import/add.php");
			}
			if($site->account_id!=$_SESSION["MemberId"]) {
				Error::set("Geçerli izniniz yok");
				Lib::redirect("members/import/add.php");
			}
			$import->site_id = $site_id;
			$import->import_type_id = $import_type_id;
			$import->import_field_id = $import_field_id;
			$import->update();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("members/import/edit.php?id=".$id);
		}
		Lib::redirect("members/import/list.php");
		exit();
	}
}

$importRunner = new ImportRunner($db);
$importRunner->run();