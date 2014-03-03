<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB
 *
 * @author kotuz
 */
class DB {
    //put your code here

    public $rows;
    public $rowCount;
    public $lastQuery;
    
    function __construct() {
		$this->rows = array();
		$this->rowCount = 0;
		$this->lastQuery = "";
    }

    public function query($query) {
        $result = mysql_query($query);
        if(!$result) {
            echo $query;
            exit();
        	//ExceptionController::throwException("DB", "ERROR_IN_QUERY");
        }
        $this->lastQuery = $query;
        return $result;
    }

    public function select($fields,$tables = "",$joins = "",$conditions = "",$order = "",$limit = "", $specialChars = true) {
        $query = "SELECT ".$fields;
        if($tables != "") {
        	$query .= " FROM ".$tables." ";
        }
        if($joins != "") {
            $query .= $joins." ";
        }
        if($conditions != "") {
            $query .= " WHERE ".$conditions." ";
        }
        if($order != "") {
            $query .= " ORDER BY ".$order;
        }
        if($limit != "") {
            $query .= " LIMIT ".$limit;
        }
        $result = $this->query($query, $specialChars);
        $this->rows = array();
        $count = 0;
        while($row = mysql_fetch_assoc($result)) {
            $this->rows[] = $row;
            $count ++;
        }
        $this->rowCount = $count;
    }

    public function getLastId() {
        $result = mysql_query("SELECT LAST_INSERT_ID() AS last_id");
        if(!$result) {
            ExceptionController::throwException("DB", "ERROR_IN_QUERY");
        }
        $row = mysql_fetch_assoc($result);
        return intval($row["last_id"]);
    }

    public static function escape($data,$specialChars = true) {
        if(get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }
        $data = $specialChars ? mysql_real_escape_string(htmlspecialchars($data,ENT_QUOTES,"UTF-8")) : mysql_real_escape_string($data);
        return $data;
    }

}
?>
