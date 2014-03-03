<?php
require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");
Error::write();

$class = json_decode($_SESSION["DecisionTree"]["AnalyzeClass"]);
$importId = intval($_SESSION["DecisionTree"]["ImportId"]);
$testChoice = intval($_SESSION["DecisionTree"]["TestChoice"]);
$classValues = json_decode($_SESSION["DecisionTree"]["ClassValues"]);

// import acc control
require_once(PATH."bean/Import.php");
$import = new Import($db);
if(!$import->load($importId)) {
	Error::set("Lütfen geçerli bir veri seti seçiniz");
	Lib::redirect("members/analyze/decisiontree/default.php");
}
if($import->getAccountId() != $_SESSION["MemberId"]) {
	Error::set("Yeterli izniniz yok");
	Lib::redirect("members/analyze/decisiontree/default.php");
}

require_once(PATH."lib/importer/Importer.php");
$importer = Importer::createInstance($db,$import);

$testDataSet = json_decode($_SESSION["DecisionTree"]["TestDataSet"]);
for($i=0;$i<count($testDataSet->samples);$i++) {
	$testDataSet->samples[$i]->class->value = $classValues[$i];
}
$modelDataSet = json_decode($_SESSION["DecisionTree"]["ModelDataSet"]);

require_once(PATH."controller/DataSetTableController.php");

$tc = new DataSetTableController($db,"imports",$modelDataSet,"Model Veri Seti (".$importer->name.")","",PATH);
$tc->run();

echo "<p>&nbsp;</p>";

$tc = new DataSetTableController($db,"imports",$testDataSet,"Test Veri Seti Sonucu (".$importer->name.")","",PATH);
$tc->run();

echo "<p>total memory usage: ".$_SESSION["DecisionTree"]["TotalMemoryUsage"]."<br>total peak memory usage: ".$_SESSION["DecisionTree"]["TotalPeakMemoryUsage"]."<br>memory usage: ".$_SESSION["DecisionTree"]["MemoryUsage"]."<br>memory peak usage: ".$_SESSION["DecisionTree"]["MemoryPeakUsage"]."<br>time usage: ".$_SESSION["DecisionTree"]["TimeUsage"]."</p>";

unset($_SESSION["DecisionTree"]);

include_once(PATH."footers/member.php");
