<?php
require_once(PATH."lib/importer/Importer.php");

class OrderImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Sipariş Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select(
		"
		io.id,
		CONCAT(ic.first_name,' ',ic.last_name) AS customer_name,
		YEAR(io.order_time) as order_year,
		MONTH(io.order_time) as order_month,
		DAY(io.order_time) as order_day,
		DATE_FORMAT(io.order_time,'%H') as order_hour,
		DATE_FORMAT(io.order_time,'%i') as order_minute,
		io.currency,
		io.payment_type,
		io.shipping_price,
		getProductPriceOfOrder(io.import_id,io.imported_order_id) as total_price",
		"imported_orders io",
		"inner join imported_customers ic on (io.imported_customer_id=ic.imported_customer_id)",
		"io.import_id=".$this->import->id." group by io.id",
		"",
		$limit
		);
		
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		require_once(PATH."lib/core/NumericalAttribute.php");
		
		$attributes = array(
			new CategoricalAttribute("Müşteri"),
			new CategoricalAttribute("Sipariş Yılı"),
			new CategoricalAttribute("Sipariş Ayı"),
			new CategoricalAttribute("Sipariş Günü"),
			new CategoricalAttribute("Sipariş Saati"),
			new CategoricalAttribute("Sipariş Dakikası"),
			new CategoricalAttribute("Kur"),
			new CategoricalAttribute("Ödeme Tipi"),
			new NumericalAttribute("Kargo Ücreti"),
			new NumericalAttribute("Toplam Ücret")
		);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array(
			$row["customer_name"],
			$row["order_year"],
			$row["order_month"],
			$row["order_day"],
			$row["order_hour"],
			$row["order_minute"],
			$row["currency"],
			$row["payment_type"],
			$row["shipping_price"],
			$row["total_price"]
			));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->order as $order) {
			$this->db->query("INSERT INTO imported_orders(
			import_id,
			imported_order_id,
			imported_customer_id,
			order_time,
			currency,
			payment_type,
			shipping_price) VALUES(
			".$this->import->id.",
			".$order->id.",
			".$order->customer_id.",
			'".$order->order_time."',
			'".$order->currency."',
			'".$order->payment_type."',
			".$order->shipping_price.")");
			foreach($order->products->product as $product) {
				if(!empty($product->product_id)) {
					$this->db->query("INSERT INTO imported_order_to_products(
					import_id,
					imported_order_id,
					imported_product_id,
					quantity,
					price,
					tax) VALUES(
					".$this->import->id.",
					".$order->id.",
					".$product->product_id.",
					".$product->quantity.",
					".$product->tax.",
					".$product->price.")");
				}
			}
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
			$this->db->query("INSERT INTO imported_orders(
			import_id,
			imported_order_id,
			imported_customer_id,
			order_time,
			currency,
			payment_type,
			shipping_price) VALUES(
			".$this->import->id.",
			".$row[$mr["ORDERID"]].",
			".$row[$mr["CUSTOMERID"]].",
			'".$row[$mr["ORDERTIME"]]."',
			'".$row[$mr["CURRENCY"]]."',
			'".$row[$mr["PAYMENTTYPE"]]."',
			".$row[$mr["SHIPPINGPRICE"]].")");
		}
		
		$sheet = $this->readFromExcel(1);
		$cells = $sheet["cells"];
		if(count($cells)<=1) {
			return false;
		}
		$mr = array_flip($cells[1]);
		for($i=2;$i<count($cells);$i++) {
			$row = $cells[$i];
			$this->db->query("INSERT INTO imported_order_to_products(
			import_id,
			imported_order_id,
			imported_product_id,
			quantity,
			price,
			tax) VALUES(
			".$this->import->id.",
			".$row[$mr["ORDERID"]].",
			".$row[$mr["PRODUCTID"]].",
			".$row[$mr["QUANTITY"]].",
			".$row[$mr["PRICE"]].",
			".$row[$mr["TAX"]].")");
		}
	}
}
