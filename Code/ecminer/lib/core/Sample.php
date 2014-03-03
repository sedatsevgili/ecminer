<?php
require_once(PATH."lib/core/Attribute.php");

class Sample {
	
	public $attributes;
	
	public $id;	// bu analiz için değil, verilerin sayfada yönetilmesi için, checkbox'ın radio buttonun value'su gibi.
	
	/**
	 * 
	 * @var SampleClass
	 */
	public $class;
	
	function __construct($attributes = array(), $class = "") {
		foreach($attributes as $attribute) {
			$this->attributes[] = clone $attribute;
		}
		if($class != "") {
			$this->class = clone $class;
		} else {
			$this->class = "";
		}
	}
	
	public function loadFromStdClass($stdClass) {
		$this->id = $stdClass->id;
		$this->class = new SampleClass();
		$this->class->loadFromStdClass($stdClass->class);
		$this->attributes = array();
		foreach($stdClass->attributes as $stdAttribute) {
			$attribute = new Attribute();
			$attribute->loadFromStdClass($stdAttribute);
			$this->attributes[] = $attribute;
		}
	}
	
	public function selectAttributeForClass($newClass) {
		$this->class->name = $newClass;
		$attributes = array();
		foreach($this->attributes as $attribute) {
			if($attribute->name == $newClass->name) {
				$this->class->value = $attribute->value;
				continue;
			}
			$attributes[] = $attribute;
		}
		$this->attributes = $attributes;
	}
	
	public function fillWithAttributeValues($attributeValues,$seperator = ",") {
		if(!is_array($attributeValues)) {
			$attributeValues = explode($seperator,$attributeValues);
		}
		if(count($attributeValues) != count($this->attributes)) {
			ExceptionController::throwException("Core","ERROR_COUNT_OF_ATTRIBUTES_DONT_MATCH");
		}
		for($i=0;$i<count($attributeValues);$i++) {
			$this->attributes[$i]->value = $attributeValues[$i];
		}
	}
	
	public function setClassValue($classValue) {
		$this->class->value = $classValue;
	}
	
	public function setAttributeValue($attributeName,$attributeValue) {
		for($i=0;$i<count($this->attributes);$i++) {
			if($this->attributes[$i]->name == $attributeName) {
				$this->attributes[$i]->value = $attributeValue;
			}
		}
	}
	
	public function getAttributeValue($attributeName) {
		foreach($this->attributes as $attribute) {
			if($attribute->name == $attributeName) {
				return $attribute->value;
			}
		}
		return false;
		/*echo "<p>".$attributeName."</p>";
		print_r($this->attributes);
		exit();*/
		ExceptionController::throwException("Core","ERROR_ATTRIBUTE_NOT_FOUND");
	}
}