<?php
require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

class AnalyzeRunner {
	
	public $do;
	public $db;
	
	function __construct($db) {
		$this->db = $db;
		$this->do = $_GET["do"];
	}
	
	public function run() {
		if(!method_exists(get_class($this),$this->do)) {
			ExceptionController::throwException("Runner","ERROR_METHOD_NOT_FOUND");
		}
		$this->{$this->do}();
	}
	
	public function test() {
		$class = json_decode($_SESSION["DecisionTree"]["AnalyzeClass"]);
		$import_id = intval($_SESSION["DecisionTree"]["ImportId"]);
		$testChoice = intval($_SESSION["DecisionTree"]["TestChoice"]);
		
		// import acc control
		require_once(PATH."bean/Import.php");
		$import = new Import($this->db);
		if(!$import->load($import_id)) {
			Error::set("Lütfen geçerli bir veri seti seçiniz");
			Lib::redirect("members/analyze/decisiontree/default.php");
		}
		if($import->getAccountId() != $_SESSION["MemberId"]) {
			Error::set("Yeterli izniniz yok");
			Lib::redirect("members/analyze/decisiontree/default.php");
		}
		
		require_once(PATH."lib/importer/Importer.php");
		$importer = Importer::createInstance($this->db,$import);
		
		if(!empty($_SESSION["DecisionTree"]["DataSet"])) {
			require_once(PATH."lib/core/DataSet.php");
			$dataSet = new DataSet();
			$dataSet->loadFromStdClass(json_decode($_SESSION["DecisionTree"]["DataSet"]));
		} else {
			$dataSet = $importer->getDataSet();
		}
		
		$dataSet->selectAttributeForClass($class);
		
		if($testChoice == 2) {
			$samples = array();
			$testSamples = array();
			foreach($dataSet->samples as $sample) {
				if(in_array($sample->id,$_SESSION["DecisionTree"]["ModelSampleIds"])) {
					$samples[] = $sample;
				} else {
					$testSamples[] = $sample;
				}
			}
			$dataSet = new DataSet($dataSet->attributes,$dataSet->class,$samples);
			$testDataSet = new DataSet($dataSet->attributes,$dataSet->class,$testSamples);
		} else {
			$testSampleCount = intval($_POST["test_sample_count"]);
			$testSamples = array();
			for($i=0;$i<$testSampleCount;$i++) {
				$attributes = $dataSet->attributes;
				for($j=0;$j<count($attributes);$j++) {
					$attributes[$j]->value = $_POST["test_".md5($attributes[$j]->name)][$i];
				}
				$testSamples[] = new Sample($attributes,"");
			}
			$testDataSet = new DataSet($dataSet->attributes,$dataSet->class,$testSamples);
		}
		
		$_SESSION["DecisionTree"]["ModelDataSet"] = json_encode($dataSet);
		$_SESSION["DecisionTree"]["TestDataSet"] = json_encode($testDataSet);
		
		require_once(PATH."lib/classifier/DecisionTree.php");
		$classifier = new DecisionTree($dataSet);
		$classValues = array();
		$memStart = memory_get_usage(true);
		$memPeakStart = memory_get_peak_usage(true);
		$timeStart = microtime(true);
		foreach($testDataSet->samples as $testSample) {
			$classValues[] = $classifier->getClassOfSample($testSample);
		}
		$_SESSION["DecisionTree"]["MemoryUsage"] = memory_get_usage(true) - $memStart;
		$_SESSION["DecisionTree"]["MemoryPeakUsage"] = memory_get_peak_usage(true) - $memPeakStart;
		$_SESSION["DecisionTree"]["TotalMemoryUsage"] = memory_get_usage(true);
		$_SESSION["DecisionTree"]["TotalPeakMemoryUsage"] = memory_get_peak_usage(true);
		$_SESSION["DecisionTree"]["TimeUsage"] = microtime(true) - $timeStart;
		$_SESSION["DecisionTree"]["ClassValues"] = json_encode($classValues);
		Lib::redirect("members/analyze/decisiontree/results.php");
	}
	
	public function chooseDataSet() {
		foreach($_POST as $key=>$val) {
			$$key = intval($val);
		}
		if($import_id == 0) {
			Error::set("Lütfen bir veri seti seçiniz");
			Lib::redirect("members/analyze/decisiontree/default.php");
		}
		if($test_choice == 0) {
			Error::set("Lütfen bir test seçeneği seçiniz");
			Lib::redirect("members/analyze/decisiontree/default.php");
		}
		$_SESSION["DecisionTree"]["AnalyzeStep"] = 1;
		$_SESSION["DecisionTree"]["ImportId"] = $import_id;
		$_SESSION["DecisionTree"]["TestChoice"] = $test_choice;
		if($prep_check == 1) {
			$_SESSION["DecisionTree"]["PrepCheck"] = 1;
			$_SESSION["DecisionTree"]["PrepChoice"] = $prep_choice;
			$_SESSION["DecisionTree"]["PrepErrorChoice"] = $prep_error_choice;
			if($prep_choice == 2) {
				Lib::redirect("members/analyze/decisiontree/preprocess.php");
			} else if ($prep_error_choice == 2) {
				Lib::redirect("members/analyze/decisiontree/preprocess2.php");
			} else {
				Lib::redirect("members/analyze/decisiontree/chooseClass.php");
			}
		} else {
			$_SESSION["DecisionTree"]["PrepCheck"] = 0;
			Lib::redirect("members/analyze/decisiontree/chooseClass.php");
		}
	}
	
	public function chooseClass() {
		$_SESSION["DecisionTree"]["AnalyzeClass"] = $_POST["json_class"];
		
		if($_SESSION["DecisionTree"]["TestChoice"] == 2) {
			Lib::redirect("members/analyze/decisiontree/chooseModel.php");
		}
		Lib::redirect("members/analyze/decisiontree/createModel.php");
	}
	
	public function chooseModelData() {
		if($_SESSION["DecisionTree"]["TestChoice"] == 2) {
			$_SESSION["DecisionTree"]["ModelSampleIds"] = array();
			foreach($_POST["samples"] as $sampleArray) {
				$_SESSION["DecisionTree"]["ModelSampleIds"][] = $sampleArray[0];
			}
		}
		Lib::redirect("members/analyze/decisiontree/createModel.php");
	}
}

$analyzeRunner = new AnalyzeRunner($db);
$analyzeRunner->run();