<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect(PATH."admin/login.php");
}
include_once(PATH."headers/admin.php");

Error::write();
include_once(PATH."controller/TableController.php");

$tc = new TableController($db,"ImportTypeTable",
array(
	"id"=>"id",
	"name"=>"Veri Tipi İsmi"
),
array(
	"id"=>"",
	"name"=>""
),
"import_types",
"",
"",
"id desc",
"0,10",
"Veri Tipleri <a style='float: right;margin-right: 10px;' href='list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/admin.php");