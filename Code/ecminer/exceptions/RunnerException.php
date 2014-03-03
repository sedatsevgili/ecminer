<?php
class RunnerException extends Exception {
	public static $ERROR_METHOD_NOT_FOUND = "İlgili metod bulunamadı";
	
	function __construct($message) {
		parent::__construct(self::$$message);
	}
}