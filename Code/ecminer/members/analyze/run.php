<?php
require_once("../../utils/config.php");
require_once (PATH."utils/Runner.php");

class AnalyzeRunner extends Runner {
	function __construct($db) {
		parent::__construct($db,"Classifier");
	}
	
	public function chooseClassifier() {
		$classifier = new Classifier($this->db);
		if(!$classifier->load(intval($_POST["classifier_id"]))) {
			Error::set("Lütfen geçerli bir sınıflandırıcı seçiniz");
			Lib::redirect("members/analyze/default.php");
		}
		Lib::redirect("members/analyze/".str_replace(" ","",strtolower($classifier->name))."/default.php");
	}
}

$analyzeRunner = new AnalyzeRunner($db);
$analyzeRunner->run();