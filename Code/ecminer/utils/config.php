<?php

define("_DBHOST_","localhost");
define("_DBUSER_","root");
define("_DBPASSWORD_","");
define("_DBNAME_","ecminer");

$rootPath = "./";
$uriValues = explode("/",$_SERVER["SCRIPT_NAME"]);
if(is_array($uriValues) && count($uriValues)>3) {
	$rootPath = "";
	for($i=3;$i<count($uriValues);$i++) {
		$rootPath .= "../";
	}
}

//define("FILEPATH","C:/wamp/www/ecminer/");
define(FILEPATH,"/var/www/ecminer/");
define("PATH",$rootPath);

$conn = mysql_connect(_DBHOST_,_DBUSER_,_DBPASSWORD_) or die("Database connection couldn't be established");
mysql_select_db(_DBNAME_,$conn) or die("Database connection couldn't be established");

ini_set('error_reporting',E_ALL ^ E_NOTICE);
session_save_path(FILEPATH."ec_sessions");
session_start();

include(PATH."utils/Error.php");
include(PATH."utils/Warning.php");
include(PATH."exceptions/ExceptionController.php");
include(PATH."utils/Lib.php");
include(PATH."utils/DB.php");

$db = new DB();

