<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();
include_once(PATH."controller/TableController.php");

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
"0,10",
"Veriler <a style='float: right;margin-right: 10px;' href='list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/admin.php");