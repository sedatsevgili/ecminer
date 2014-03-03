<?php
class Importer {
	
	public $db;
	public $import;
	public $extension;
	
	function __construct($db,$import) {
		$this->db = $db;
		$this->import = $import;
		switch($this->import->extension) {
			case ".xml":
				$this->extension = "xml";
				break;
			case ".xls":
				$this->extension = "xls";
				break;
			default:
				throw new Exception("Veri dosyasının uzantısı tanımlanamadı");
				break;
		}
	}
	
	public static function createInstance($db,$import) {
		$db->select("ic.name","import_classes ic","inner join import_fields iff on (iff.import_class_id = ic.id)","iff.id=".$import->import_field_id);
		$className = $db->rows[0]["name"];
		if($className == "") {
			throw new Exception("Veri sınıfı bulunamadı");
		}
		require_once(PATH."lib/importer/".$className.".php");
		return new $className($db,$import);
	}
	
	protected function readFromXml() {
		$xml = simplexml_load_file(FILEPATH."imports/".$this->import->id.$this->import->extension);
		if(!$xml) {
			throw new Exception("Xml veri dosyası geçerli değil");
		}
		return $xml;
	}
	
	protected function readFromExcel($sheetIndex = 0) {
		require_once(PATH."lib/reader/Reader.php");
		$reader = new Spreadsheet_Excel_Reader(FILEPATH."imports/".$this->import->id.$this->import->extension);
		return $reader->sheets[$sheetIndex];
	}
	
	public function run() {
		if($this->extension == "xml") {
			$this->runAsXml();
		} else if ($this->extension == "xls") {
			$this->runAsExcel();
		} else {
			throw new Exception("Tanımlanamayan Dosya Biçimi");
		}
	}
	
}