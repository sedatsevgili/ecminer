<?php
require_once (PATH."lib/classifier/Classifier.php");

class DecisionTree extends Classifier {
	
	public static $TYPE_CATEGORICAL = "categorical";
	public static $TYPE_NUMERIC = "numeric";
	
	public $entropy;
	public $sampleCount;
	public $tree;
	
	function __construct($dataSet) {
		parent::__construct($dataSet);
		
		// calculating entropy
		$classValues = array();
		foreach($this->dataSet->samples as $sample) {
			if(!array_key_exists($sample->class->value,$classValues)) {
				$classValues[$sample->class->value] = 0;
			}
			$classValues[$sample->class->value]++;
		}
		$this->sampleCount = count($this->dataSet->samples);
		$this->entropy = 0;
		foreach($classValues as $classValue=>$count) {
			$this->entropy += ($count/$this->sampleCount*log($count/$this->sampleCount,2));
		}
		$this->entropy = 0-$this->entropy;
		
		$this->setTree();
	}
	
	protected function getGains($dataSet) {
		$gains = array();
		foreach($dataSet->attributes as $attribute) {
			$attributeValues = array();
			foreach($dataSet->samples as $sample) {
				$attributeValue = $sample->getAttributeValue($attribute->name);
				if(!array_key_exists($attributeValue,$attributeValues)) {
					$attributeValues[$attributeValue] = array("count"=>0,"entropy"=>0,"classValues"=>array());
				}
				$attributeValues[$attributeValue]["count"]++;
				$classValue = $sample->class->value;
				if(!array_key_exists($classValue,$attributeValues[$attributeValue]["classValues"])) {
					$attributeValues[$attributeValue]["classValues"][$classValue] = 0;
				}
				$attributeValues[$attributeValue]["classValues"][$classValue]++;
			}
			foreach($attributeValues as $attributeValue=>$values) {
				$entropy = 0;
				foreach($values["classValues"] as $classValue=>$classCount) {
					$entropy += ($classCount/$values["count"]*log($classCount/$values["count"],2));
				}
				$attributeValues[$attributeValue]["entropy"] = 0-$entropy;
			}
			$gain = 0;
			foreach($attributeValues as $attributeValue=>$values) {
				$gain += $values["count"]/$this->sampleCount*$values["entropy"];
			}
			$gains[$attribute->name] = $this->entropy - $gain;
		}
		return $gains;
	}
	
	protected function getNodeAttribute($dataSet) {
		$gains = $this->getGains($dataSet);
		$biggestGainValue = 0;
		$biggestGainAttributeName = "";
		foreach($gains as $attributeName=>$value) {
			if($value>$biggestGainValue) {
				$biggestGainValue = $value;
				$biggestGainAttributeName = $attributeName;
			}
		}
		$result = new stdClass();
		$result->attributeName = $biggestGainAttributeName;
		$result->gainValue = $biggestGainValue;
		return $result;
	}

	protected function getSubDataSet($dataSet,$attributeName,$attributeValue) {
		$subSamples = array();
		foreach($dataSet->samples as $sample) {
			if($sample->getAttributeValue($attributeName) == $attributeValue) {
				$subAttributes = array();
				foreach($sample->attributes as $attribute) {
					if($attribute->name != $attributeName) {
						$subAttributes[] = clone $attribute;
					}
				}
				$subSamples[] = new Sample($subAttributes,$sample->class);
			}
		}
		$subAttributes = array();
		foreach($dataSet->attributes as $attribute) {
			if($attribute->name != $attributeName) {
				$subAttributes[] = clone $attribute;
			}
		}
		return new DataSet($subAttributes,$dataSet->class,$subSamples);
	}
	
	protected function getNode($dataSet,$rootNodeAttributeName) {
		$node = new stdClass();
		if(count($dataSet->attributes)<=0) {
			$node->isClass = true;
			return $node;
		}
		$node->isClass = false;
		$nodeResult = $this->getNodeAttribute($dataSet);
		$node->attributeName = $nodeResult->attributeName;
		$attributeValueRange = $dataSet->getAttributeValueRange($node->attributeName,true);
		foreach($attributeValueRange as $attributeValue) {
			$subDataSet = $this->getSubDataSet($dataSet,$node->attributeName,$attributeValue);
			$node->$attributeValue = $this->getNode($subDataSet,$node->attributeName);
		}
		return $node;
	}
	
	public function setTree() {
		$this->tree = new stdClass();
		$nodeResult = $this->getNodeAttribute($this->dataSet);
		$this->tree->attributeName = $nodeResult->attributeName;
		$attributeValueRange = $this->dataSet->getAttributeValueRange($this->tree->attributeName);
		foreach($attributeValueRange  as $attributeValue) {
			$subDataSet = $this->getSubDataSet($this->dataSet,$this->tree->attributeName,$attributeValue);
			$this->tree->$attributeValue = $this->getNode($subDataSet,$this->tree->attributeName);
		}
	}
	
	protected function getEmptySampleAttribute(Sample $newSample,$attribute) {
		if($attribute->type == Attribute::ATTRIBUTE_TYPE_CATEGORICAL) {
			$attributeName = $attribute->name;
			$biggestSimilarity = 0;
			$mostSimilarAttributeValue = "";
			foreach($this->dataSet->samples as $sample) {
				$similarity = 0;
				for($i=0;$i<count($sample->attributes);$i++) {
					if($sample->attributes[$i]->value == $newSample->attributes[$i]->value) {
						$similarity++;
					}
				}
				if($similarity>$biggestSimilarity) {
					$biggestSimilarity = $similarity;
					$mostSimilarAttributeValue = $sample->getAttributeValue($attributeName);
				}
			}
			return $mostSimilarAttributeValue;
		}
		
		// type of attribute is numerical
		$attributeName = $attribute->name;
		$biggestSimilarity = PHP_INT_MAX;
		$mostSimilarAttributeValue = "";
		foreach($this->dataSet->samples as $sample) {
			$attributeValue = $sample->getAttributeValue($attributeName);
			$similarity = abs($attributeValue-$attribute->value);
			if($similarity<$biggestSimilarity) {
				$biggestSimilarity = $similarity;
				$mostSimilarAttributeValue = $attributeValue;
			}
		}
		return $mostSimilarAttributeValue;
	}
	
	public function getClassOfSample(Sample $newSample) {
		for($i=0;$i<count($newSample->attributes);$i++) {
			$attribute = $newSample->attributes[$i];
			$attributeValueRange = $this->dataSet->getAttributeValueRange($attribute->name);
			if(!in_array($attribute->value,$attributeValueRange)) {
				$newSample->attributes[$i]->value = $this->getEmptySampleAttribute($newSample,$attribute);
			}
		}
		return $this->searchClassOfSample($newSample,$this->dataSet,$this->tree);
	}
	
	protected function searchClassOfSample(Sample $newSample,DataSet $dataSet,$node) {
		$sampleAttributeValue = $newSample->getAttributeValue($node->attributeName);
		$subDataSet = $this->getSubDataSet($dataSet,$node->attributeName,$sampleAttributeValue);
		if(count($subDataSet->samples) <= 1 || count($subDataSet->attributes)<=1) {
			return $subDataSet->samples[0]->class->value;
		}
		return $this->searchClassOfSample($newSample,$subDataSet,$node->$sampleAttributeValue);
	}
	
}