<?php
class Attribute {
	
	const ATTRIBUTE_TYPE_NUMERIC = "numeric";
	const ATTRIBUTE_TYPE_CATEGORICAL = "categorical";
	
	public $type;
	public $name;
	public $value;
	
	function __construct($type = "", $name = "", $value = "") {
		$this->type = $type;
		$this->name = $name;
		$this->value = $value;
	}
	
	public function loadFromStdClass($stdClass) {
		$this->type = $stdClass->type;
		$this->name = $stdClass->name;
		$this->value = $stdClass->value;
	}
	
	public function createWithValue($value) {
		return new Attribute($this->type,$this->name,$this->value);
	}
	
}