<?php
require_once (PATH."lib/core/Attribute.php");
require_once (PATH."lib/core/Sample.php");
require_once (PATH."lib/core/SampleClass.php");

class DataSet {
	
	public $attributes;
	public $class;
	public $samples;
	
	function __construct($attributes = array(), $class = "", $samples = array()) {
		foreach($attributes as $attribute) {
			$this->attributes[] = clone $attribute;
		}
		$this->class = $class;
		$this->samples = $samples;
	}
	
	public function getAttributeValueRange($attributeName) {
		$attributeValueRange = array();
		foreach($this->samples as $sample) {
			$attributeValue = $sample->getAttributeValue($attributeName);
			if(!in_array($attributeValue,$attributeValueRange)) {
				$attributeValueRange[] = $attributeValue;
			}
		}
		return $attributeValueRange;
	}
	
	public function loadFromStdClass($stdClass) {
		$this->class = new SampleClass();
		$this->class->loadFromStdClass($stdClass->class);
		$this->attributes = array();
		foreach($stdClass->attributes as $stdAttribute) {
			$attribute = new Attribute();
			$attribute->loadFromStdClass($stdAttribute);
			$this->attributes[] = $attribute;
		}
		$this->samples = array();
		foreach($stdClass->samples as $stdSample) {
			$sample = new Sample();
			$sample->loadFromStdClass($stdSample);
			$this->samples[] = $sample;
		}
	}
	
	public function selectAttributeForClass($newClass) {
		$this->class->name = $newClass;
		$attributes = array();
		foreach($this->attributes as $attribute) {
			if($newClass->name == $attribute->name) {
				continue;
			}
			$attributes[] = $attribute;
		}
		$this->attributes = $attributes;
		foreach($this->samples as $sample) {
			$sample->selectAttributeForClass($newClass);
		}
	}
	
	public function getSelectBoxOfAttributes($id,$cssClass,$selectedValue = "",$extras = array(),$onchange = "") {
		$html = "<select name='".$id."' id='".$id."' class='".$cssClass."'";
		if($onchange != "") {
			$html .= " onchange=".$onchange;
		}
		$html .= ">";
		foreach($extras as $extra) {
			$html .= "<option value='".$extra["value"]."'>".$extra["name"]."</option>";
		}
		foreach($this->attributes as $attribute) {
			$html .= "<option value='".json_encode($attribute)."'>".$attribute->name."</option>";
		}
		$html .= "</select>";
		return $html;
	}
}