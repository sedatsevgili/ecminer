<?php

require_once PATH.'bean/Model.php';

class Import extends Model{
    //put your code here

    public $id;
    public $site_id;
    public $import_type_id;
    public $import_field_id;
    public $create_time;
    public $ip_address;
    public $file_size;
    public $extension;
    
    private $account_id;

    function __construct($db) {
        parent::__construct($db,"imports",array("site_id","import_type_id","import_field_id","create_time","ip_address","file_size","extension"));
        $this->account_id = 0;
    }
    
    public function getAccountId() {
    	if($this->accont_id == 0) {
    		$this->_db->select("account_id","sites","","id=".$this->site_id);
    		$this->account_id = intval($this->_db->rows[0]["account_id"]);
    	}
    	return $this->account_id;
    }
    
    public function add() {
    	parent::add();
    	$this->addFile();
    	$this->update($this->loadAttributes(),false);
    	require_once(PATH."lib/importer/Importer.php");
    	$importer = Importer::createInstance($this->_db,$this);
    	$importer->run();
    }
    
    protected function loadAttributes() {
    	$attributes = array();
    	$attributes["site_id"] = $this->site_id;
    	$attributes["import_type_id"] = $this->import_type_id;
    	$attributes["import_field_id"] = $this->import_field_id;
    	$attributes["create_time"] = $this->create_time;
    	$attributes["ip_address"] = $this->ip_address;
    	$attributes["file_size"] = $this->file_size;
    	$attributes["extension"] = $this->extension;
    	return $attributes;
    }
    
    public function addFile() {
    	if(!isset($_FILES["filename"]) || $_FILES["filename"]["error"]>0 || !move_uploaded_file($_FILES["filename"]["tmp_name"],FILEPATH."temp/".$_FILES["filename"]["name"])) {
    		ExceptionController::throwException("Model","ERROR_IMPORT_FILE_NOT_UPLOADED");
		}
		$fileName = $_FILES["filename"]["name"];
		$dotPos = strrpos($fileName,".");
		$extension = substr($fileName,$dotPos);
		if(!copy(FILEPATH."temp/".$fileName,FILEPATH."imports/".$this->id.$extension) || !unlink(FILEPATH."temp/".$fileName)) {
			ExceptionController::throwException("Model","ERROR_IMPORT_FILE_NOT_COPIED");
		}
		$this->file_size = filesize(PATH."imports/".$this->id.$extension);
		$this->extension = $extension;
    }
    
    public function update($attributes = array(),$fileUpload=true) {
    	if($fileUpload && isset($_FILES["filename"]) && !empty($_FILES["filename"]["name"])) {
    		$this->addFile();
    	}
    	parent::update(empty($attributes) ? $this->loadAttributes() : $attributes);
    }
    
    public function delete() {
    	if(!unlink(PATH."imports/".$this->id.$this->extension)) {
    		file_put_contents(PATH."debug.txt",PATH."imports/".$this->id.$this->extension);
    		ExceptionController::throwException("Model","ERROR_IMPORT_FILE_NOT_DELETED");
    	}
    	parent::delete();
    }
}
?>
