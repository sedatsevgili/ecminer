<?php
require_once(PATH."lib/importer/Importer.php");

class VisitImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Ziyaret Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select(
		"
		iv.id,
		if(CONCAT(ic.first_name,' ',ic.last_name) is null,'-',CONCAT(ic.first_name,' ',ic.last_name)) AS customer_name,
		iv.visited_page,
		YEAR(iv.time_start) AS visited_year,
		MONTH(iv.time_start) AS visited_month,
		DAY(iv.time_start) AS visited_day,
		DATE_FORMAT(iv.time_start,'%H') AS visited_hour,
		DATE_FORMAT(iv.time_start,'%i') AS visited_minute",
		"imported_visits as iv",
		"inner join imported_visitors ivv on (iv.imported_visitor_id=ivv.imported_visitor_id)
left join imported_customers ic on (ivv.imported_customer_id=ic.imported_customer_id)",
		"iv.import_id=".$this->import->id." group by iv.imported_visitor_id",
		"",
		$limit
		);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		$attributes = array(
			new CategoricalAttribute("Ziyaretçi"),
			new CategoricalAttribute("Ziyaret Edilen Sayfa"),
			new CategoricalAttribute("Ziyaret Yılı"),
			new CategoricalAttribute("Ziyaret Ayı"),
			new CategoricalAttribute("Ziyaret Günü"),
			new CategoricalAttribute("Ziyaret Saati"),
			new CategoricalAttribute("Ziyaret Dakikası")
		);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array(
			$row["customer_name"],
			$row["visited_page"],
			$row["visited_year"],
			$row["visited_month"],
			$row["visited_day"],
			$row["visited_hour"],
			$row["visited_minute"]
			));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->visit as $visit) {
			$this->db->query("
			INSERT INTO imported_visits(
			import_id,
			imported_visitor_id,
			visited_page,
			visited_element_id,
			time_start,
			time_end) VALUES(
			".$this->import->id.",
			".$visit->visitor_id.",
			'".$visit->visited_page."',
			".intval($visit->visited_element_id).",
			'".$visit->time_start."',
			'".$visit->time_end."')
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
			$this->db->query("
			INSERT INTO imported_visits(
			import_id,
			imported_visitor_id,
			visited_page,
			visited_element_id,
			time_start,
			time_end) VALUES(
			".$this->import->id.",
			".$row[$mr["VISITORID"]].",
			'".$row[$mr["PAGE"]]."',
			".intval($row[$mr["ELEMENTID"]]).",
			'".$row[$mr["STARTTIME"]]."',
			'".$row[$mr["ENDTIME"]]."')
			");
		}
	}
}