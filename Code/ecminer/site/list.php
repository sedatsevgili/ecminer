<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();
include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"SiteTable",
array(
	"s.id"=>"id",
	"a.username"=>"Kullanıcı",
	"s.url"=>"Adres",
	"s.create_time"=>"Eklenme Tarihi",
	"s.status"=>"Durum"
),
array(
	"id"=>"",
	"username"=>"",
	"url"=>"",
	"create_time"=>"",
	"status"=>"<%status%> == 1 ? 'Aktif' : 'Pasif';"
),
"sites s",
"INNER JOIN accounts a ON (s.account_id=a.id)",
"",
"s.id desc",
"0,10",
"Siteler",
"",
"add.php",
"edit.php?id="
);
$tc->run();

include_once(PATH."footers/admin.php");