<?php
require_once(PATH."lib/importer/Importer.php");

class CustomerImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Müşteri Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select(
		"
		id,
		CONCAT(first_name,' ',last_name) AS name,
		IF(STRCMP(LOWER(gender),'')=0,'-',LOWER(gender)) as gender,
		email_address,
		status,
		registration_date,
		birthday,
		last_connection",
		"imported_customers",
		"",
		"",
		"",
		$limit
		);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		
		$attributes = array(
			new CategoricalAttribute("İsim"),
			new CategoricalAttribute("Cinsiyet"),
			new CategoricalAttribute("Email"),
			new CategoricalAttribute("Durum"),
			new CategoricalAttribute("Kayıt Tarihi"),
			new CategoricalAttribute("Doğum Tarihi"),
			new CategoricalAttribute("Son Bağlanma Zamanı")
		);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array($row["name"],$row["gender"],$row["email_address"],$row["status"],$row["registration_date"],$row["birthday"],$row["last_connection"]));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->customer as $customer) {
			$this->db->query("INSERT INTO imported_customers(
			import_id,
			imported_customer_id,
			first_name,
			last_name,
			gender,
			email_address,
			status,
			registration_date,
			birthday,
			last_connection) VALUES(
			".$this->import->id.",
			".$customer->id.",
			'".$customer->first_name."',
			'".$customer->last_name."',
			'".$customer->gender."',
			'".$customer->email_address."',
			".$customer->status.",
			'".$customer->registration_date."',
			'".$customer->birthday."',
			'".$customer->last_connection."')");
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
			$this->db->query("INSERT INTO imported_customers(
			import_id,
			imported_customer_id,
			first_name,
			last_name,
			gender,
			email_address,
			status,
			registration_date,
			birthday,
			last_connection) VALUES(
			".$this->import->id.",
			".$row[$mr["ID"]].",
			'".$row[$mr["FIRSTNAME"]]."',
			'".$row[$mr["LASTNAME"]]."',
			'".$row[$mr["GENDER"]]."',
			'".$row[$mr["EMAIL"]]."',
			".$row[$mr["STATUS"]].",
			'".$row[$mr["REGDATE"]]."',
			'".$row[$mr["BIRTHDAY"]]."',
			'".$row[$mr["LASTCONNECTION"]]."')");
		}
	}
}