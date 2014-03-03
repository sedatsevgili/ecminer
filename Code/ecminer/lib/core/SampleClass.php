<?php
require_once (PATH."lib/core/Attribute.php");

class SampleClass extends Attribute {
	
	public $value;
	
	function __construct($type = "", $name = "", $value = "") {
		parent::__construct($type,$name);
		$this->value = $value;
	}
	
}