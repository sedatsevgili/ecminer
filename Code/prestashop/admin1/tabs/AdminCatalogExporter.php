<?php

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminCatalogExporter extends AdminTab
{
	
	const ERROR_IN_QUERY = "Error in query";
	const ERROR_EMPTY_CATEGORY = "There is no category to export";
	const ERROR_EMPTY_BRAND = "There is no brand to export";
	const ERROR_EMPTY_PRODUCT = "There is no product to export";
	const ERROR_EMPTY_EXPORT_TYPE = "Please choose an export type";
	const ERROR_EMPTY_EXPORT_FIELD = "Please choose an export field";

        const EXPORT_DIRECTORY = "exports/";
		
	function __construct()
	{
		$this->table = 'none';
		$this->className = 'none';
		
		parent::__construct();
	}
	
	public function display() 
	{
		$exportType = isset($_POST["export_type"]) ? intval($_POST["export_type"]) : 0;
		$exportField = isset($_POST["export_field"]) ? intval($_POST["export_field"]) : 0;
		?>
		<h2 class="space"><?php echo $this->l('Catalog Export');?></h2>
		<form action="index.php?tab=AdminCatalogExporter&token=<?php echo $this->token;?>" method="post">
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
				<option value="1" <?php if($exportField ==  1) { echo "selected"; }?>>Categories</option>
				<option value="2" <?php if($exportField ==  2) { echo "selected"; }?>>Brands</option>
				<option value="3" <?php if($exportField ==  3) { echo "selected"; }?>>Products</option>
				<option value="4" <?php if($exportField ==  4) { echo "selected"; }?>>All Catalog</option>
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
					$exporter = new XmlCatalogExporter();
					try {
						switch($_POST["export_field"]) {
							case "1":
                                                                $exportPath = self::EXPORT_DIRECTORY . "categories.xml";
								$exporter->exportCategories($exportPath);
								echo "Category xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "2":
                                                                $exportPath = self::EXPORT_DIRECTORY . "brands.xml";
								$exporter->exportBrands($exportPath);
								echo "Brand xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "3":
                                                                $exportPath = self::EXPORT_DIRECTORY . "products.xml";
								$exporter->exportProducts($exportPath);
								echo "Product xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "4":
                                                                $exportPath = self::EXPORT_DIRECTORY . "catalog.xml";
								$exporter->exportAllXml($exportPath);
								echo "All Catalog xml exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							default:
								throw new Exception(self::ERROR_EMPTY_EXPORT_FIELD);
								break;
						}
					} catch (Exception $exception) {
						$this->_errors[] = $exception->getMessage();	
					}
					break;
				case "2":
					$exporter = new ExcelCatalogExporter();
					try {
						switch($_POST["export_field"]) {
							case "1":
                                                                $exportPath = self::EXPORT_DIRECTORY . "categories.xls";
								$exporter->exportCategories($exportPath);
								echo "Category excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "2":
                                                                $exportPath = self::EXPORT_DIRECTORY . "brands.xls";
								$exporter->exportBrands($exportPath);
								echo "Brand excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "3":
                                                                $exportPath = self::EXPORT_DIRECTORY . "products.xls";
								$exporter->exportProducts($exportPath);
								echo "Product excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							case "4":
								$exportPath = self::EXPORT_DIRECTORY . "products.xls";
								$exporter->exportProducts($exportPath);
								echo "Category excel file exported to <a href='".$exportPath."' target='_blank' title='".$exportPath."' class='link'>here</a>";
								break;
							default:
								throw new Exception(self::ERROR_EMPTY_EXPORT_FIELD);
								break;
						}
					} catch (Exception $exception) {
						$this->_errors[] = $exception->getMessage();
					}
					break;
				default:
					$this->_errors[] = self::ERROR_EMPTY_EXPORT_TYPE;
					return false;
					break;
			}
		}
	}
}

class CatalogExporter
{
	
	const ERROR_IN_QUERY = "Error in query";
	const ERROR_EMPTY_CATEGORY = "There is no category to export";
	const ERROR_EMPTY_BRAND = "There is no brand to export";
	const ERROR_EMPTY_PRODUCT = "There is no product to export";
	const ERROR_EMPTY_EXPORT_TYPE = "Please choose an export type";
	const ERROR_EMPTY_EXPORT_FIELD = "Please choose an export field";
	
