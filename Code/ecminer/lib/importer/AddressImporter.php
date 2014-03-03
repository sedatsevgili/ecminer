<?php
require_once(PATH."lib/importer/Importer.php");

class AddressImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Adres Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select("ia.id,ia.address,ia.zip_code,ia.city,ia.country,CONCAT(ic.first_name,' ',ic.last_name) AS name",
		"imported_addresses as ia",
		" INNER JOIN imported_customers ic ON (ia.imported_customer_id = ic.imported_customer_id)",
		"",
		"",
		$limit);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		
		$addressAttribute = new CategoricalAttribute("Adres");
		$zipCodeAttribute = new CategoricalAttribute("Posta Kodu");
		$cityAttribute = new CategoricalAttribute("Şehir");
		$countryAttribute = new CategoricalAttribute("Ülke");
		$nameAttribute = new CategoricalAttribute("Müşteri");
		$attributeArray = array($addressAttribute,$zipCodeAttribute,$cityAttribute,$countryAttribute,$nameAttribute);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributeArray);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(
			array($row["address"],$row["zip_code"],$row["city"],$row["country"],$row["name"])
			);
			$samples[] = $sample;
		}
		return new DataSet($attributeArray,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->customer_address as $customer_address) {
			$this->db->query("INSERT INTO imported_addresses(
				import_id,
				imported_customer_id,
				address,
				zip_code,
				city,
				country)
				 VALUES(
				".$this->import->id.",
				".$customer_address->customer_id.",
				'".$customer_address->address."',
				'".$customer_address->zip_code."',
				'".$customer_address->city."',
				'".$customer_address->country."')");
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
			$this->db->query("INSERT INTO imported_addresses(
			import_id,
			imported_customer_id,
			address,
			zip_code,
			city,
			country) VALUES(
			".$this->import->id.",
			".$row[$mr["CUSTOMERID"]].",
			'".$row[$mr["ADDRESS"]]."',
			'".$row[$mr["ZIPCODE"]]."',
			'".$row[$mr["CITY"]]."',
			'".$row[$mr["COUNTRY"]]."')");
		}
	}
	
}