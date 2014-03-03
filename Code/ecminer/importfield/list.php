<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}
include_once(PATH."headers/admin.php");

Error::write();
include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"ImportFieldTable",
array(
	"id"=>"id",
	"name"=>"Veri Alanı İsmi"
),
array(
	"id"=>"",
	"name"=>""
),
"import_fields",
"",
"",
"id desc",
"0,10",
"Veri Alanları",
"",
"add.php",
"edit.php?id="
);
$tc->run();

include_once(PATH."footers/admin.php");