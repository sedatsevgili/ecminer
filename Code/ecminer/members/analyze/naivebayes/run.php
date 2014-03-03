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
		$class = json_decode($_SESSION["NaiveBayes"]["AnalyzeClass"]);
		$import_id = intval($_SESSION["NaiveBayes"]["ImportId"]);
		$testChoice = intval($_SESSION["NaiveBayes"]["TestChoice"]);
		
		// import acc control
		require_once(PATH."bean/Import.php");
		$import = new Import($this->db);
		if(!$import->load($import_id)) {
			Error::set("Lütfen geçerli bir veri seti seçiniz");
			Lib::redirect("members/analyze/naivebayes/default.php");
		}
		if($import->getAccountId() != $_SESSION["MemberId"]) {
			Error::set("Yeterli izniniz yok");
			Lib::redirect("members/analyze/naivebayes/default.php");
		}
		
		require_once(PATH."lib/importer/Importer.php");
		$importer = Importer::createInstance($this->db,$import);
		
		if(!empty($_SESSION["NaiveBayes"]["DataSet"])) {
			require_once(PATH."lib/core/DataSet.php");
			$dataSet = new DataSet();
			$dataSet->loadFromStdClass(json_decode($_SESSION["NaiveBayes"]["DataSet"]));
		} else {
			$dataSet = $importer->getDataSet();
		}
		
		$dataSet->selectAttributeForClass($class);
		
		if($testChoice == 2) {
			$samples = array();
			$testSamples = array();
			foreach($dataSet->samples as $sample) {
				if(in_array($sample->id,$_SESSION["NaiveBayes"]["ModelSampleIds"])) {
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
		
		$_SESSION["NaiveBayes"]["ModelDataSet"] = json_encode($dataSet);
		$_SESSION["NaiveBayes"]["TestDataSet"] = json_encode($testDataSet);
		
		require_once(PATH."lib/classifier/NaiveBayes.php");
		$classifier = new NaiveBayes($dataSet);
		$estimations = array();
		$memStart = memory_get_usage(true);
		$memPeakStart = memory_get_peak_usage(true);
		$timeStart = microtime(true);
		foreach($testDataSet->samples as $testSample) {
			$estimations[] = $classifier->estimateClassOfSample($testSample,true);
		}
		$_SESSION["NaiveBayes"]["MemoryUsage"] = memory_get_usage(true)-$memStart;
		$_SESSION["NaiveBayes"]["PeakMemoryUsage"] = memory_get_peak_usage(true) - $memPeakStart;
		$_SESSION["NaiveBayes"]["TotalMemoryUsage"] = memory_get_usage(true);
		$_SESSION["NaiveBayes"]["TotalPeakMemoryUsage"] = memory_get_peak_usage(true);
		$_SESSION["NaiveBayes"]["TimeUsage"] = microtime(true) - $timeStart;
		$_SESSION["NaiveBayes"]["Estimations"] = json_encode($estimations);
		Lib::redirect("members/analyze/naivebayes/results.php");
	}
	
	public function chooseDataSet() {
		foreach($_POST as $key=>$val) {
			$$key = intval($val);
		}
		if($import_id == 0) {
			Error::set("Lütfen bir veri seti seçiniz");
			Lib::redirect("members/analyze/naivebayes/default.php");
		}
		if($test_choice == 0) {
			Error::set("Lütfen bir test seçeneği seçiniz");
			Lib::redirect("members/analyze/naivebayes/default.php");
		}
		$_SESSION["NaiveBayes"]["AnalyzeStep"] = 1;
		$_SESSION["NaiveBayes"]["ImportId"] = $import_id;
		$_SESSION["NaiveBayes"]["TestChoice"] = $test_choice;
		if($prep_check == 1) {
			$_SESSION["NaiveBayes"]["PrepCheck"] = 1;
			$_SESSION["NaiveBayes"]["PrepChoice"] = $prep_choice;
			$_SESSION["NaiveBayes"]["PrepErrorChoice"] = $prep_error_choice;
			if($prep_choice == 2) {
				Lib::redirect("members/analyze/naivebayes/preprocess.php");
			} else if ($prep_error_choice == 2) {
				Lib::redirect("members/analyze/naivebayes/preprocess2.php");
			} else {
				Lib::redirect("members/analyze/naivebayes/chooseClass.php");
			}
		} else {
			$_SESSION["NaiveBayes"]["PrepCheck"] = 0;
			Lib::redirect("members/analyze/naivebayes/chooseClass.php");
		}
	}
	
	public function chooseClass() {
		$_SESSION["NaiveBayes"]["AnalyzeClass"] = $_POST["json_class"];
		
		if($_SESSION["NaiveBayes"]["TestChoice"] == 2) {
			Lib::redirect("members/analyze/naivebayes/chooseModel.php");
		}
		Lib::redirect("members/analyze/naivebayes/createModel.php");
	}
	
	public function chooseModelData() {
		if($_SESSION["NaiveBayes"]["TestChoice"] == 2) {
			$_SESSION["NaiveBayes"]["ModelSampleIds"] = array();
			foreach($_POST["samples"] as $sampleArray) {
				$_SESSION["NaiveBayes"]["ModelSampleIds"][] = $sampleArray[0];
			}
		}
		Lib::redirect("members/analyze/naivebayes/createModel.php");
	}
}

$analyzeRunner = new AnalyzeRunner($db);
$analyzeRunner->run();