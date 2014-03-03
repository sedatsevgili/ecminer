<?php
function showCatalogSubForm($exportField) {
	?>
	<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
        <td class="dataTableContent" align="center" width="20%">Please choose an export field: </td>
        <td class="dataTableContent" align="left" width="80%">
        <select name="exportField" id="exportType" onchange="javascript:document.exportForm.submit();">
        	<option value="0">--Choose--</option>
        	<option value="categories" <?php if($exportField == "categories") { echo "selected"; }?>>Categories</option>
        	<option value="brands" <?php if($exportField == "brands") { echo "selected"; }?>>Brands</option>
        	<option value="products" <?php if($exportField == "products") { echo "selected"; }?>>Products</option>
        	<option value="all" <?php if($exportField == "all") { echo "selected"; }?>>All Catalog</option>
        </select>
        </td>
    </tr>
	<?php 
}

function showCustomerSubForm($exportField) {
	
}

function showOrderSubForm($exportField) {
	
}

function runExport($exportItem,$exportType,$exportField,$exportDirectory) {
	switch($exportItem) {
		case "catalog":
			return runCatalogExport($exportType,$exportField,$exportDirectory);
			break;
		case "customer":
			return runCustomerExport($exportType,$exportField,$exportDirectory);
			break;
		case "order":
			return runOrderExport($exportType,$exportField,$exportDirectory);
			break;
		default:
			showError("Please choose an export item");
			return false;
			break;
	}
}

function runCatalogExport($exportType,$exportField,$exportDirectory) {
	switch($exportType) {
		case "xml":
			return runXmlCatalogExport($exportField,$exportDirectory);
			break;
		case "excel":
			return runExcelCatalogExport($exportField,$exportDirectory);
			break;
		default:
			showError("Please choose an export type");
			return false;
			break;
	}
}

function runCustomerExport($exportType,$exportField,$exportDirectory) {
	
}

function runOrderExport($exportType,$exportField,$exportDirectory) {
	
}

function runExcelCatalogExport($exportField,$exportDirectory) {
	switch($exportField) {
		case "categories": 
			$exportDirectory = prepareDirectoryToExport($exportDirectory);
			if(!$exportDirectory) {
				return false;
			}
			$excelCreatedTime = time();
			$categoryRowSet = getCategories();
			if(!$categoryRowSet) {
				showError("There is no category data to export");
				return false;
			}
			break;
		case "brands":
			
			break;
		case "products":
			
			break;
		default:
			showError("Please choose an export field");
			return false;
			break;
	}
}

function runXmlCatalogExport($exportField,$exportDirectory) {
	switch($exportField) {
		case "categories":
			$exportDirectory = prepareDirectoryToExport($exportDirectory);
			if(!$exportDirectory) {
				return false;
			}
			$xmlCreatedTime = time();
			$categoryXml = getCategoryXml();
			if(!$categoryXml) {
				return false;
			}
			if(!file_put_contents($exportDirectory."/categories_".$xmlCreatedTime.".xml",$categoryXml->asXML())) {
				showError("Couldn't set permissions to write over ".$exportDirectory."/categories_".$xmlCreatedTime.".xml file. Please check your file permissions");
				return false;
			}
			return $exportDirectory."/categories_".$xmlCreatedTime.".xml";
			break;
		case "brands":
			$exportDirectory = prepareDirectoryToExport($exportDirectory);
			if(!$exportDirectory) {
				return false;
			}
			$xmlCreatedTime = time();
			$brandXml = getBrandXml();
			if(!$brandXml) {
				return false;
			}
			if(!file_put_contents($exportDirectory."/brands_".$xmlCreatedTime.".xml",$brandXml->asXML())) {
				showError("Couldn't set permissions to write over ".$exportDirectory."/brands_".$xmlCreatedTime.".xml file. Please check your file permissions");
				return false;
			}
			return $exportDirectory."/brands_".$xmlCreatedTime.".xml";
			break;
		case "products":
			$exportDirectory = prepareDirectoryToExport($exportDirectory);
			if(!$exportDirectory) {
				return false;
			}
			$xmlCreatedTime = time();
			$productXml = getProductXml();
			if(!$productXml) {
				return false;
			}
			if(!file_put_contents($exportDirectory."/products_".$xmlCreatedTime.".xml",$productXml->asXML())) {
				showError("Couldn't set permissions to write over ".$exportDirectory."/products_".$xmlCreatedTime.".xml file. Please check your file permissions");
				return false;
			}
			return $exportDirectory."/products_".$xmlCreatedTime.".xml";
			break;
		case "all":
			$exportDirectory = prepareDirectoryToExport($exportDirectory);
			if(!$exportDirectory) {
				return false;
			}
			$xmlCreatedTime = time();
			$rootXml = new SimpleXmlElement("<root><catalog></catalog></root>");
			$rootXml = getCategoryXml(0,$rootXml);
			if(!$rootXml) {
				return false;
			}
			$rootXml = getBrandXml(0,$rootXml);
			if(!$rootXml) {
				return false;
			}
			$rootXml = getProductXml($rootXml);
			if(!$rootXml) {
				return false;
			}
			if(!file_put_contents($exportDirectory."/catalog_".$xmlCreatedTime.".xml",$rootXml->asXML())) {
				showError("Couldn't set permissions to write over ".$exportDirectory."/catalog_".$xmlCreatedTime.".xml file. Please check your file permissions");
				return false;
			}
			return $exportDirectory."/catalog_".$xmlCreatedTime.".xml";
			break;
		default:
			showError("Please choose an export field");
			break;
	}
}

