<?php
class ControllerException extends Exception {
	public static $ERROR_MENU_XML_NOT_VALID = "Menu xml dosyası gecerli bir xml degil";
	
	function __construct($message) {
		parent::__construct(self::$message);
	}
}