<?php

class Model {

    private $_attributes;   // attributes excluding id
    protected $_tableName;
    protected $_db;

    function __construct($db,$tableName, $attributes = array()) {
        $this->_db = $db;
        $this->_tableName = $tableName;
        $this->_attributes = (array)$attributes;
        foreach($this->_attributes as $attribute) {
            $this->{$attribute} = "";
        }
    }

    public function load($id,$attributes = array()) {
        $this->id = intval($id);
        $attributeSql = "*";
        if(count($attributes)>0) {
            $attributeSql = "id,".implode(",",$attributes);
        } else {
            $attributes = $this->_attributes;
        }
        $this->_db->select($attributeSql,$this->_tableName,"","id=".$this->id);
        if($this->_db->rowCount == 0) {
        	return false;
        }
        foreach($attributes as $attribute) {
            $this->$attribute = $this->_db->rows[0][$attribute];
        }
        return true;
    }
    
    public function add() {
        $query = "INSERT INTO ".$this->_tableName."(".$this->_attributes[0];
        for($i=1;$i<count($this->_attributes);$i++) {
            $query .= ",".$this->_attributes[$i];
        }
        $query .= ") VALUES ('".$this->{$this->_attributes[0]}."'";
        for($i=1;$i<count($this->_attributes);$i++) {
            $query .= ",'".$this->{$this->_attributes[$i]}."'";
        }
        $query .= ")";
        $this->_db->query($query);
        $this->id = $this->_db->getLastId();
    }

    public function update($attributes = array()) {
    	if(empty($this->id)) {
            ExceptionController::throwException("ModelException","ERROR_EMPTY_ID");
        }
        $sqlAttributes = $this->_attributes;
        if(count($attributes)>0) {
            $sqlAttributes = $attributes;
        }
        $attributeSql = "SET ";
        foreach($sqlAttributes as $key=>$val) {
            $attributeSql .= $key." = '".$val."',";
        }
        $attributeSql = substr($attributeSql,0,strlen($attributeSql)-1);
        $this->_db->query("UPDATE ".$this->_tableName." ".$attributeSql." WHERE id=".$this->id);
    }

    public function delete() {
        if(empty($this->id)) {
            ExceptionController::throwException("ModelException","ERROR_EMPTY_ID");
        }
        $this->_db->query("DELETE FROM ".$this->_tableName." WHERE id=".$this->id);
    }
    
    public static function getSelectBox($db,$id,$cssClass,$nameKey,$valueKey,$tables,$joins = "",$conditions = "",$order = "",$limit = "",$selectedValue = "",$extras = array(),$onchange = "") {
    	$db->select($nameKey.",".$valueKey,$tables,$joins,$conditions,$order,$limit);
    	$html = "<select name='".$id."' id='".$id."' class='".$cssClass."'";
    	if($onchange != "") {
    		$html .= " onchange = ".$onchange;
    	}
    	$html .= ">";
    	if(count($extras)>0) {
    		$html .= "<option value='".$extras["value"]."'>".$extras["name"]."</option>";
    	}
    	foreach($db->rows as $row) {
    		$html .= "<option value='".$row[$valueKey]."' ".($row[$valueKey] == $selectedValue ? "selected" : "") .">".$row[$nameKey]."</option>";
    	}
    	$html .= "</select>";
    	return $html;
    }

}
?>
