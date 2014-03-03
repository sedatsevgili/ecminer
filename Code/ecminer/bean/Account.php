<?php

require_once PATH."bean/Model.php";

class Account extends Model{
    //put your code here

    public $id;
    public $username;
    public $email;
    public $firstname;
    public $lastname;
    public $pass;		//sha1("Ec_SiGn".md5($pass).$pass);
    public $create_time;
    public $status;

    function __construct($db) {
        parent::__construct($db,"accounts",array("username","email","firstname","lastname","pass","create_time","status"));
    }
    
    public function add() {
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","accounts","","username='".$this->username."'");
    	$this->_db->select("FOUND_ROWS() as foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_USERNAME_EXISTS");
    	}
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","accounts","","email='".$this->email."'");
    	$this->_db->select("FOUND_ROWS() AS foundRows");
    	if($this->_db->rows[0]["foundRows"] != "0") {
    		ExceptionController::throwException("Model","ERROR_EMAIL_EXISTS");
    	}
    	parent::add();
    }
    
    public function update($attributes = array()) {
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","accounts","","username='".$this->username."' AND id<>".$this->id);
    	$this->_db->select("FOUND_ROWS() AS foundRows");
    	if($this->_db->rows[0]["foundRows"]!="0") {
    		ExceptionController::throwException("Model","ERROR_USERNAME_EXISTS");
    	}
    	$this->_db->select("SQL_CALC_FOUND_ROWS id","accounts","","email='".$this->email."' AND id<>".$this->id);
    	$this->_db->select("FOUND_ROWS() AS foundRows");
    	if($this->_db->rows[0]["foundRows"]!="0") {
    		ExceptionController::throwException("Model","ERROR_EMAIL_EXISTS");
    	}
    	parent::update($attributes);
    }
    
    public function loadFromLogin() {
    	$this->_db->select("id","accounts","","username='".$this->username."' AND pass='".sha1("Ec_SiGn".md5($this->pass).$this->pass)."' and status=1");
    	if($this->_db->rowCount == 0) {
    		ExceptionController::throwException("Model","ERROR_ACCOUNT_NOT_LOADED");
    	}
    	$this->id = $this->_db->rows[0]["id"];
    }
}
?>
