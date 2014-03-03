<?php
require_once("../../utils/config.php");
if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");

Error::write();
include_once(PATH."controller/AjaxTableController.php");

$tc = new AjaxTableController($db,"SiteTable",
array(
	"s.id"=>"id",
	"s.url"=>"Adres",
	"s.create_time"=>"Eklenme Tarihi",
	"s.status"=>"Durum"
),
array(
	"id"=>"",
	"url"=>"",
	"create_time"=>"",
	"status"=>"<%status%> == 1 ? 'Aktif' : 'Pasif';"
),
"sites s",
"INNER JOIN accounts a ON (s.account_id=a.id)",
"s.account_id=".$_SESSION["MemberId"],
"s.id desc",
"0,10",
"Siteler",
"",
"add.php",
"edit.php?id="
);
$tc->run();


include_once(PATH."footers/member.php");