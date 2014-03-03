<?php

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminCustomerExporter extends AdminTab 
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
			<h2 class="space"><?php echo $this->l('Customer Export');?></h2>
			<form action="index.php?tab=AdminCustomerExporter&token=<?php echo $this->token;?>" method="post">
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
					<option value="1" <?php if($exportField ==  1) { echo "selected"; }?>>Customers</option>
					<option value="2" <?php if($exportField ==  2) { echo "selected"; }?>>Addresses</option>
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
		if(!isset($_POST["export_type"])) {
			$this->errors[] = self::ERROR_EMPTY_EXPORT_TYPE;
			return false;
		}
		if(!isset($_POST["export_field"])) {
			$this->errors[] = self::ERROR_EMPTY_EXPORT_FIELD;
			return false;	
		}
		switch($_POST["export_type"]) {
			case "1":
				$exporter = new XmlCustomerExporter();
				try {
					switch($_POST["export_field"]) {
						case "1":
                                                        $exportPath = self::EXPORT_DIRECTORY."customers.xml";
							$exporter->exportCustomers($exportPath);
							echo "Customer xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
							break;
						case "2":
                                                        $exportPath = self::EXPORT_DIRECTORY."addresses.xml";
							$exporter->exportAddresses($exportPath);
							echo "Address xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
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
				$exporter = new ExcelCustomerExporter();
				try {
					switch($_POST["export_field"]) {
						case "1":
                                                        $exportPath = self::EXPORT_DIRECTORY."customers.xls";
							$exporter->exportCustomers($exportPath);
							echo "Customer excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
							break;
						case "2":
                                                        $exportPath = self::EXPORT_DIRECTORY."addresses.xls";
							$exporter->exportAddresses($exportPath);
							echo "Address excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
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

class CustomerExporter {

	const ERROR_IN_QUERY = "Error in query";
	const ERROR_EMPTY_CUSTOMER = "There is no customer data to export";
	const ERROR_EMPTY_ADDRESS = "There is no address data to export";
	
	protected $customerData;
	protected $addressData;
	
	private $db;
	
	function __construct() {
		$this->customerData = array();
		$this->addressData = array();
		$this->db = Db::getInstance();
	}

        protected function prepareCustomers() {
		$query = "SELECT id_customer,firstname,lastname,birthday,id_gender,email,active,date_add,date_upd FROM ps_customer";
		$this->customerData = $this->db->ExecuteS($query);
		if(!$this->customerData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
	
	protected function prepareAddresses() {
		$query = "
		SELECT a.id_customer,a.address1,a.postcode,a.city,c.name 
		FROM `ps_address` a 
		INNER JOIN ps_customer cu ON cu.id_customer = a.id_customer 
		LEFT JOIN ps_country_lang c ON a.id_country = c.id_country
		WHERE c.id_lang = 1;
		";
		$this->addressData = $this->db->ExecuteS($query);
		if(!$this->addressData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
}

class ExcelCustomerExporter extends CustomerExporter {
	
	
	function __construct() {
		parent::__construct();
		require_once 'Spreadsheet/Excel/Writer.php';
	}
	
	public function exportCustomers($exportPath) {
		$this->prepareCustomers();
                if(empty($this->customerData)) {
			throw new Exception(CustomerExporter::ERROR_EMPTY_ADDRESS);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Customers");
		$sheet->setInputEncoding("UTF-8");
		$sheet->write(0,0,"ID");
		$sheet->write(0,1,"FIRSTNAME");
		$sheet->write(0,2,"LASTNAME");
		$sheet->write(0,3,"GENDER");
		$sheet->write(0,4,"EMAIL");
		$sheet->write(0,5,"STATUS");
		$sheet->write(0,6,"REGDATE");
		$sheet->write(0,7,"BIRTHDAY");
		$sheet->write(0,8,"LASTCONNECTION");
		
		$rowCount = 1;
		foreach($this->customerData as $customerRow) {
			$sheet->write($rowCount,0,$customerRow["id_customer"]);
			$sheet->write($rowCount,1,$customerRow["firstname"]);
			$sheet->write($rowCount,2,$customerRow["lastname"]);
			$sheet->write($rowCount,3,($customerRow["id_gender"] == 1 ? "Male" : "Female"));
			$sheet->write($rowCount,4,$customerRow["email"]);
			$sheet->write($rowCount,5,$customerRow["active"]);
			$sheet->write($rowCount,6,$customerRow["date_add"]);
			$sheet->write($rowCount,7,$customerRow["birthday"]);
			$sheet->write($rowCount,8,$customerRow["date_upd"]);
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportAddresses($exportPath) {
		$this->prepareAddresses();
		if(empty($this->addressData)) {
			throw new Exception(CustomerExporter::ERROR_EMPTY_ADDRESS);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Addresses");
		$sheet->write(0,0,"CUSTOMERID");
		$sheet->write(0,1,"ADDRESS");
		$sheet->write(0,2,"ZIPCODE");
		$sheet->write(0,3,"CITY");
		$sheet->write(0,4,"COUNTRY");
		
		$rowCount = 1;
		foreach($this->addressData as $addressRow) {
			$sheet->write($rowCount,0,$addressRow["id_customer"]);
			$sheet->write($rowCount,1,$addressRow["address1"]);
			$sheet->write($rowCount,2,$addressRow["postcode"]);
			$sheet->write($rowCount,3,$addressRow["city"]);
			$sheet->write($rowCount,4,$addressRow["name"]);
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
}

class XmlCustomerExporter extends CustomerExporter {
	
	private $xmlCustomerData;
	private $xmlAddressData;
	
	
	function __construct() {
		parent::__construct();
		$this->xmlCustomerData = "";
		$this->xmlAddressData = "";
	}
	
	protected function getCustomerXml() {
		$this->prepareCustomers();
		if(empty($this->customerData)) {
			throw new Exception(CustomerExporter::ERROR_EMPTY_CUSTOMER);
		}
		
		$customerXml = new SimpleXmlElement("<customers></customers>");
		foreach($this->customerData as $customerRow) {
			$xml = $customerXml->addChild("customer");
			$xml->addChild("id",$customerRow["id_customer"]);
			$xml->addChild("first_name",$customerRow["firstname"]);
			$xml->addChild("last_name",$customerRow["lastname"]);
			$xml->addChild("gender",($customerRow["id_gender"]==1 ? "m" : "f"));
			$xml->addChild("email_address",$customerRow["email"]);
			$xml->addChild("status",$customerRow["active"]);
			$xml->addChild("registration_date",$customerRow["date_add"]);
			$xml->addChild("birthday",$customerRow["birthday"]);
			$xml->addChild("last_connection",$customerRow["date_upd"]);
		}
		
		return $customerXml;
	}
	
	protected function getAddressXml() {
		$this->prepareAddresses();
		if(empty($this->addressData)) {
			throw new Exception(CustomerExporter::ERROR_EMPTY_ADDRESS);
		}
		
		$addressXml = new SimpleXmlElement("<customer_addresses></customer_addresses>");
		foreach($this->addressData as $addressRow) {
			$xml = $addressXml->addChild("customer_address");
			$xml->addChild("customer_id",$addressRow["id_customer"]);
			$xml->addChild("address",$addressRow["address1"]);
			$xml->addChild("zip_code",$addressRow["postcode"]);
			$xml->addChild("city",$addressRow["city"]);
			$xml->addChild("country",$addressRow["name"]);
		}
		
		return $addressXml;
	} 
	
	public function exportCustomers($exportPath) {
		$customerXml = $this->getCustomerXml();
		if(!file_put_contents($exportPath,$customerXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportAddresses($exportPath) {
		$addressXml = $this->getAddressXml();
		if(!file_put_contents($exportPath,$addressXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$xmlDir."/addresses_".$this->xmlCreatedTime.".xml file. Please check your file permissions");
			return false;
		}
		return true;
	}
}

