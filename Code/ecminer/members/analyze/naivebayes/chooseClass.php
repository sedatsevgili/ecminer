<?php
require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

if($_SESSION["NaiveBayes"]["AnalyzeStep"] != 1) {
	Error::set("Lütfen bir veri seti seçiniz");
	Lib::redirect("members/analyze/naivebayes/default.php");
}

include_once(PATH."headers/member.php");
Error::write();

$import_id = intval($_SESSION["NaiveBayes"]["ImportId"]);
$test_choice = intval($_SESSION["NaiveBayes"]["TestChoice"]);

// import acc control
require_once(PATH."bean/Import.php");
$import = new Import($db);
if(!$import->load($import_id)) {
	Error::set("Lütfen geçerli bir veri seti seçiniz");
	Lib::redirect("members/analyze/naivebayes/default.php");
}
if($import->getAccountId() != $_SESSION["MemberId"]) {
	Error::set("Yeterli izniniz yok");
	Lib::redirect("members/analyze/naivebayes/default.php");
}

require_once(PATH."lib/importer/Importer.php");
$importer = Importer::createInstance($db,$import);

if(!empty($_SESSION["NaiveBayes"]["DataSet"])) {
	require_once(PATH."lib/core/DataSet.php");
	$dataSet = new DataSet();
	$dataSet->loadFromStdClass(json_decode($_SESSION["NaiveBayes"]["DataSet"]));
} else {
	$dataSet = $importer->getDataSet("0,1");
}

?>
<form action="run.php?do=chooseClass" method="POST">
<table class="Table" width="100%" cellpadding="1" cellspacing="0" border="0">
	<tr>
		<td class="Header" colspan="3">
			Özellikler Arasından Sınıf Seçiniz (<?php echo $importer->name;?>)
		</td>
	</tr>
	<tr>
		<td width="25%" align="left">Özellikler</td>
		<td width="5%" align="left">:</td>
		<td width="70%" align="left"><?php echo $dataSet->getSelectBoxOfAttributes("json_class","","",array(array("name"=>"--Seçiniz","value"=>"0")))?></td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<input type="submit" value="Devam Et" />
			<input type="button" value="Geri Dön" onclick="javascript: window.location='<?php echo PATH;?>members/analyze/naivebayes/default.php';" style="margin-left: 10px;"/>
		</td>
	</tr>
</table>
</form>
<?php 

include_once(PATH."footers/member.php");