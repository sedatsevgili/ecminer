<?php

require_once PATH.'bean/Model.php';

class Site extends Model{
    //put your code here

    public $id;
    public $account_id;
    public $create_time;
    public $status;
    public $url;

    function __construct($db) {
        parent::__construct($db,"sites",array("account_id","create_time","status","url"));
    }
    
    public function add() {
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","sites","","url='".$this->url."'");
    	$this->_db->select("FOUND_ROWS() as foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_SITE_ADDRESS_EXISTS");
    	}
    	require_once(PATH."bean/Account.php");
    	$account = new Account($this->_db);
    	if(!$account->load($this->account_id)) {
    		ExceptionController::throwException("Model","ERROR_ACCOUNT_DOESNT_EXIST");
    	}
    	parent::add();
    }
    
    public function update($attributes = array()) {
   		$this->_db->select("SQL_CALC_FOUND_ROWS id","sites","","url='".$this->url."' AND id<>".$this->id);
    	$this->_db->select("FOUND_ROWS() as foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_SITE_ADDRESS_EXISTS");
    	}
    	require_once(PATH."bean/Account.php");
    	$account = new Account($this->_db);
    	if(!$account->load($this->account_id)) {
    		ExceptionController::throwException("Model","ERROR_ACCOUNT_DOESNT_EXIST");
    	}
    	parent::update($attributes);
    }
}
?>
