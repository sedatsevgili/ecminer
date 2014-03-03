<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");
Error::write();

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
"0,5",
"Hesaplar <a style='float: right;margin-right: 10px;' href='../accounts/list.php'>Tamamı</a>",
""
);
$tc->run();
echo "<p>&nbsp;</p>";
$tc = new TableController($db,"ImportTable",
array(
	"im.id"=>"id",
	"s.url"=>"Adres",
	"imt.name as typename"=>"Veri Tipi İsmi",
	"imf.name as fieldname"=>"Veri Alanı İsmi",
	"im.create_time"=>"Eklenme Tarihi",
	"im.ip_address"=>"IP Adresi",
	"im.file_size"=>"Dosya Boyutu"
),
array(
	"id"=>"",
	"url"=>"",
	"typename"=>"",
	"fieldname"=>"",
	"create_time"=>"",
	"ip_address"=>"",
	"file_size"=>""
),
"imports im",
"
INNER JOIN import_types imt ON (im.import_type_id=imt.id)
INNER JOIN import_fields imf ON (im.import_field_id=imf.id)
INNER JOIN sites s ON (im.site_id=s.id)",
"",
"im.id desc",
"0,5",
"Veriler <a style='float: right;margin-right: 10px;' href='../import/list.php'>Tamamı</a>",
""
);
$tc->run();
echo "<p>&nbsp;</p>";
$tc = new TableController($db,"SiteTable",
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
"0,5",
"Siteler <a style='float: right;margin-right: 10px;' href='../accounts/list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/admin.php");