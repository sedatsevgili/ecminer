<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();
include_once(PATH."controller/TableController.php");

$tc = new TableController($db,"ClustererTable",
array(
	"id"=>"id",
	"name"=>"Kümelendirici İsmi"
),
array(
	"id"=>"",
	"name"=>""
),
"clusterers",
"",
"",
"id desc",
"0,10",
"Kümelendiriciler <a style='float: right;margin-right: 10px;' href='list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/admin.php");