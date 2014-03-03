<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

include_once(PATH."controller/TableController.php");

$tc = new TableController($db,"AccountTable",
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
"Hesaplar <a style='float: right;margin-right: 10px;' href='list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/admin.php");