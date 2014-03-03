<?php

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminOrderExporter extends AdminTab {
	
	const ERROR_EMPTY_EXPORT_TYPE = "Please choose an export type";

        const EXPORT_DIRECTORY = "exports/";
	
	function __construct() {
		$this->table = 'none';
		$this->className = 'none';
		parent::__construct();
	}
	
	public function display() {
		$exportType = isset($_POST["export_type"]) ? intval($_POST["export_type"]) : 0;
		?>
		<h2 class="space"><?php echo $this->l('Order Export');?></h2>
		<form action="index.php?tab=AdminOrderExporter&token=<?php echo $this->token;?>" method="post">
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
		if(isset($_POST["export_type"])) {
			switch($_POST["export_type"]) {
				case "1":
					try {
                                                $exportPath = self::EXPORT_DIRECTORY."orders.xml";
						$exporter = new XmlOrderExporter();
						$exporter->exportOrders($exportPath);
						echo "Order xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
					} catch (Exception $exception) {
						$this->errors[] = $exception->getMessage();
					}
					break;
				case "2":
					try {
                                                $exportPath = self::EXPORT_DIRECTORY."orders.xls";
						$exporter = new ExcelOrderExporter();
						$exporter->exportOrders($exportPath);
						echo "Order excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
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

class OrderExporter {
	
	const ERROR_IN_QUERY = "Error in query";
	const ERROR_EMPTY_ORDER = "There is no order data to export";
	const ERROR_EMPTY_PRODUCTS = "There is no products in any order to export";
	
	protected $orderData;
	
	private $db;
	
	function __construct() {
		$this->orderData = array();
		$this->db = Db::getInstance();
	}
	
	protected function getProductsOfOrder($orderId = 0) {
		$query = "SELECT id_order,product_id,product_quantity,product_price*(100+tax_rate)/100 as price,tax_rate FROM `ps_order_detail";
		if($orderId>0) {
			$query = "SELECT product_id,product_quantity,product_price*(100+tax_rate)/100 as price,tax_rate FROM `ps_order_detail` WHERE id_order=".$orderId;
		}
		$products = $this->db->ExecuteS($query);
		if(!$products) {
			return array();
		}
		return $products;
	}
	
	protected function prepareOrders() {
		$query = "SELECT o.id_order,o.id_customer,o.date_add,c.iso_code,o.payment,o.total_shipping FROM `ps_orders` o
		LEFT JOIN ps_currency c ON c.id_currency = o.id_currency";
		$this->orderData = $this->db->ExecuteS($query);
		if(!$this->orderData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
}

class ExcelOrderExporter extends OrderExporter {
	
	function __construct() {
		parent::__construct();
		require_once 'Spreadsheet/Excel/Writer.php';
	}
	
	public function exportOrders($exportPath) {
		$this->prepareOrders();
		if(empty($this->orderData)) {
			throw new Exception(OrderExporter::ERROR_EMPTY_ORDER);
		}
		$orderProducts = $this->getProductsOfOrder();
		if(empty($orderProducts)) {
			throw new Exception(OrderExporter::ERROR_EMPTY_PRODUCTS);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Orders");
		$sheet->setInputEncoding("UTF-8");
		$sheet->write(0,0,"ORDERID");
		$sheet->write(0,1,"CUSTOMERID");
		$sheet->write(0,2,"ORDERTIME");
		$sheet->write(0,3,"CURRENCY");
		$sheet->write(0,4,"PAYMENTTYPE");
		$sheet->write(0,5,"SHIPPINGPRICE");
		
		$rowCount = 1;
		foreach($this->orderData as $orderRow) {
			$sheet->write($rowCount,0,$orderRow["id_order"]);
			$sheet->write($rowCount,1,$orderRow["id_customer"]);
			$sheet->write($rowCount,2,$orderRow["date_add"]);
			$sheet->write($rowCount,3,$orderRow["iso_code"]);
			$sheet->write($rowCount,4,$orderRow["payment"]);
			$sheet->write($rowCount,5,$orderRow["total_shipping"]);
			$rowCount++;
		}
		
		$sheet2 = $writer->addWorksheet("Products");
		$sheet2->setInputEncoding("UTF-8");
		$sheet2->write(0,0,"PRODUCTID");
		$sheet2->write(0,1,"QUANTITY");
		$sheet2->write(0,2,"PRICE");
		$sheet2->write(0,3,"TAX");
		$sheet2->write(0,4,"ORDERID");
		$rowCount = 1;
		foreach($orderProducts as $productRow) {
			$sheet2->write($rowCount,0,$productRow["product_id"]);
			$sheet2->write($rowCount,1,$productRow["product_quantity"]);
			$sheet2->write($rowCount,2,$productRow["price"]);
			$sheet2->write($rowCount,3,$productRow["tax_rate"]);
			$sheet2->write($rowCount,4,$productRow["id_order"]);
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
}

class XmlOrderExporter extends OrderExporter {
	
	
	function __construct() {
		parent::__construct();
	}
	
	protected function getOrderXml() {
		$this->prepareOrders();
		if(empty($this->orderData)) {
			throw new Exception(self::ERROR_EMPTY_ORDER);
		}
		
		$orderXml = new SimpleXmlElement("<orders></orders>");
		foreach($this->orderData as $orderRow) {
			$xml = $orderXml->addChild("order");
			$xml->addChild("id",$orderRow["id_order"]);
			$xml->addChild("customer_id",$orderRow["id_customer"]);
			$xml->addChild("order_time",$orderRow["date_add"]);
			$xml->addChild("currency",$orderRow["iso_code"]);
			$xml->addChild("payment_type",$orderRow["payment"]);
			$xml->addChild("shipping_price",$orderRow["total_shipping"]);
			$orderProducts = $this->getProductsOfOrder($orderRow["id_order"]);
			
			$productXml = $xml->addChild("products");
			foreach($orderProducts as $productRow) {
				$pXml = $productXml->addChild("product");
				$pXml->addChild("product_id",$productRow["product_id"]);
				$pXml->addChild("quantity",$productRow["product_quantity"]);
				$pXml->addChild("price",$productRow["price"]);
				$pXml->addChild("tax",$productRow["tax_rate"]);
				unset($pXml);
			}
			unset($productXml);
		}
		
		return $orderXml;
	}
	
	public function exportOrders($exportPath) {
		$orderXml = $this->getOrderXml();
		if(!file_put_contents($exportPath,$orderXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$xmlDir."/orders_".$this->xmlCreatedTime.".xml file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
}






