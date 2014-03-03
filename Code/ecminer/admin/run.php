<?php
require_once("../utils/config.php");

require_once (PATH."utils/Runner.php");

class UserRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"User");
	}
	
	public function login() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$user = new User($this->db);
		$user->username = $username;
		$user->pass = $pass;
		try {
			$user->loadFromLogin();
		} catch (Exception $ex) {
			Error::set($ex->getMessage(),"Yönetici Girişi Hatası");
			Lib::redirect("admin/login.php");
		}
		$_SESSION["IsAdmin"] = 1;
		Lib::redirect("admin/default.php");
	}
	
	public function logout() {
		unset($_SESSION["IsAdmin"]);
		Lib::redirect("admin/default.php");
	}
	
}

$runner = new UserRunner($db);
$runner->run();