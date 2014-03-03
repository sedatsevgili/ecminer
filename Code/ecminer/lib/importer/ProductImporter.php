<?php
require_once(PATH."lib/importer/Importer.php");

class ProductImporter extends Importer {
	
	public $name;
	
	function __construct($db,$import) {
		parent::__construct($db,$import);
		$this->name = "Ürün Bilgileri";
	}
	
	public function getDataSet($limit = "") {
		$this->db->select(
		"
		ip.id,
		ip.name,
		ip.status,
		ip.price,
		ip.currency,
		ip.tax,
		ip.quantity,
		ib.name as brand_name,
		ic.name as category_name,
		getCountOrderOfProduct(ip.import_id,ip.imported_product_id) as count_order",
		"imported_products ip",
		"inner join imported_product_to_brands ipb on (ipb.imported_product_id=ip.imported_product_id and ipb.import_id=ip.import_id)
inner join imported_brands ib on (ipb.imported_brand_id=ib.imported_brand_id)
inner join imported_product_to_categories ipc on (ipc.imported_product_id=ip.imported_product_id and ipc.import_id=ip.import_id)
inner join imported_categories ic on (ipc.imported_category_id=ic.imported_category_id)",
		"ip.import_id=".$this->import->id." group by ip.imported_product_id",
		"",
		$limit
		);
		require_once(PATH."lib/core/DataSet.php");
		require_once(PATH."lib/core/CategoricalAttribute.php");
		require_once(PATH."lib/core/NumericalAttribute.php");
		
		$attributes = array(
		new CategoricalAttribute("Ürün İsmi"),
		new CategoricalAttribute("Durum"),
		new NumericalAttribute("Fiyat"),
		new CategoricalAttribute("Kur"),
		new NumericalAttribute("Vergi"),
		new NumericalAttribute("Stok"),
		new CategoricalAttribute("Marka"),
		new CategoricalAttribute("Kategori"),
		new NumericalAttribute("Sipariş Sayısı")
		);
		$samples = array();
		foreach($this->db->rows as $row) {
			$sample = new Sample($attributes);
			$sample->id = $row["id"];
			$sample->fillWithAttributeValues(array(
			$row["name"],
			$row["status"],
			$row["price"],
			$row["currency"],
			$row["tax"],
			$row["quantity"],
			$row["brand_name"],
			$row["category_name"],
			$row["count_order"]
			));
			$samples[] = $sample;
		}
		return new DataSet($attributes,"",$samples);
	}
	
