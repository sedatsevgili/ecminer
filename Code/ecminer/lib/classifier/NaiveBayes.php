<?php
require_once (PATH."lib/classifier/Classifier.php");

class NaiveBayes extends Classifier {
	
	public static $TYPE_CATEGORICAL = "categorical";
	public static $TYPE_NUMERIC = "numeric";
	
	protected $classValueRange;
	protected $hitTable;		//hangi attributeların hangi classlara kaç adet sample üzerinden bağlı olduğunun bilgisini tutan tablo.
	protected $classValueCountRow;	// hangi class değerinden kaç tane var olduğunu tutar.
	
	function __construct($dataSet) {
		parent::__construct($dataSet);
		
		$this->classValueRange = array();
		$this->classValueCountRow = array();
		
		foreach($this->dataSet->samples as $sample) {
			if(!in_array($sample->class->value,$this->classValueRange)) {
				$this->classValueRange[] = $sample->class->value;
				$this->classValueCountRow[$sample->class->value] = 1;
			} else {
				$this->classValueCountRow[$sample->class->value]++;
			}
		}
		
		$this->setHitTable();
		
	}
	
	protected function setHitTable() {
		$this->hitTable = array();
		foreach($this->dataSet->samples as $sample) {
			foreach($sample->attributes as $attribute) {
				if(!array_key_exists($attribute->name.$attribute->value,$this->hitTable)) {
					$this->hitTable[$attribute->name.$attribute->value] = array("name"=>$attribute->name,"value"=>$attribute->value,"classRange"=>array());
					foreach($this->classValueRange as $classValue) {
						$this->hitTable[$attribute->name.$attribute->value]["classRange"][$classValue] = 0;
					}
				}
				foreach($this->classValueRange as $classValue) {
					if($sample->class->value == $classValue) {
						$this->hitTable[$attribute->name.$attribute->value]["classRange"][$classValue]++;
					}
				}
			}
		}
		$addLaplace = $this->laplaceControl();
		if($addLaplace) {
			foreach($this->hitTable as $attributeName=>$attribute) {
				foreach($attribute["classRange"] as $classValue=>$classHitCount) {
					$this->hitTable[$attribute["name"].$attribute["value"]]["classRange"][$classValue]++;
				}
			}
		}
	}
	
	protected function setHitTableWithNewSample(Sample $newSample) {
		foreach($newSample->attributes as $attribute) {
			if(!array_key_exists($attribute->name.$attribute->value,$this->hitTable)) {
				$this->hitTable[$attribute->name.$attribute->value] = array("name"=>$attribute->name,"value"=>$attribute->value,"classRange"=>array());
				foreach($this->classValueRange as $classValue) {
					$this->hitTable[$attribute->name.$attribute->value]["classRange"][$classValue] = 0;
				}
				foreach($this->hitTable as $attributeName=>$tableAttribute) {
					foreach($tableAttribute["classRange"] as $classValue=>$classHitCount) {
						$this->hitTable[$tableAttribute["name"].$tableAttribute["value"]]["classRange"][$classValue]++;
					}
				}
			}
		}
	}
	
	protected function laplaceControl() {
		foreach($this->hitTable as $attribute) {
			foreach($attribute["classRange"] as $classHitCount) {
				if($classHitCount == 0) {
					$this->addLaplace = true;
					return true;
				}
			}
		}
		$this->addLaplace = false;
		return false;
	}
	
	public function estimateClassOfSample(Sample $newSample, $returnArray = false) {
			
		$this->setHitTableWithNewSample($newSample);

		$estimatedClassValue = "";
		$classProbability = 0;
		if($returnArray) {
			$resultArray = array();
			$probTotal = 0;
		}
		foreach($this->classValueRange as $classValue) {
			$probability = $this->getProbabilityOfClass($classValue);
			foreach($newSample->attributes as $attribute) {
				$probability *= $this->getProbabilityOfAttributeWithClassValue($attribute,$classValue);
			}
			if(!$returnArray && $probability > $classProbability) {
				$classProbability = $probability;
				$estimatedClassValue = $classValue;
				//echo "prob: ".$probability.", class: ".$classValue."<br>";
			}
			if($returnArray) {
				$estimation = new stdClass();
				$estimation->classValue = $classValue;
				$estimation->probability = $probability;
				$probTotal += $probability;
				$i=0;
				while($i<count($resultArray) && $resultArray[$i]->probability>$probability) {
					$i++;
				}
				for($j=count($resultArray);$j>$i;$j--) {
					$resultArray[$j] = $resultArray[$j-1];
				}
				$resultArray[$i] = $estimation;
			}
		}
		if($returnArray) {
			for($i=0;$i<count($resultArray);$i++) {
				$resultArray[$i]->probability *= (100/$probTotal);
			}
			return $resultArray;
		} else {
			$estimationResult = new stdClass();
			$estimationResult->classValue = $estimatedClassValue;
			$estimationResult->probability = $classProbability;
			return $estimationResult;
		}
	}
	
	protected function getProbabilityOfClass($classValue) {
		$classHit = 0;
		foreach($this->dataSet->samples as $sample) {
			if($sample->class->value == $classValue) {
				$classHit++;
			}
		}
		return floatval($classHit / count($this->dataSet->samples));
	}
	
	protected function getProbabilityOfAttributeWithClassValue($attribute,$classValue) {
		if($attribute->type == Attribute::ATTRIBUTE_TYPE_CATEGORICAL) {
			if(!array_key_exists($attribute->name.$attribute->value,$this->hitTable)) {
				return 0;	//missed laplace
			}
			$hit = $this->hitTable[$attribute->name.$attribute->value]["classRange"][$classValue];
			$classHit = $hit;
			foreach($this->hitTable as $tableAttribute) {
				if($tableAttribute["name"] != $attribute->name || $tableAttribute["value"] == $attribute->value) {
					continue;
				}
				$classHit += $tableAttribute["classRange"][$classValue];
			}
			if($classHit == 0) {
				return 0;
			}
			return floatval($hit / $classHit);
		} else {
			$mean = $this->getMeanOfAttributeWithClassValue($attribute,$classValue);
			$deviation = $this->getDeviationOfAttributeWithClass($attribute,$mean,$classValue);
			if($deviation == 0) {
				return 1;
			}
			return (1/sqrt(2*M_PI)*$deviation)*exp(-( ($attribute->value-$mean)*($attribute->value-$mean) )/(2*$deviation*$deviation));
			//HÖH!
		}
	}
	
	protected function getMeanOfAttributeWithClassValue($attribute,$classValue) {
		$total = 0;
		$classHit = 0;
		foreach($this->dataSet->samples as $sample) {
			if($sample->class->value != $classValue) {
				continue;
			}
			$classHit++;
			foreach($sample->attributes as $sampleAttribute) {
				if($sampleAttribute->name != $attribute->name) {
					continue;
				}
				$total += $sampleAttribute->value;
			}
		}
		return floatval($total/$classHit);
	}
	
	protected function getDeviationOfAttributeWithClass($attribute,$mean,$classValue) {
		$total = 0;
		$classHit = 0;
		foreach($this->dataSet->samples as $sample) {
			if($sample->class->value != $classValue) {
				continue;
			}
			$classHit++;
			foreach($sample->attributes as $sampleAttribute) {
				if($sampleAttribute->name != $attribute->name) {
					continue;
				}
				$total += ($sampleAttribute->value-$mean)*($sampleAttribute->value-$mean);
			}
		}
		return sqrt(floatval($total/$classHit));
	}

}