<?php
class CoreException extends Exception {
	
	const ERROR_COUNT_OF_ATTRIBUTES_DONT_MATCH = "Özellik sayısı eşleşmiyor";
	const ERROR_DATASET_OBJECT_IS_NOT_VALID = "DataSet nesnesi geçerli değil";
	const ERROR_ATTRIBUTE_NOT_FOUND = "Özellik bulunamadı";
	
	function __construct($message) {
		parent::__construct($message);
	}
}