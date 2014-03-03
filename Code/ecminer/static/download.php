<?php
require_once("../utils/config.php");

if(!isset($_GET["type"]) || empty($_GET["type"])) {
	echo "<script type=\"text/javascript\">alert('Dosya tipi belirtilmemis');self.close();</script>";
	exit();
}
if(!isset($_GET["fileName"]) || empty($_GET["fileName"])) {
	echo "<script type=\"text/javascript\">alert('Dosya adi belirtilmemis');self.close();</script>";
	exit();
}

$fileName = $_GET["fileName"];

if(!Lib::isAdmin()) {
	if(!Lib::isMember()) {
		echo "<script type=\"text/javascript\">alert('Gecerli izniniz yok');self.close();</script>";
		exit();
	}
}

switch($_GET["type"]) {
	case "classifier":
		$fileName = PATH."lib/classifier/".$fileName;
		break;
	case "clusterer":
		$fileName = PATH."lib/clusterer/".$fileName;
		break;
	case "import":
		$fileValues = explode(".",$fileName);
		require_once(PATH."bean/Import.php");
		$import = new Import($db);
		if(!$import->load(intval($fileValues[0]))) {
			echo "<script type=\"text/javascript\">alert('Dosya yuklenemedi');self.close();</script>";
			exit();
		}
		$fileName = PATH."imports/".$fileName;
                require_once(PATH."bean/Site.php");
                $site = new Site($db);
                if(!$site->load(intval($import->site_id))) {
                        echo "<script type=\"text/javascript\">alert('Dosya yuklenemedi');self.close();</script>";
                        exit();
                }
                if($site->account_id != $_SESSION["MemberId"]) {
                        echo "<script type=\"text/javascript\">alert('Gecerli izniniz yok');self.close();</script>";
                        exit();
                }
		break;
	default:
		echo "Dosya tipi tanımlanamadı!";
		exit();
		break;
}

if(!file_exists($fileName)) {
	echo "dosya bulunamadı!";
	exit();
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"".basename($fileName)."\";");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($fileName));
readfile($fileName);
exit();