<?php
require_once(PATH."lib/importer/Importer.php");

class CategoryImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Kategori Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select("ic1.id,ic1.name,ic1.status,if(ic2.name is null,'-',ic2.name) as parent_name","imported_categories ic1",
		"LEFT JOIN imported_categories ic2 ON (ic1.imported_parent_category_id=ic2.imported_category_id AND ic2.import_id=".$this->import->id.")",
		"ic1.import_id=".$this->import->id,
		"",
		$limit);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		
		$nameAttribute = new CategoricalAttribute("İsim");
		$statusAttribute = new CategoricalAttribute("Durum");
		$parentAttribute = new CategoricalAttribute("Üst Kategori İsmi");
		$attributes = array($nameAttribute,$statusAttribute,$parentAttribute);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array($row["name"],$row["status"],$row["parent_name"]));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->category as $category) {
			$this->db->query("INSERT INTO imported_categories(import_id,imported_category_id,name,imported_parent_category_id,status) VALUES(
			".$this->import->id.",
			".$category->id.",
			'".$category->name."',
			".$category->parentid.",
			".$category->status.")
			");
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
			$this->db->query("INSERT INTO imported_categories(import_id,imported_category_id,name,imported_parent_category_id,status) VALUES(
			".$this->import->id.",
			".$row[$mr["ID"]].",
			'".$row[$mr["NAME"]]."',
			".$row[$mr["PARENTID"]].",
			".$row[$mr["STATUS"]].")
			");
		}
	}
}