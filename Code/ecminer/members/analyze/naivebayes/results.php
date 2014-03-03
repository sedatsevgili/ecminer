<?php
require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");
Error::write();

$class = json_decode($_SESSION["NaiveBayes"]["AnalyzeClass"]);
$importId = intval($_SESSION["NaiveBayes"]["ImportId"]);
$testChoice = intval($_SESSION["NaiveBayes"]["TestChoice"]);
$estimations = json_decode($_SESSION["NaiveBayes"]["Estimations"]);

// import acc control
require_once(PATH."bean/Import.php");
$import = new Import($db);
if(!$import->load($importId)) {
	Error::set("Lütfen geçerli bir veri seti seçiniz");
	Lib::redirect("members/analyze/naivebayes/default.php");
}
if($import->getAccountId() != $_SESSION["MemberId"]) {
	Error::set("Yeterli izniniz yok");
	Lib::redirect("members/analyze/naivebayes/default.php");
}

require_once(PATH."lib/importer/Importer.php");
$importer = Importer::createInstance($db,$import);

$testDataSet = json_decode($_SESSION["NaiveBayes"]["TestDataSet"]);
for($i=0;$i<count($testDataSet->samples);$i++) {
	$classValue = "";
	for($j=0;$j<count($estimations[$i]);$j++) {
		$classValue .= "%".sprintf("%01.2f",$estimations[$i][$j]->probability).": ".$estimations[$i][$j]->classValue."<br>";
	}
	$testDataSet->samples[$i]->class->value = $classValue;
}

$modelDataSet =json_decode($_SESSION["NaiveBayes"]["ModelDataSet"]);
require_once(PATH."controller/DataSetTableController.php");
$tc = new DataSetTableController($db,"imports",$modelDataSet,"Model Veri Seti","",PATH);
$tc->run();

echo "<p>&nbsp;</p>";

$tc = new DataSetTableController($db,"imports",$testDataSet,"Test Veri Seti Sonucu (".$importer->name.")","",PATH);
$tc->run();

echo "<p>memory usage: ".$_SESSION["NaiveBayes"]["MemoryUsage"]."<br>peak memory usage: ".$_SESSION["NaiveBayes"]["PeakMemoryUsage"]."<br>total memory usage: ".$_SESSION["NaiveBayes"]["TotalMemoryUsage"]."<br>total peak memory usage: ".$_SESSION["NaiveBayes"]["TotalPeakMemoryUsage"]."<br>time usage: ".$_SESSION["NaiveBayes"]["TimeUsage"]."</p>";

unset($_SESSION["NaiveBayes"]);

include_once(PATH."footers/member.php");
