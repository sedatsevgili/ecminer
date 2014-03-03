<?php
require_once (PATH."lib/core/Attribute.php");

class CategoricalAttribute extends Attribute {
	
	function __construct($name = "",$value = "") {
		$this->type = Attribute::ATTRIBUTE_TYPE_CATEGORICAL;
		$this->name = $name;
		$this->value = $value;
	}

	public function createWithValue($value) {
		return new CategoricalAttribute($this->name,$value);
	}
	
}