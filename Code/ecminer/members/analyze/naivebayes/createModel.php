<?php
require_once("../../../utils/config.php");

if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");
Error::write();

$class = json_decode($_SESSION["NaiveBayes"]["AnalyzeClass"]);
$import_id = intval($_SESSION["NaiveBayes"]["ImportId"]);
$testChoice = intval($_SESSION["NaiveBayes"]["TestChoice"]);

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
	$dataSet = $importer->getDataSet();
}

$dataSet->selectAttributeForClass($class);

if($testChoice == 2) {
	$samples = array();
	$testSamples = array();
	foreach($dataSet->samples as $sample) {
		if(in_array($sample->id,$_SESSION["NaiveBayes"]["ModelSampleIds"])) {
			$samples[] = $sample;
		} else {
			$testSamples[] = $sample;
		}
	}
	$dataSet = new DataSet($dataSet->attributes,$dataSet->class,$samples);
	$testDataSet = new DataSet($dataSet->attributes,$dataSet->class,$testSamples);
}

?>

<script type="text/javascript">

var testSampleCount = 0;

function addTestValues() {
	var rowId = parseInt(Math.random()*3000);
	var htmlOut = '<tr class="Row" id="'+rowId+'"><td class="Cell"><a href="javascript:void(0);" onclick="removeTestValue('+rowId+')" border="0"><img src="<?php echo PATH;?>images/delete.png" width="16" height="16" /></a>';
	<?php 
	foreach($dataSet->attributes as $attribute) {
		?>
	htmlOut += '<td class="Cell">'+$('#<?php echo md5($attribute->name);?>').val()+'<input type="hidden" name="test_<?php echo md5($attribute->name);?>[]" value="'+$('#<?php echo md5($attribute->name);?>').val()+'"/></td>';
		<?php 
	}
	?>
	htmlOut += '</tr>';
	$('#test_table').append(htmlOut);
	testSampleCount++;
	$('#test_sample_count').val(testSampleCount);
	//$('#test_sample_count').val(parseInt($('#test_sample_count').val())+1);
}

function removeTestValue(rowId) {
	if(confirm('Silmek istediğinizden emin misiniz?')) {
		$('#'+rowId).remove();
		testSampleCount--;
		$('#test_sample_count').val(testSampleCount);
	}
}
</script>



<form action="run.php?do=test" method="POST" >
<input type="hidden" name="test_sample_count" id="test_sample_count" value="0" />
	<table class="Table" cellpadding="1" cellspacing="0" border="0" width="970px">
		<tr>
			<td align="center" valign="top">
<?php 
require_once(PATH."controller/DataSetTableController.php");
$tc = new DataSetTableController($db,"imports",$dataSet,"Model Veri Seti (".$importer->name.")","",PATH);
$tc->run();
?>
			</td>
		</tr>
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
		<?php if($testChoice == 1) { ?>
		<tr>
			<td align="center" valign="top">
			<table class="Table" cellpadding="1" cellspacing="0" border="0" width="970px" id="test_table">
				<tr><td class="Header" colspan="<?php echo count($dataSet->attributes)+1;?>">Test Veri Seti (<?php echo $importer->name;?>)</td></tr>
				<tr>
					<th class="Column" style="color:red;"><?php echo $class->name;?></th>
					<?php 
					foreach($dataSet->attributes as $attribute) {
						echo "<th class='Column'>".$attribute->name."</th>";
					}
					?>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top">
			<table class="Table" cellpadding="1" cellspacing="0" border="0" width="970px" id="test_table">
				<tr><td colspan="3" height="10">&nbsp;</td>
				<tr><td colspan="3" style="font-weight:bold;" align="center">Test için veri ekleyin</td></tr>
				<?php 
				foreach($dataSet->attributes as $attribute) {
					?>
					<tr>
						<td width="25%" align="left"><?php echo $attribute->name;?></td>
						<td width="5%" align="left">:</td>
						<td width="70%" align="left"><input type="text" name="<?php echo md5($attribute->name);?>" id="<?php echo md5($attribute->name);?>" /></td>
					</tr>
					<?php 
				}
				?>
				<tr>
					<td colspan="3" align="center"><input type="button" onclick="addTestValues()" value="Test Verisini Ekle" /></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="20">&nbsp;</td>
		</tr>
		<?php } else {?>
		<tr>
			<td align="center" valign="top">
			<?php 
			require_once(PATH."controller/DataSetTableController.php");
			$tc = new DataSetTableController($db,"imports",$testDataSet,"Test Veri Seti (".$importer->name.")","",PATH);
			$tc->run();
			?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td align="center" valign="top">
				<input type="submit" value="Devam" />
				<input type="button" style="margin-left: 10px;" value="Geri Dön" onclick="javascript:window.location='<?php echo PATH;?>members/analyze/naivebayes/chooseModel.php';" />
			</td>
		</tr>
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
	</table>
</form>
<?php 


include_once(PATH."footers/member.php");