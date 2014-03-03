<?php
class Lib {
	
	public static function checkSessionSign() {
		if(!isset($_SESSION["ec-sign"]) || $_SESSION["ec-sign"]!=sha1(md5("ec_seed".$_SERVER["REMOTE_ADDR"])).sha1($_SERVER["REMOTE_ADDR"]."aoıewhy9235y")) {
			$_SESSION["ec-sign"] = sha1(md5("ec_seed".$_SERVER["REMOTE_ADDR"])).sha1($_SERVER["REMOTE_ADDR"]."aoıewhy9235y");
			self::redirect($_SERVER["REQUEST_URI"]);
		}
	}
	
	public static function isAdmin() {
		return isset($_SESSION["IsAdmin"]) && $_SESSION["IsAdmin"] == 1;
	}
	
	public static function isMember() {
		return isset($_SESSION["IsMember"]) && $_SESSION["IsMember"] == 1;
	}
	
	public static function redirect($destination) {
		$destination = PATH.$destination;
		if(headers_sent()) {
			echo "<script type='text/javascript'>window.location='".$destination."';</script>";
		} else {
			header("Location: ".$destination);
		}
		exit();
	}
	
	public static function redirectWithError($message = "",$title = "") {
		Error::set($message,$title);
		self::redirect(PATH."static/error.php");
	}
	
	public static function filterInput($input,$specialChars = true) {
		if(is_array($input)) {
			foreach($input as $key=>$val) {
				$input[$key] = filterInput($val,$specialChars);
			}
		} else {
			$input = DB::escape($input,$specialChars);
		}
		return $input;
	}
}