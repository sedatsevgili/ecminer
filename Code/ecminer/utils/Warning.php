<?php
class Warning {
	public static function set($message,$title = "") {
		$_SESSION["Warning"]["Message"] = $message;
		$_SESSION["Warning"]["Title"] = $title;
	}
	
	public static function unsetWarning() {
		unset($_SESSION["Warning"]);
	}
	
	public static function write() {
		if(!isset($_SESSION["Warning"])) {
			return false;
		}
		if($_SESSION["Warning"]["Title"]!="") {
			echo "<div class='WarningTab'>".$_SESSION["Warning"]["Title"]."</div>";
		}
		echo "<div class='WarningContent'>".$_SESSION["Warning"]["Message"]."</div>";
		self::unsetWarning();
	}
}