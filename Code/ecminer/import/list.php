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
	"im.id"=>"id",
	"s.url"=>"Adres",
	"imt.name as typename"=>"Veri Tipi İsmi",
	"imf.name as fieldname"=>"Veri Alanı İsmi",
	"im.create_time"=>"Eklenme Tarihi",
	"im.ip_address"=>"IP Adresi",
	"im.file_size"=>"Dosya Boyutu",
	"CONCAT(im.id,im.extension) AS filename"=>"Veri"
),
array(
	"id"=>"",
	"url"=>"",
	"typename"=>"",
	"fieldname"=>"",
	"create_time"=>"",
	"ip_address"=>"",
	"file_size"=>"",
	"filename"=>"'<a href=\"".PATH."static/download.php?type=import&fileName='.<%filename%>.'\" target=\"_blank\">indir</a>';"
),
"imports im",
"
INNER JOIN import_types imt ON (im.import_type_id=imt.id)
INNER JOIN import_fields imf ON (im.import_field_id=imf.id)
INNER JOIN sites s ON (im.site_id=s.id)",
"",
"im.id desc",
"0,10",
"Veriler",
"",
"add.php",
"",
"Import"
);
$tc->run();

include_once(PATH."footers/admin.php");