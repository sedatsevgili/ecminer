<?php
require_once PATH.'bean/Model.php';

class User extends Model{
    //put your code here
    public $id;
    public $username;
    public $pass;
    public $create_time;

    function __construct($db) {
        parent::__construct($db,"users",array("username","pass","create_time"));
    }
    
    public function loadFromLogin() {
    	$this->_db->select("id","users","","username='".$this->username."' AND pass='".sha1(md5("Ec+SiG".$this->password."n"))."'");
    	if($this->_db->rowCount == 0) {
    		ExceptionController::throwException("Model","ERROR_USER_NOT_LOADED");
    	}
    	$this->id = $this->_db->getLastId();
    }
    
    public function add() {
    	$query = "INSERT INTO ".$this->_tableName." (username,pass,create_time)
    	VALUES('".$this->username."','".sha1(md5("Ec+SiG".$this->password."n"))."',NOW())";
    	$this->_db->query($query);
    	$this->id = $this->_db->getLastId();
    }
}
?>
