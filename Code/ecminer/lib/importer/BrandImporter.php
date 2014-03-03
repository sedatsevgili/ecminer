<?php
require_once(PATH."lib/importer/Importer.php");

class BrandImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Marka Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select("id,name,status","imported_brands","","","",$limit);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		
		$nameAttribute = new CategoricalAttribute("İsim");
		$statusAttribute = new CategoricalAttribute("Durum");
		$attributes = array($nameAttribute,$statusAttribute);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array($row["name"],$row["status"]));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->brand as $brand) {
			$this->db->query("INSERT INTO imported_brands(import_id,imported_brand_id,name,status) VALUES
			(".$this->import->id.",
			".$brand->id.",
			'".$brand->name."',
			".$brand->status.")");
		}
	}
	
	public function runAsExcel() {
		$sheet = $this->readFromExcel();
		$cells = $sheet["cells"];
		if(count($cells)<=1) {
			return false;
		}
		$mr = array_flip($cells[1]);
		for($i=2;$i<count($cells);$i++) {
			$row = $cells[$i];
			$this->db->query("INSERT INTO imported_brands(import_id,imported_brand_id,name,status) VALUES
			(".$this->import->id.",
			".$row[$mr["ID"]].",
			'".$row[$mr["NAME"]]."',
			".$row[$mr["STATUS"]].
			")");
		}
	}
}