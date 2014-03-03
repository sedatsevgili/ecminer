<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect(PATH."admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();
include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"ImportTypeTable",
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
"Veri Tipleri",
"",
"add.php",
"edit.php?id="
);
$tc->run();

include_once(PATH."footers/admin.php");