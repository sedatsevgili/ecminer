<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect(PATH."admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/ImportField.php");
$id = intval($_GET["id"]);
$importField = new ImportField($db);
try {
	$importField->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editImportFieldForm').validate();
});

</script>

<form action="run.php?do=updateImportField&id=<?php echo $id;?>" method="POST" name="editImportFieldForm" id="editImportFieldForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Veri Alanı Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Veri Alanı Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="importFieldname" id="importFieldname" maxlength="60" value="<?php echo $importField->name;?>"/></td>
</tr>
<tr>
	<td width="25%" align="left">Veri Alanı Sınıfı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
	<?php echo Model::getSelectBox($db,"class_id","required","name","id","import_classes","","","name asc","",$importField->import_class_id,array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Güncelle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
