<?php
require_once("../utils/config.php");
require_once (PATH."utils/Runner.php");

class AccountRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Account");
	}
	
	public function login() {
		extract($_POST);
		$account = new Account($this->db);
		$account->username = $username;
		$account->pass = $pass;
		try {
			$account->loadFromLogin();
		} catch (Exception $exception) {
			Error::set($exception->getMessage(),"Giriş Hatası");
			Lib::redirect("static/default.php");
		}
		$_SESSION["IsMember"] = 1;
		$_SESSION["MemberId"] = intval($account->id);
		Lib::redirect("members/default.php");
	}
	
	public function logout() {
		unset($_SESSION["IsMember"]);
		Lib::redirect("static/default.php");
	}
	
}

$runner = new AccountRunner($db);
$runner->run();