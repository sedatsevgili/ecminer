<?php
include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminStatExporter extends AdminTab
{
	const ERROR_EMPTY_EXPORT_FIELD = "Please choose an export field";
	const ERROR_EMPTY_EXPORT_TYPE = "Please choose an export type";

        const EXPORT_DIRECTORY = "exports/";
	
	function __construct() {
		$this->table = 'none';
		$this->className = 'none';
		parent::__construct();
	}
	
	public function display() {
		$exportType = isset($_POST["export_type"]) ? intval($_POST["export_type"]) : 0;
		$exportField = isset($_POST["export_field"]) ? intval($_POST["export_field"]) : 0;
			?>
			<h2 class="space"><?php echo $this->l('Stat Export');?></h2>
			<form action="index.php?tab=AdminStatExporter&token=<?php echo $this->token;?>" method="post">
			<center><table width="60%" cellpadding="3" cellspacing="0" class="table">
			<tr class="nodrag nodrop">
				<td align="left">
				Select an export type:
				</td>
				<td align="left">
				<select name="export_type">
					<option value="0">--Choose--</option>
					<option value="1" <?php if($exportType == 1) { echo "selected"; }?>>Xml</option>
					<option value="2" <?php if($exportType == 2) { echo "selected"; }?>>Excel</option>
				</select>
				</td>
			</tr>
			<tr class="nodrag nodrop">
				<td align="left">
				Select an export field:
				</td>
				<td align="left">
				<select name="export_field">
					<option value="0">--Choose--</option>
					<option value="1" <?php if($exportField ==  1) { echo "selected"; }?>>Visitors</option>
					<option value="2" <?php if($exportField ==  2) { echo "selected"; }?>>Visits</option>
				</select>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="submit" value="Export" class="button"/></input>
				</td>
			</tr>
			</table></center>
			</form>
			<?php
	}
	
	public function postProcess() {
		if(isset($_POST["export_type"]) && isset($_POST["export_field"])) {
			switch($_POST["export_type"]) {
				case "1":
					$exporter = new XmlStatExporter();
					try {
						switch($_POST["export_field"]) {
							case "1":
                                                                $exportPath = self::EXPORT_DIRECTORY."visitors.xml";
								$exporter->exportVisitors($exportPath);
								echo "Visitor xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "2":
                                                                $exportPath = self::EXPORT_DIRECTORY."visits.xml";
								$exporter->exportVisits($exportPath);
								echo "Visit xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							default:
								throw new Exception(self::ERROR_EMPTY_EXPORT_FIELD);
								break;
						}
					} catch (Exception $exception) {
						$this->errors[] = $exception->getMessage();
					}
					break;
				case "2":
					$exporter = new ExcelStatExporter();
					try {
						switch($_POST["export_field"]) {
							case "1":
                                                                $exportPath = self::EXPORT_DIRECTORY."visitors.xls";
								$exporter->exportVisitors($exportPath);
								echo "Visitor excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "2":
                                                                $exportPath = self::EXPORT_DIRECTORY."visits.xls";
								$exporter->exportVisits($exportPath);
								echo "Visit excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							default:
								throw new Exception(self::ERROR_EMPTY_EXPORT_FIELD);
								break;
						}
					} catch (Exception $exception) {
						$this->errors[] = $exception->getMessage();
					}
					break;
				default:
					$this->errors[] = self::ERROR_EMPTY_EXPORT_TYPE;
					break;
			}
		}
		
	}
}

class StatExporter {
	
	const ERROR_IN_QUERY = "Error in query";
	const ERROR_EMPTY_VISITOR = "There is no visitor data to export";
	const ERROR_EMPTY_VISIT = "There is no visit data to export";
	
	protected $visitorData;
	protected $visitData;
	
	private $db;
	
	function __construct() {
		$this->db = Db::getInstance();
		$this->visitorData = array();
		$this->visitData = array();
	}
	
