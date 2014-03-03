<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"ClassifierTable",
array(
	"id"=>"id",
	"name"=>"Sınıflandırıcı İsmi",
	"fileName"=>"Dosya"
),
array(
	"id"=>"",
	"name"=>"",
	"fileName"=>"'<a href=\"".PATH."static/download.php?type=classifier&fileName='.<%fileName%>.'\" target=\"_blank\">indir</a>';"
),
"classifiers",
"",
"",
"id desc",
"0,10",
"Sınıflandırıcılar",
"",
"add.php",
"edit.php?id="
);
$tc->run();

include_once(PATH."footers/admin.php");