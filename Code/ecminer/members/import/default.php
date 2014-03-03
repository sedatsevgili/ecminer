<?php
require_once("../../utils/config.php");
if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");

Error::write();
include_once(PATH."controller/TableController.php");

$tc = new TableController($db,"ImportTable",
array(
	"im.id"=>"id",
	"s.url"=>"Adres",
	"imt.name as typename"=>"Veri Tipi İsmi",
	"imf.name as fieldname"=>"Veri Alanı İsmi",
	"im.create_time"=>"Eklenme Tarihi",
	"im.file_size"=>"Dosya Boyutu"
),
array(
	"id"=>"",
	"url"=>"",
	"typename"=>"",
	"fieldname"=>"",
	"create_time"=>"",
	"file_size"=>""
),
"imports im",
"
INNER JOIN import_types imt ON (im.import_type_id=imt.id)
INNER JOIN import_fields imf ON (im.import_field_id=imf.id)
INNER JOIN sites s ON (im.site_id=s.id)
INNER JOIN accounts a ON (s.account_id=a.id)",
"a.id=".$_SESSION["MemberId"],
"im.id desc",
"0,10",
"Veriler <a style='float: right;margin-right: 10px;' href='list.php'>Tamamı</a>",
""
);
$tc->run();

include_once(PATH."footers/member.php");