	protected function prepareVisitors() {
		$query = "SELECT pc.id_connections,pos.name AS osname,pwb.name AS browser,g.id_customer,g.accept_language, pc.http_referer, pc.ip_address, pc.date_add 
		FROM ps_connections pc
INNER JOIN ps_guest g ON pc.id_guest = g.id_guest
LEFT JOIN ps_web_browser pwb ON g.id_web_browser = pwb.id_web_browser
LEFT JOIN ps_operating_system pos ON g.id_operating_system = pos.id_operating_system";
		$this->visitorData = $this->db->ExecuteS($query);
		if(!$this->visitorData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
	
	protected function prepareVisits() {
		$query = "SELECT pcp.id_connections,ppt.name,pp.id_object,pcp.time_start,pcp.time_end FROM ps_connections_page pcp
INNER JOIN ps_page pp ON pp.id_page = pcp.id_page
INNER JOIN ps_page_type ppt ON pp.id_page_type = ppt.id_page_type";
		$this->visitData = $this->db->ExecuteS($query);
		if(!$this->visitData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
}

class ExcelStatExporter extends StatExporter {
	
	function __construct() {
		parent::__construct();
		require_once 'Spreadsheet/Excel/Writer.php';
	}
	
	public function exportVisitors($exportPath) {
		$this->prepareVisitors();
		if(empty($this->visitorData)) {
			throw new Exception(StatExporter::ERROR_EMPTY_VISITOR);
		}
		
		$this->excelCreatedTime = time();
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Visitors");
		$sheet->setInputEncoding("UTF-8");
		$sheet->write(0,0,"ID");
		$sheet->write(0,1,"OSNAME");
		$sheet->write(0,2,"BROWSER");
		$sheet->write(0,3,"CUSTOMERID");
		$sheet->write(0,4,"LANGUAGE");
		$sheet->write(0,5,"HTTPREFERER");
		$sheet->write(0,6,"IPADDRESS");
		$sheet->write(0,7,"FIRSTTIME");
		$rowCount = 1;
		foreach($this->visitorData as $visitorRow) {
			$sheet->write($rowCount,0,$visitorRow["id_connections"]);
			$sheet->write($rowCount,1,$visitorRow["osname"]);
			$sheet->write($rowCount,2,$visitorRow["browser"]);
			$sheet->write($rowCount,3,$visitorRow["id_customer"]);
			$sheet->write($rowCount,4,$visitorRow["accept_language"]);
			$sheet->write($rowCount,5,$visitorRow["http_referer"]);
			$sheet->write($rowCount,6,$visitorRow["ip_address"]);
			$sheet->write($rowCount,7,$visitorRow["date_add"]);
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportVisits($exportPath) {
		$this->prepareVisits();
		if(empty($this->visitData)) {
			throw new Exception(StatExporter::ERROR_EMPTY_VISIT);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Visits");
		$sheet->setInputEncoding("UTF-8");
		$sheet->write(0,0,"VISITORID");
		$sheet->write(0,1,"PAGE");
		$sheet->write(0,2,"ELEMENTID");
		$sheet->write(0,3,"STARTTIME");
		$sheet->write(0,4,"ENDTIME");
		$rowCount = 1;
		foreach($this->visitData as $visitRow) {
			$sheet->write($rowCount,0,$visitRow["id_connections"]);
			$sheet->write($rowCount,1,$visitRow["name"]);
			$sheet->write($rowCount,2,$visitRow["id_object"]);
			$sheet->write($rowCount,3,$visitRow["time_start"]);
			$sheet->write($rowCount,4,$visitRow["time_end"]);
			$rowCount++;
		}
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
}

class XmlStatExporter extends StatExporter{
	
	function __construct() {
		parent::__construct();
	}
	
	protected function getVisitorXml() {
		$this->prepareVisitors();
		if(empty($this->visitorData)) {
			throw new Exception(StatExporter::ERROR_EMPTY_VISITOR);
		}
		
		$visitorXml = new SimpleXmlElement("<visitors></visitors>");
		foreach($this->visitorData as $visitorRow) {
			$xml = $visitorXml->addChild("visitor");
			$xml->addChild("id",$visitorRow["id_connections"]);
			$xml->addChild("operating_system",$visitorRow["osname"]);
			$xml->addChild("browser",$visitorRow["browser"]);
			$xml->addChild("customer_id",intval($visitorRow["id_customer"]));
			$xml->addChild("accept_language",$visitorRow["accept_language"]);
			$xml->addChild("http_referer",$visitorRow["http_referer"]);
			$xml->addChild("ip_address",$visitorRow["ip_address"]);
			$xml->addChild("first_visited_time",$visitorRow["date_add"]);
		}
		return $visitorXml;
	}
	
	protected function getVisitXml() {
		$this->prepareVisits();
		if(empty($this->visitData)) {
			throw new Exception(StatExporter::ERROR_EMPTY_VISIT);
		}
		
		$visitXml = new SimpleXmlElement("<visits></visits>");
		foreach($this->visitData as $visitRow) {
			$xml = $visitXml->addChild("visit");
			$xml->addChild("visitor_id",$visitRow["id_connections"]);
			$xml->addChild("visited_page",$visitRow["name"]);
			$xml->addChild("visited_element_id",$visitRow["id_object"]);
			$xml->addChild("time_start",$visitRow["time_start"]);
			$xml->addChild("time_end",$visitRow["time_end"]);
		}
		return $visitXml;
	}
	
	public function exportVisitors($exportPath) {
		$visitorXml = $this->getVisitorXml();
		if(!file_put_contents($exportPath,$visitorXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportVisits($exportPath) {
		$visitXml = $this->getVisitXml();
		if(!file_put_contents($exportPath,$visitXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
}










