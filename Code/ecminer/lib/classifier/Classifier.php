<?php
require_once (PATH."lib/core/DataSet.php");

abstract class Classifier {
	
	public $dataSet;
	public $modelStartIndex;
	public $modelLength;
	
	public $modelDataSet;
	
	function __construct($dataSet) {
		if(!($dataSet instanceof DataSet)) {
			ExceptionController::throwException("Core","ERROR_DATASET_OBJECT_IS_NOT_VALID");
		}
		$this->dataSet = $dataSet;
	}
	
	
	
}