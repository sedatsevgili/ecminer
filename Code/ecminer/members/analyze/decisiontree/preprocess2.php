<?php
// bu sayfada hatalı verilerin doldurulması işlemi gerçekleştirilmektedir.

require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

$import_id = intval($_SESSION["DecisionTree"]["ImportId"]);
$prep_check = intval($_SESSION["DecisionTree"]["PrepCheck"]);
$prep_error_choice = intval($_SESSION["DecisionTree"]["PrepErrorChoice"]);

if($prep_check != 1) {
	Lib::redirect("members/analyze/decisiontree/chooseClass.php");
}

if(!empty($_SESSION["DecisionTree"]["DataSet"])) {
        require_once(PATH."lib/core/DataSet.php");
        $dataSet = new DataSet();
        $dataSet->loadFromStdClass(json_decode($_SESSION["DecisionTree"]["DataSet"]));
	require_once (PATH."lib/core/Attribute.php");
	require_once (PATH."lib/core/Sample.php");
	require_once (PATH."lib/core/SampleClass.php");
} else {
	// import acc control 
	require_once(PATH."bean/Import.php");
	$import = new Import($db);
	if(!$import->load($import_id)) {
		Error::set("Lütfen geçerli bir veri seti seçiniz");
		Lib::redirect("members/analyze/decisiontree/default.php");
	}
	if($import->getAccountId() != $_SESSION["MemberId"]) {
		Error::set("Yeterli izniniz yok");
		Lib::redirect("members/analyze/decisiontree/default.php");
	}
	
	require_once(PATH."lib/importer/Importer.php");
	$importer = Importer::createInstance($db,$import);
	$dataSet = $importer->getDataSet();
}

if($prep_error_choice == 2) {
	$attributeValues = array();
	$means = array();
	foreach($dataSet->attributes as $attribute) {
		$values = array();
		$mean = 0;
		if($attribute->type == Attribute::ATTRIBUTE_TYPE_CATEGORICAL) {
			continue;
		}
		foreach($dataSet->samples as $sample) {
			$attributeValue = $sample->getAttributeValue($attribute->name);
			$values[] = $attributeValue;
			$mean += $attributeValue;
		}
		$mean /= count($dataSet->samples);
		sort($values);
		$attributeValues[$attribute->name] = $values;
		$means[$attribute->name] = $mean;
	}
	
	foreach($attributeValues as $attributeName=>$values) {
		$q1 = $values[intval(count($values)/4)];
		$q3 = $values[intval(count($values)*3/4)];
		for($i=0;$i<count($dataSet->samples);$i++) {
			$attributeValue = $dataSet->samples[$i]->getAttributeValue($attributeName);
			if($attributeValue < $q1 || $attributeValue>$q3) {
				$dataSet->samples[$i]->setAttributeValue($attributeName,$means[$attributeName]);
			}
		}
	}
}

Lib::redirect("members/analyze/decisiontree/chooseClass.php");