function getBrandXml($productId = 0,$allXml = false) {
	$brandRowSet = getBrands($productId);
	if(!$brandRowSet) {
		showError("There is no brand data to export");
		return false;
	}
	$brandXml = $allXml ? $allXml->catalog->addChild("brands") : new SimpleXmlElement("<brands></brands>");
	foreach($brandRowSet as $brandRow) {
		$xmlNode = $brandXml->addChild("brand");
		$xmlNode->addChild("id",$brandRow["manufacturers_id"]);
		$xmlNode->addChild("name",$brandRow["manufacturers_name"]);
		$xmlNode->addChild("status","1");
	}
	return $allXml ? $allXml : $brandXml;
}

function getCategoryXml($productId = 0,$allXml = false) {
	$categoryRowSet = getCategories($productId);
	if(!$categoryRowSet) {
		showError("There is no category data to export");
		return false;
	}
	$categoryXml = $allXml ? $allXml->catalog->addChild("categories") : new SimpleXmlElement("<categories></categories>");
	foreach($categoryRowSet as $categoryRow) {
		$xmlNode = $categoryXml->addChild("category");
		$xmlNode->addChild("id",$categoryRow["categories_id"]);
		$xmlNode->addChild("name",$categoryRow["categories_name"]);
		$xmlNode->addChild("parentid",$categoryRow["parent_id"]);
		$xmlNode->addChild("status","1");
	}
	return $allXml ? $allXml : $categoryXml;
}

