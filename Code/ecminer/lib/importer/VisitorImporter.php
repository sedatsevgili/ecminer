<?php
require_once(PATH."lib/importer/Importer.php");

class VisitorImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Ziyaretçi Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select(
		"
		iv.id,
		if(concat(ic.first_name,' ',ic.last_name) is null,'-',concat(ic.first_name,' ',ic.last_name)) as customer_name,
		iv.operating_system,
		iv.browser,
		iv.accept_language,
		if(strcmp(iv.http_referer,'')=0,'-',iv.http_referer) as http_referer,
		iv.ip_address,
		YEAR(iv.first_visited_time) AS visit_year,
		MONTH(iv.first_visited_time) AS visit_month,
		DAY(iv.first_visited_time) AS visit_day,
		DATE_FORMAT(iv.first_visited_time,'%H') AS visit_hour,
		DATE_FORMAT(iv.first_visited_time,'%i') AS visit_minute,
		getCountVisitsOfVisitor(iv.imported_visitor_id) as visit_count",
		"imported_visitors iv",
		"left join imported_customers ic on (iv.imported_customer_id=ic.imported_customer_id)",
		"iv.import_id=".$this->import->id." group by iv.imported_visitor_id",
		"",
		$limit
		);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		
		$attributes = array(
			new CategoricalAttribute("Ziyaretçi"),
			new CategoricalAttribute("İşletim Sistemi"),
			new CategoricalAttribute("Tarayıcı"),
			new CategoricalAttribute("Dil"),
			new CategoricalAttribute("Referans Adresi"),
			new CategoricalAttribute("IP Adresi"),
			new CategoricalAttribute("Ziyaret Yılı"),
			new CategoricalAttribute("Ziyaret Ayı"),
			new CategoricalAttribute("Ziyaret Günü"),
			new CategoricalAttribute("Ziyaret Saati"),
			new CategoricalAttribute("Ziyaret Dakikası"),
			new CategoricalAttribute("Ziyaret Sayısı")
		);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array(
			$row["customer_name"],
			$row["operating_system"],
			$row["browser"],
			$row["accept_language"],
			$row["http_referer"],
			$row["ip_address"],
			$row["visit_year"],
			$row["visit_month"],
			$row["visit_day"],
			$row["visit_hour"],
			$row["visit_minute"],
			$row["visit_count"]
			));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->visitor as $visitor) {
			$this->db->query("
			INSERT INTO imported_visitors(
			import_id,
			imported_visitor_id,
			operating_system,
			browser,
			imported_customer_id,
			accept_language,
			http_referer,
			ip_address,
			first_visited_time) VALUES(
			".$this->import->id.",
			".$visitor->id.",
			'".$visitor->operating_system."',
			'".$visitor->browser."',
			".$visitor->customer_id.",
			'".$visitor->accept_language."',
			'".$visitor->http_referer."',
			'".$visitor->ip_address."',
			'".$visitor->first_visited_time."')
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
			INSERT INTO imported_visitors(
			import_id,
			imported_visitor_id,
			operating_system,
			browser,
			imported_customer_id,
			accept_language,
			http_referer,
			ip_address,
			first_visited_time) VALUES(
			".$this->import->id.",
			".$row[$mr["ID"]].",
			'".$row[$mr["OSNAME"]]."',
			'".$row[$mr["BROWSER"]]."',
			".$row[$mr["CUSTOMERID"]].",
			'".$row[$mr["LANGUAGE"]]."',
			'".$row[$mr["HTTPREFERER"]]."',
			'".$row[$mr["IPADDRESS"]]."',
			'".$row[$mr["FIRSTTIME"]]."')
			");
		}
	}
}