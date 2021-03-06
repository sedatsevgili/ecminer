<?php
// bu sayfada hatal� verilerin doldurulmas� i�lemi ger�ekle�tirilmektedir.

require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

$import_id = intval($_SESSION["NaiveBayes"]["ImportId"]);
$prep_check = intval($_SESSION["NaiveBayes"]["PrepCheck"]);
$prep_error_choice = intval($_SESSION["NaiveBayes"]["PrepErrorChoice"]);

if($prep_check != 1) {
	Lib::redirect("members/analyze/naivebayes/chooseClass.php");
}

if(!empty($_SESSION["NaiveBayes"]["DataSet"])) {
	$dataSet = json_decode($_SESSION["NaiveBayes"]["DataSet"]);
	require_once (PATH."lib/core/Attribute.php");
	require_once (PATH."lib/core/Sample.php");
	require_once (PATH."lib/core/SampleClass.php");
} else {
	// import acc control 
	require_once(PATH."bean/Import.php");
	$import = new Import($db);
	if(!$import->load($import_id)) {
		Error::set("L�tfen ge�erli bir veri seti se�iniz");
		Lib::redirect("members/analyze/naivebayes/default.php");
	}
	if($import->getAccountId() != $_SESSION["MemberId"]) {
		Error::set("Yeterli izniniz yok");
		Lib::redirect("members/analyze/naivebayes/default.php");
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
		$attributeValues[$attribute->name] = clone $values;
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

Lib::redirect("members/analyze/naivebayes/chooseClass.php");
