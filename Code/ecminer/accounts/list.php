<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");
include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"AccountTable",
array(
	"id"=>"id",
	"username"=>"Kullanıcı Adı",
	"email"=>"E-mail",
	"CONCAT(firstname,' ',lastname)"=>"Ad Soyad",
	"create_time"=>"Kayıt Tarihi",
	"status"=>"Durum"
),
array(
	"id"=>"",
	"username"=>"",
	"email"=>"",
	"CONCAT(firstname,' ',lastname)"=>"",
	"create_time"=>"",
	"status"=>"<%status%> == 1 ? 'Aktif' : 'Pasif';"
),
"accounts",
"",
"",
"id desc",
"0,10",
"Hesaplar",
"",
"add.php",
"edit.php?id="
);
$tc->run();

include_once(PATH."footers/admin.php");