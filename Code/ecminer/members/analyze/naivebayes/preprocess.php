
<?php
// bu sayfada boþ verilerin doldurulmasý iþlemi gerçekleþtirilmektedir.

require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

$import_id = intval($_SESSION["NaiveBayes"]["ImportId"]);
$prep_check = intval($_SESSION["NaiveBayes"]["PrepCheck"]);
$prep_choice = intval($_SESSION["NaiveBayes"]["PrepChoice"]);

if($prep_check != 1) {
	Lib::redirect("members/analyze/naivebayes/chooseClass.php");
}

// import acc control
require_once(PATH."bean/Import.php");
$import = new Import($db);
if(!$import->load($import_id)) {
	Error::set("Lütfen geçerli bir veri seti seçiniz");
	Lib::redirect("members/analyze/naivebayes/default.php");
}
if($import->getAccountId() != $_SESSION["MemberId"]) {
	Error::set("Yeterli izniniz yok");
	Lib::redirect("members/analyze/naivebayes/default.php");
}

require_once(PATH."lib/importer/Importer.php");
$importer = Importer::createInstance($db,$import);
$dataSet = $importer->getDataSet();

if($prep_choice == 2) {		// boþ veriler ortalama deðerlerle doldurulacaklar
	$means = array();
	foreach($dataSet->attributes as $attribute) {
		if($attribute->type == Attribute::ATTRIBUTE_TYPE_CATEGORICAL) {
			continue;
		}
		$mean = 0;
		foreach($dataSet->samples as $sample) {
			$attributeValue = $sample->getAttributeValue($attribute->name);
			$mean += $attributeValue;
		}
		$mean /= count($dataSet->samples);
		$means[$attribute->name] = $mean;
	}
	foreach($dataSet->attributes as $attribute) {
		if($attribute->type == Attribute::ATTRIBUTE_TYPE_CATEGORICAL) {
			continue;
		}
		for($i=0;$i<count($dataSet->samples);$i++) {
			if(trim($dataSet->samples[$i]->getAttributeValue($attribute->name)) == "" || trim($dataSet->samples[$i]->getAttributeValue($attribute->name)) == "0") {
				$dataSet->samples[$i]->setAttributeValue($attribute->name,$means[$attribute->name]);
			}
		}
	}
	$_SESSION["NaiveBayes"]["DataSet"] = json_encode($dataSet);
}

Lib::redirect("members/analyze/naivebayes/preprocess2.php");