	public function runAsXml() {
		$xml = $this->readFromXml();
		foreach($xml->product as $product) {
			$this->db->query("
			INSERT INTO imported_products(
			import_id,
			imported_product_id,
			name,
			status,
			price,
			currency,
			tax,
			quantity) VALUES(
			".$this->import->id.",
			".$product->id.",
			'".$product->name."',
			".$product->status.",
			".$product->price.",
			'".$product->currency."',
			".$product->tax.",
			".$product->quantity.")
			");
			foreach($product->category_id as $category_id) {
				$this->db->query("INSERT INTO imported_product_to_categories(import_id,imported_product_id,imported_category_id)
				VALUES("
				.$this->import->id.",
				".$product->id.",
				".strval($category_id).")
				");
			}
			foreach($product->brand_id as $brand_id) {
				$this->db->query("INSERT INTO imported_product_to_brands(import_id,imported_product_id,imported_brand_id)
				VALUES("
				.$this->import->id.",
				".$product->id.",
				".strval($brand_id).")");
			}
			foreach($product->attribute_combination as $attribute_combination) {
				$attribute_combination_id = mt_rand();
				$this->db->query("INSERT INTO imported_attribute_combinations(
				import_id,
				imported_attribute_combination_id,
				imported_product_id,
				price) VALUES(
				".$this->import->id.",
				".$attribute_combination_id.",
				".$product->id.",
				".$attribute_combination->price.")");
				foreach($attribute_combination as $attribute) {
					$attribute_id = mt_rand();
					$this->db->query("INSERT INTO imported_attributes(
					import_id,
					imported_attribute_id,
					name,
					value) VALUES(
					".$this->import->id.",
					".$attribute_id.",
					'".$attribute->name."',
					'".$attribute->value."')");
					$this->db->query("INSERT INTO imported_attribute_combination_to_attribute(
					import_id,
					imported_attribute_combination_id,
					imported_attribute_id) VALUES(
					".$this->import->id.",
					".$attribute_combination_id.",
					".$attribute_id.")");
				}
			}
			foreach($product->label as $label) {
				$label_id = mt_rand();
				$this->db->query("INSERT INTO imported_labels(import_id,imported_label_id,name) VALUES(
				".$this->import->id.",
				".$label_id.",
				'".strval($label)."')");
				$this->db->query("INSERT INTO imported_product_to_labels(import_id,imported_product_id,imported_label_id)
				VALUES(".$this->import->id.",
				".$product->id.",
				".$label_id.")");
			}
			foreach($product->image as $image) {
				$image_id = mt_rand();
				$this->db->query("INSERT INTO imported_images(import_id,imported_image_id,path) VALUES(
				".$this->import->id.",".$image_id.",'".strval($image)."')");
				$this->db->query("INSERT INTO imported_product_to_images(import_id,imported_product_id,imported_image_id) VALUES(
				".$this->import->id.",
				".$product->id.",
				".$image_id.")");
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
			$this->db->query("
			INSERT INTO imported_products(
			import_id,
			imported_product_id,
			name,
			status,
			price,
			currency,
			tax,
			quantity) VALUES(
			".$this->import->id.",
			".$row[$mr["ID"]].",
			'".$row[$mr["NAME"]]."',
			".$row[$mr["STATUS"]].",
			".$row[$mr["PRICE"]].",
			'".$row[$mr["CURRENCY"]]."',
			".$row[$mr["TAX"]].",
			".$row[$mr["QUANTITY"]].")
			");
			
			$attribute_combinations = array();
			
			for($j=1;$j<=3;$j++) {

				if(!empty($row[$mr["CATEGORY".$j]])) {
					$this->db->query("INSERT INTO imported_product_to_categories(import_id,imported_product_id,imported_category_id)
					VALUES("
					.$this->import->id.",
					".$row[$mr["ID"]].",
					".$row[$mr["CATEGORY".$j]].")
					");
				}
				
				if(!empty($row[$mr["BRAND".$j]])) {
					$this->db->query("INSERT INTO imported_product_to_brands(import_id,imported_product_id,imported_brand_id)
					VALUES("
					.$this->import->id.",
					".$row[$mr["ID"]].",
					".$row[$mr["BRAND".$j]].")");
				}
				
				if(!empty($row[$mr["LABEL".$j]])) {
					$label_id = mt_rand();
					$this->db->query("INSERT INTO imported_labels(import_id,imported_label_id,name) VALUES(
					".$this->import->id.",
					".$label_id.",
					'".$row[$mr["LABEL".$j]]."')");
					$this->db->query("INSERT INTO imported_product_to_labels(import_id,imported_product_id,imported_label_id)
					VALUES(".$this->import->id.",
					".$row[$mr["ID"]].",
					".$label_id.")");
				}
				
				if(!empty($row[$mr["ATTRIBUTE_PRICE_".$j]]) && !empty($row[$mr["ATTRIBUTE_NAME_".$j]]) && !empty($row[$mr["ATTRIBUTE_VALUE_".$j]])) {
					$attribute_combination_id = mt_rand();
					if(!array_key_exists(strval($row[$mr["ATTRIBUTE_PRICE_".$j]]),$attribute_combinations)) {
						$attribute_combinations[$row[$mr["ATTRIBUTE_PRICE_".$j]]] = array("id"=>$attribute_combination_id,"attributes"=>array());
					}
					$attribute_id = mt_rand();
					$attribute_combinations[$row[$mr["ATTRIBUTE_PRICE_".$j]]]["attributes"][] = array("id"=>$attribute_id,"name"=>$row[$mr["ATTRIBUTE_NAME_".$j]],"value"=>$row[$mr["ATTRIBUTE_VALUE_".$j]]);
				}
				
				if(!empty($row[$mr["IMAGE".$j]])) {
					$image_id = mt_rand();
					$this->db->query("INSERT INTO imported_images(import_id,imported_image_id,path) VALUES(
					".$this->import->id.",".$image_id.",'".$row[$mr["IMAGE".$j]]."')");
					$this->db->query("INSERT INTO imported_product_to_images(import_id,imported_product_id,imported_image_id) VALUES(
					".$this->import->id.",
					".$row[$mr["ID"]].",
					".$image_id.")");
				}
			}
			
			foreach($attribute_combinations as $price=>$combination) {
				$this->db->query("INSERT INTO imported_attribute_combinations(
				import_id,
				imported_attribute_combination_id,
				imported_product_id,
				price) VALUES(
				".$this->import->id.",
				".$combination["id"].",
				".$row[$mr["ID"]].",
				".$price.")");
				foreach($combination["attributes"] as $attribute) {
					$this->db->query("INSERT INTO imported_attributes(
					import_id,
					imported_attribute_id,
					name,
					value) VALUES(
					".$this->import->id.",
					".$attribute["id"].",
					'".$attribute["name"]."',
					'".$attribute["value"]."')");
					$this->db->query("INSERT INTO imported_attribute_combination_to_attributes(
					import_id,
					imported_attribute_combination_id,
					imported_attribute_id) VALUES(
					".$this->import->id.",
					".$combination["id"].",
					".$attribute["id"].")");
				}
			}
			
			if(!empty($row[$mr["IMAGE4"]])) {
				$image_id = mt_rand();
				$this->db->query("INSERT INTO imported_images(import_id,imported_image_id,path) VALUES(
				".$this->import->id.",".$image_id.",'".$row[$mr["IMAGE4"]]."')");
				$this->db->query("INSERT INTO imported_product_to_images(import_id,imported_product_id,imported_image_id) VALUES(
				".$this->import->id.",
				".$row[$mr["ID"]].",
				".$image_id.")");
			}
			
			for($j=4;$j<=5;$j++) {
				
				if(!empty($row[$mr["LABEL".$j]])) {
					$label_id = mt_rand();
					$this->db->query("INSERT INTO imported_labels(import_id,imported_label_id,name) VALUES(
					".$this->import->id.",
					".$label_id.",
					'".$row[$mr["LABEL".$j]]."')");
					$this->db->query("INSERT INTO imported_product_to_labels(import_id,imported_product_id,imported_label_id)
					VALUES(".$this->import->id.",
					".$row[$mr["ID"]].",
					".$label_id.")");
				}
			}
		}
	}
}