	protected $categoryData;
	protected $brandData;
	protected $productData;
	
	private $db;
	
	function __construct() {
		$this->db = Db::getInstance();
		$this->categoryData =  array();
		$this->brandData = array();
		$this->productData = array();
	}
	
	protected function prepareCategories($productId = 0) {
		if($productId>0) {
			$query = "SELECT c.id_category,c.id_parent,c.active,l.name FROM ps_product p
			LEFT JOIN ps_category_product cp ON (p.id_product = cp.id_product)
			LEFT JOIN ps_category c ON (cp.id_category = c.id_category)
			LEFT JOIN ps_category_lang AS l ON (c.id_category = l.id_category)
			WHERE p.id_product = ".intval($productId)." AND l.id_lang = 1";
		} else {
			$query = "SELECT c.id_category,c.id_parent,c.active,l.name FROM ps_category AS c 
			INNER JOIN ps_category_lang AS l ON (c.id_category = l.id_category) WHERE l.id_lang = 1";
		}
		$this->categoryData = $this->db->ExecuteS($query);
		if(!$this->categoryData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
	
	protected function prepareBrands($productId = 0) {
		if($productId>0) {
			$query = "SELECT m.id_manufacturer,m.name FROM ps_product p
			LEFT JOIN ps_manufacturer m ON (p.id_manufacturer = m.id_manufacturer)";
		} else {
			$query = "SELECT id_manufacturer,name FROM ps_manufacturer";
		}
		$this->brandData = $this->db->ExecuteS($query);
		if(!$this->brandData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
	
	protected function prepareProducts() {
		$query = "SELECT p.id_product,pl.name,p.active,p.price*(100+pt.rate)/100 as price,pt.rate as tax,p.quantity
	 	FROM ps_product p
		LEFT JOIN ps_tax pt ON (p.id_tax = pt.id_tax)
		LEFT JOIN ps_product_lang pl ON (p.id_product=pl.id_product)
		WHERE pl.id_lang=1";
		$this->productData = $this->db->ExecuteS($query);
		if(!$this->productData) {
			throw new Exception(self::ERROR_IN_QUERY);
		}
	}
	
	protected function getCategoriesOfProduct($productId) {
		$query = "SELECT id_category FROM ps_category_product WHERE id_product=".intval($productId);
		$categories = $this->db->ExecuteS($query);
		if(!$categories) {
			//$this->throwException(self::ERROR_IN_QUERY);
			return array();
		}
		return $categories;
	}
	
	protected function getBrandsOfProduct($productId) {
		$query = "SELECT m.id_manufacturer FROM ps_product p
			LEFT JOIN ps_manufacturer m ON (p.id_manufacturer = m.id_manufacturer) WHERE p.id_product=".$productId;
		$brands = $this->db->ExecuteS($query);
		if(!$brands) {
			return array();
		}
		return $brands;
	}
	
	protected function getAttributesOfProduct($productId) {
		$query = "SELECT
			agl.`name` AS attribute_name, 
			al.`name` AS attribute_value,
			p.price * (100 + pt.rate) / 100 + pa.price AS attribute_price,
			pa.id_product_attribute AS attribute_id
			
		FROM `ps_product` p 
			LEFT JOIN  ps_tax pt ON p.id_tax = pt.id_tax
			LEFT JOIN `ps_product_attribute` pa ON p.id_product=pa.id_product
			LEFT JOIN `ps_product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute` 
			LEFT JOIN `ps_attribute` a ON a.`id_attribute` = pac.`id_attribute` 
			LEFT JOIN `ps_attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
			LEFT JOIN `ps_attribute_lang` al ON a.`id_attribute` = al.`id_attribute` 
			LEFT JOIN `ps_attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group` 
			WHERE pa.`id_product` = ".$productId." AND al.`id_lang` = 1 AND agl.`id_lang` = 1 ORDER BY pa.`id_product_attribute`";
		$dbAttributes = $this->db->ExecuteS($query);
		if(!$dbAttributes) {
			return array();
		}
		$attributes = array();
		foreach($dbAttributes as $dbAttribute) {
			$attribute = array();
			$attribute["name"] = $dbAttribute["attribute_name"];
			$attribute["value"] = $dbAttribute["attribute_value"];
			$attribute["price"] = $dbAttribute["attribute_price"];
			if(!array_key_exists($dbAttribute["attribute_id"],$attributes)) {
				$attributes[$dbAttribute["attribute_id"]] = array();
			}
			$attributes[$dbAttribute["attribute_id"]][] = $attribute;
			unset($attribute);
		}
		return $attributes;
	}
	
	protected function getTagsOfProduct($productId) {
		$query = "SELECT t.name FROM ps_product_tag pt
			LEFT JOIN ps_tag t ON pt.id_tag=t.id_tag
			WHERE pt.id_product=".$productId." and t.id_lang=1";
		$tags = $this->db->ExecuteS($query);
		if(!$tags) {
			return array();
		}
		return $tags;
	}
	
	protected function getImagesOfProduct($productId) {
		$query = "SELECT i.`id_image` FROM `ps_image` i
		WHERE i.`id_product` = ".$productId."
		ORDER BY i.`position`
		";
		$images = $this->db->ExecuteS($query);
		if(!$images) {
			return array();
		}
		return $images;
	}
	
}

class ExcelCatalogExporter extends CatalogExporter {
	
	public $excelCreatedTime;
	
	function __construct() {
		parent::__construct();
		require_once 'Spreadsheet/Excel/Writer.php';
		$this->excelCreatedTime = time();
	}
	
	public function exportCategories($exportPath) {
		$this->prepareCategories();
		if(empty($this->categoryData)) {
			throw new Exception(CatalogExporter::ERROR_EMPTY_CATEGORY);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Categories");
		$sheet->write(0,0,"ID");
		$sheet->write(0,1,"NAME");
		$sheet->write(0,2,"PARENTID");
		$sheet->write(0,3,"STATUS");

		$rowCount = 1;
		foreach($this->categoryData as $categoryRow) {
			$sheet->write($rowCount,0,$categoryRow["id_category"]);
			$sheet->write($rowCount,1,$categoryRow["name"]);
			$sheet->write($rowCount,2,$categoryRow["id_parent"]);
			$sheet->write($rowCount,3,$categoryRow["active"]);
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportBrands($exportPath) {
		$this->prepareBrands();
		if(empty($this->brandData)) {
			throw new Exception(CatalogExporter::ERROR_EMPTY_BRAND);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Brands");
		$sheet->write(0,0,"ID");
		$sheet->write(0,1,"NAME");
		$sheet->write(0,2,"STATUS");
		
		$rowCount = 1;
		foreach($this->brandData as $brandRow) {
			$sheet->write($rowCount,0,$brandRow["id_manufacturer"]);
			$sheet->write($rowCount,1,$brandRow["name"]);
			$sheet->write($rowCount,2,"1");
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportProducts($exportPath) {
		$this->prepareProducts();
		if(empty($this->productData)) {
			throw new Exception(CatalogExporter::ERROR_EMPTY_PRODUCT);
		}
		
		$writer = new Spreadsheet_Excel_Writer($exportPath);
		$sheet = $writer->addWorksheet("Products");
		$sheet->write(0,0,"ID");
		$sheet->write(0,1,"NAME");
		$sheet->write(0,2,"CATEGORY1");
		$sheet->write(0,3,"CATEGORY2");
		$sheet->write(0,4,"CATEGORY3");
		$sheet->write(0,5,"BRAND1");
		$sheet->write(0,6,"BRAND2");
		$sheet->write(0,7,"BRAND3");
		$sheet->write(0,8,"STATUS");
		$sheet->write(0,9,"PRICE");
		$sheet->write(0,10,"TAX");
		$sheet->write(0,11,"QUANTITY");
		$sheet->write(0,12,"LABEL1");
		$sheet->write(0,13,"LABEL2");
		$sheet->write(0,14,"LABEL3");
		$sheet->write(0,15,"LABEL4");
		$sheet->write(0,16,"LABEL5");
		$sheet->write(0,17,"ATTRIBUTE_NAME_1");
		$sheet->write(0,18,"ATTRIBUTE_VALUE_1");
		$sheet->write(0,19,"ATTRIBUTE_PRICE_1");
		$sheet->write(0,20,"ATTRIBUTE_NAME_2");
		$sheet->write(0,21,"ATTRIBUTE_VALUE_2");
		$sheet->write(0,22,"ATTRIBUTE_PRICE_2");
		$sheet->write(0,23,"ATTRIBUTE_NAME_3");
		$sheet->write(0,24,"ATTRIBUTE_VALUE_3");
		$sheet->write(0,25,"ATTRIBUTE_PRICE_3");
		$sheet->write(0,26,"ATTRIBUTE_NAME_4");
		$sheet->write(0,27,"ATTRIBUTE_VALUE_4");
		$sheet->write(0,28,"ATTRIBUTE_PRICE_4");
		$sheet->write(0,29,"IMAGE1");
		$sheet->write(0,30,"IMAGE2");
		$sheet->write(0,31,"IMAGE3");
		$sheet->write(0,32,"IMAGE4");
		$rowCount = 1;
		foreach($this->productData as $productRow) {
			$sheet->write($rowCount,0,$productRow["id_product"]);
			$sheet->write($rowCount,1,$productRow["name"]);
			$sheet->write($rowCount,8,$productRow["active"]);
			$sheet->write($rowCount,9,$productRow["price"]);
			$sheet->write($rowCount,10,$productRow["tax"]);
			$sheet->write($rowCount,11,$productRow["quantity"]);
			
			$categoryData = $this->getCategoriesOfProduct($productRow["id_product"]);
			for($i=0;$i<count($categoryData) && $i<3;$i++) {
				$categoryRow = $categoryData[$i];
				$sheet->write($rowCount,2+$i,$categoryRow["id_category"]);
			}
			
			$brandData = $this->getBrandsOfProduct($productRow["id_product"]);
			for($i=0;$i<count($brandData) && $i<3;$i++) {
				$brandRow = $brandData[$i];
				$sheet->write($rowCount,5+$i,$brandRow["id_manufacturer"]);
			}
			
			$labelData  = $this->getTagsOfProduct($productRow["id_product"]);
			for($i=0;$i<count($labelData) && $i<5;$i++) {
				$labelRow = $labelData[$i];
				$sheet->write($rowCount,12+$i,$labelRow["name"]);
			}
			
			$attributeData = $this->getAttributesOfProduct($productRow["id_product"]);
			$j=0;
			for($i=0;$i<count($attributeData) && $i<4;$i++) {
				$attributeRow = next($attributeData);
				if($attributeRow) {
					foreach($attributeRow as $attribute) {
						$sheet->write($rowCount,17+$i*3,$attribute["name"]);
						$sheet->write($rowCount,18+$i*3,$attribute["value"]);
						$sheet->write($rowCount,19+$i*3,$attribute["price"]);
					}
				}
			}
			
			$imageData = $this->getImagesOfProduct($productRow["id_product"]);
			for($i=0;$i<count($imageData) && $i<4;$i++) {
				$imageRow = $imageData[$i];
				$sheet->write($rowCount,29+$i,_PS_BASE_URL_.__PS_BASE_URI__."img/p/".$productRow["id_product"]."-".$imageRow["id_image"].".jpg");
			}
			
			$rowCount++;
		}
		
		if(!$writer->close()) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
}

class XmlCatalogExporter extends CatalogExporter {
	
	private $xmlCategoryData;
	private $xmlBrandData;
	private $xmlProductData;
	
	public $xmlCreatedTime;
	
	function __construct() {
		parent::__construct();
		$this->xmlCategoryData = "";
		$this->xmlBrandData = "";
		$this->xmlProductData = "";
		$this->xmlCreatedTime = time();
	}
	
	protected function getCategoryXml($productId = 0,$allXml = false) {
		$this->prepareCategories($productId);
		if(empty($this->categoryData)) {
			throw new Exception(CatalogExporter::ERROR_EMPTY_CATEGORY);
		}
		
		$categoryXml = $allXml ? $allXml->catalog->addChild("categories") : new SimpleXmlElement("<categories></categories>");
		foreach($this->categoryData as $categoryRow) {
			$category = $categoryXml->addChild("category");
			$category->addChild("id",$categoryRow["id_category"]);
			$category->addChild("name",$categoryRow["name"]);
			$category->addChild("parentid",$categoryRow["id_parent"]);
			$category->addChild("status",$categoryRow["active"]);
		}
		
		return $allXml ? $allXml : $categoryXml;
	}
	
	protected function getBrandXml($productId = 0,$allXml = false) {
		$this->prepareBrands($productId);
		if(empty($this->brandData)) {
			throw new Exception(CatalogExporter::ERROR_EMPTY_BRANDS);
		}
		
		$brandXml = $allXml ? $allXml->catalog->addChild("brands") :  new SimpleXmlElement("<brands></brands>");
		foreach($this->brandData as $brandRow) {
			$xml = $brandXml->addChild("brand");
			$xml->addChild("id",$brandRow["id_manufacturer"]);
			$xml->addChild("name",$brandRow["name"]);
			$xml->addChild("status","1");
		}
		
		return $allXml ? $allXml : $brandXml;
	}
	
	protected function getProductXml($allXml = false) {
		$this->prepareProducts();
		if(empty($this->productData)) {
			throw new Exception(CatalogExporter::ERROR_EMPTY_PRODUCT);
		}
		
		$productXml = $allXml ? $allXml->catalog->addChild("products") : new SimpleXmlElement("<products></products>");
		foreach($this->productData as $productRow) {
			$xml = $productXml->addChild("product");
			$xml->addChild("id",$productRow["id_product"]);
			$xml->addChild("name",$productRow["name"]);
			$xml->addChild("status",$productRow["active"]);
			$xml->addChild("price",$productRow["price"]);
			$xml->addChild("currency","EUR");
			$xml->addChild("tax",$productRow["tax"]);
			$xml->addChild("quantity",$productRow["quantity"]);
			
			$categoriesXml = $xml->addChild("categories");
			$categoryData = $this->getCategoriesOfProduct($productRow["id_product"]);
			foreach($categoryData as $categoryRow) {
				$categoryXml = $categoriesXml->addChild("category_id",$categoryRow["id_category"]);
			}
			
			$brandsXml = $xml->addChild("brands");
			$brandData = $this->getBrandsOfProduct($productRow["id_product"]);
			foreach($brandData as $brandRow) {
				$brandXml = $brandsXml->addChild("brand_id",$brandRow["id_manufacturer"]);
			}
			
			$attributeCombinationsXml = $xml->addChild("attribute_combinations");
			$attributeData = $this->getAttributesOfProduct($productRow["id_product"]);
			foreach($attributeData as $attributeRow) {
				$attributeCombinationXml = $attributeCombinationsXml->addChild("attribute_combination");
				$attributePrice = 0;
				foreach($attributeRow as $attribute) {
					$attributeXml = $attributeCombinationXml->addChild("attribute");
					$attributeXml->addChild("name",$attribute["name"]);
					$attributeXml->addChild("value",$attribute["value"]);
					$attributePrice = $attribute["price"];
				}
				$attributeCombinationXml->addChild("price",$attributePrice);
			}
			
			$labelsXml = $xml->addChild("labels");
			$labelData  = $this->getTagsOfProduct($productRow["id_product"]);
			foreach($labelData as $label) {
				$labelXml = $labelsXml->addChild("label",$label["name"]);
			}
			
			$imagesXml = $xml->addChild("images");
			$imageData = $this->getImagesOfProduct($productRow["id_product"]);
			foreach($imageData as $imageRow) {
				$imageXml = $imagesXml->addChild("image",_PS_BASE_URL_.__PS_BASE_URI__."img/p/".$productRow["id_product"]."-".$imageRow["id_image"].".jpg");
			}
		}
		
		return $allXml ? $allXml : $productXml;
	}
	
	public function exportAllXml($exportPath) {
		$rootXml = new SimpleXmlElement("<root><catalog></catalog></root>");
		$rootXml = $this->getCategoryXml(0,$rootXml);
		$rootXml = $this->getBrandXml(0,$rootXml);
		$rootXml = $this->getProductXml($rootXml);
		
		if(!file_put_contents($exportPath,$rootXml->saveXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportCategories($exportPath) {
		$categoryXml = $this->getCategoryXml();
                if(!file_put_contents($exportPath,$categoryXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportBrands($exportPath) {
		$brandXml = $this->getBrandXml();
		if(!file_put_contents($exportPath,$brandXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
	
	public function exportProducts($exportPath) {
		$productXml = $this->getProductXml();
		if(!file_put_contents($exportPath,$productXml->asXML())) {
			throw new Exception("Couldn't set permissions to write over ".$exportPath." file. Please check your file permissions");
			return false;
		}
		return true;
	}
}
?>