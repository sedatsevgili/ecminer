<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

include_once(PATH."controller/TableController.php");

$tc = new TableController($db,"ClassifierTable",
array(
	"id"=>"id",
	"name"=>"Sınıflandırıcı İsmi"
),
array(
	"id"=>"",
	"name"=>""
),
"classifiers",
"",
"",
"id desc",
"0,10",
"Sınıflandırıcılar <a style='float: right;margin-right: 10px;' href='list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/admin.php");