<?php
require_once("../../utils/config.php");
if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");

Error::write();
include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"ImportFieldTable",
array(
	"im.id"=>"id",
	"s.url"=>"Adres",
	"imt.name as typename"=>"Veri Tipi",
	"imf.name as fieldname"=>"Veri Alanı",
	"im.create_time"=>"Eklenme Tarihi",
	"im.file_size"=>"Dosya Boyutu",
	"CONCAT(im.id,im.extension) AS filename"=>"Veri"
),
array(
	"id"=>"",
	"url"=>"",
	"typename"=>"",
	"fieldname"=>"",
	"create_time"=>"",
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
"edit.php?id=",
"Import"
);
$tc->run();

include_once(PATH."footers/member.php");