function getProductXml($allXml = false) {
	$productRowSet = getProducts();
	if(!$productRowSet) {
		showError("There is no product data to export");
		return false;
	}
	$productXml = $allXml ? $allXml->catalog->addChild("products") : new SimpleXmlElement("<products></products>");
	foreach($productRowSet as $productRow) {
		$xmlNode = $productXml->addChild("product");
		$xmlNode->addChild("id",$productRow["products_id"]);
		$xmlNode->addChild("name",$productRow["products_name"]);
		$xmlNode->addChild("status",$productRow["products_status"]);
		$xmlNode->addChild("price",$productRow["products_price"]);
		$xmlNode->addChild("currency","USD");
		$xmlNode->addChild("tax",$productRow["tax_rate"]);
		$xmlNode->addChild("quantity",$productRow["products_quantity"]);
		
		$categoriesXml = $xmlNode->addChild("categories");
		$categoryRowSet = getCategories($productRow["products_id"]);
		if($categoryRowSet) {
			foreach($categoryRowSet as $categoryRow) {
				$categoriesXml->addChild("category_id",$categoryRow["categories_id"]);
			}
		}
		
		$brandsXml = $xmlNode->addChild("brands");
		$brandRowSet = getBrands($productRow["products_id"]);
		if($brandRowSet) {
			foreach($brandRowSet as $brandRow) {
				$brandXml = $brandsXml->addChild("brand_id",$brandRow["manufacturers_id"]);
			}
		}
		
		$attributeCombinationsXml = $xmlNode->addChild("attribute_combinations");
		$attributeData = getAttributesOfProduct($productRow["products_id"]);
		if($attributeData) {
			foreach($attributeData as $attributeRow) {
				$attributeCombinationXml = $attributeCombinationsXml->addChild("attribute_combination");
				$attributeXml = $attributeCombinationXml->addChild("attribute");
				$attributeXml->addChild("name",$attributeRow["products_options_name"]);
				$attributeXml->addChild("value",$attributeRow["products_options_values_name"]);
				$attributeCombinationXml->addChild("price",$attributeRow["option_price"]);
			}
		}
		
		$xmlNode->addChild("labels");	//there is no label data in oscommerce
		$imagesXml = $xmlNode->addChild("images");
		$imagesXml->addChild("image",HTTP_SERVER.DIR_WS_CATALOG."images/".$productRow["products_image"]);
	}
	return $allXml ? $allXml : $productXml;
}

function getBrands($productId = 0) {
	$query = "SELECT m.manufacturers_id,m.manufacturers_name FROM manufacturers m";
	if($productId>0) {
		$query = "SELECT m.manufacturers_id FROM manufacturers m
INNER JOIN products p ON m.manufacturers_id=p.manufacturers_id
WHERE p.products_id=".intval($productId);
	}
	return getRowSet(mysql_query($query));
}

function getCategories($productId = 0) {
	$query = "SELECT c.categories_id,cd.categories_name,c.parent_id FROM categories c 
	INNER JOIN categories_description cd ON c.categories_id=cd.categories_id WHERE cd.language_id=1";
	if($productId > 0) {
		$query = "SELECT categories_id FROM products_to_categories WHERE products_id=".intval($productId);
	}
	return getRowSet(mysql_query($query));
}

function getAttributesOfProduct($productId) {
	$query = "
	SELECT IF(STRCMP(pa.price_prefix,'+') = 0,p.products_price+pa.options_values_price,p.products_price-pa.options_values_price) AS option_price,po.products_options_name,pov.products_options_values_name FROM products p
INNER JOIN products_attributes pa ON p.products_id=pa.products_id
INNER JOIN products_options po ON pa.options_id=po.products_options_id
INNER JOIN products_options_values pov ON pa.options_values_id=pov.products_options_values_id
WHERE p.products_id=".intval($productId)." AND po.language_id=1 AND pov.language_id=1
	";
	return getRowSet(mysql_query($query));
}

function getProducts() {
	$query = "
	SELECT p.products_id,pd.products_name,products_status,p.products_price,tr.tax_rate,p.products_quantity,p.products_image  FROM products p

INNER JOIN products_description pd ON p.products_id = pd.products_id
LEFT JOIN tax_rates tr ON p.products_tax_class_id = tr.tax_rates_id

WHERE pd.language_id=1
	";
	return getRowSet(mysql_query($query));
}

function getRowSet($queryResult) {
	if(!$queryResult) {
		showError("Error in query");
		return false;
	}
	$rowSet = array();
	while($row = mysql_fetch_assoc($queryResult)) {
		$rowSet[] = $row;
	}
	return $rowSet;
}

function prepareDirectoryToExport($directory) {
	$exportDir = basename($directory);
	if(!is_dir($exportDir)) {
		if(!mkdir($exportDir)) {
			showError("Couldn't create ".$exportDir." directory. Please check your file permissions");
			return false;
		}
	}
	if(!chmod($exportDir,777)) {
		showError("Couldn't set permissions to write over ".$exportDir." directory. Please check your file permissions");
		return false;
	}
	return $exportDir;
}

function showError($errorMessage) {
	echo "<div style='color:red; font-weight:bold;'>".$errorMessage."</div>";
}

function showMessage($message) {
	echo "<div style='font-weight:bold;'>".$message."</div>";
}