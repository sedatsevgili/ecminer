<?php
require_once("../utils/config.php");
require_once (PATH."utils/Runner.php");

class AccountRunner extends Runner {
	
	function __construct($db) {
		parent::__construct($db,"Account");
	}
	
	public function addAccount() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		$account = new Account($this->db);
		$account->username = $username;
		$account->pass = sha1("Ec_SiGn".md5($pass).$pass);
		$account->email = $email;
		$account->firstname = $firstname;
		$account->lastname = $lastname;
		$account->create_time = date("Y-m-d H:i:s",time());
		$account->status = $status;
		try {
			$account->add();
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("accounts/add.php");
		}
		Lib::redirect("accounts/list.php");
	}
	
	public function updateAccount() {
		foreach($_POST as $key=>$val) {
			$$key = $val;
		}
		if($newpass == "on") {
			if($pass != $pass2) {
				Error::set("Lütfen şifreleri aynı giriniz");
				Lib::redirect("accounts/edit.php?id=".$id);
			}
		}
		$id = intval($_GET["id"]);
		$account = new Account($this->db);
		try {
			$account->load($id);
			$account->username = $username;
			$account->email = $email;
			$account->firstname = $firstname;
			$account->lastname = $lastname;
			$account->status = $status;
			if($newpass == "on") {
				$account->pass = sha1("Ec_SiGn".md5($pass).$pass);
			}
			$account->update(get_object_vars($account));
		} catch (Exception $exception) {
			Error::set($exception->getMessage());
			Lib::redirect("accounts/edit.php?id=".$id);
		}
		Lib::redirect("accounts/list.php");
	}
}

$accountRunner = new AccountRunner($db);
$accountRunner->run();