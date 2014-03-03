<?php
require_once (PATH."lib/core/Attribute.php");

class NumericalAttribute extends Attribute {
	
	function __construct($name,$value = 0) {
		$this->type = Attribute::ATTRIBUTE_TYPE_NUMERIC;
		$this->name = $name;
		$this->value = $value;
	}
	
}