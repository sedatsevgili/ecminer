<?php

require_once PATH.'bean/Model.php';

class Classifier extends Model{
    //put your code here

    public $id;
    public $name;
    public $fileName;

    function __construct($db) {
        parent::__construct($db,"classifiers",array("name","fileName"));
    }

    public function add() {
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","classifiers","","name='".$this->name."'");
    	$this->_db->select("FOUND_ROWS() AS foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_CLASSIFIER_NAME_EXISTS");
    	}
    	if(!isset($_FILES["filename"])) {
    		ExceptionController::throwException("Model","ERROR_FILE_NOT_UPLOADED");
    	}
  	 	$this->_db->select("SQL_CALC_FOUND_ROWS id","classifiers","","fileName='".$this->fileName."'");
    	$this->_db->select("FOUND_ROWS() as foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_CLASSIFIER_FILE_NAME_EXISTS");
    	}
    	$this->addFile();
    	parent::add();
    }
    
    public function update($attributes = array()) {
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","classifiers","","name='".$this->name."' AND id<>".$this->id);
    	$this->_db->select("FOUND_ROWS() AS foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_CLASSIFIER_NAME_EXISTS");
    	}
    	if(isset($_FILES[$this->fileName])) {
	    	$this->_db->select("SQL_CALC_FOUND_ROWS id","classifiers","","fileName='".$this->fileName."' AND id<>".$this->id);
	    	$this->_db->select("FOUND_ROWS() as foundRows");
	    	if($this->_db->rows[0]["foundRows"] != "0") {
	    		ExceptionController::throwException("Model","ERROR_CLASSIFIER_FILE_NAME_EXISTS");
	    	}
	    	$this->addFile();
    	}
    	parent::update($attributes);
    }
    
    private function addFile() {
    	if($_FILES["filename"]["error"]>0) {
    		ExceptionController::throwException("Model","ERROR_FILE_NOT_UPLOADED");
    	}
    	if(!move_uploaded_file($_FILES["filename"]["tmp_name"],PATH."lib/classifier/".$this->fileName)) {
    		ExceptionController::throwException("Model","ERROR_FILE_NOT_UPLOADED");
    	}
    }
}
?>
