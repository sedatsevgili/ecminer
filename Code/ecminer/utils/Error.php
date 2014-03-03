<?php
class Error {
	
	
	public static function set($message,$title = "") {
		$_SESSION["Error"]["Message"] = $message;
		$_SESSION["Error"]["Title"] = $title;
	}
	
	public static function unsetError() {
		unset($_SESSION["Error"]);
	}
	
	public static function write() {
		if(!isset($_SESSION["Error"])) {
			return false;
		}
		if($_SESSION["Error"]["Title"]!="") {
			echo "<div class='ErrorTab'>".$_SESSION["Error"]["Title"]."</div>";
		}
		echo "<div class='ErrorContent'>".$_SESSION["Error"]["Message"]."</div>";
		self::unsetError();
	}
}