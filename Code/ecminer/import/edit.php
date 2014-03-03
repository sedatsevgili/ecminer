<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/Import.php");
$id = intval($_GET["id"]);
$import = new Import($db);
try {
	$import->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editImportForm').validate();
});

</script>

<form action="run.php?do=updateImport&id=<?php echo $id;?>" method="POST" name="editImportForm" id="editImportForm" enctype="multipart/form-data">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Veri Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Site</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"site_id","required","url","id","sites","","","url asc","",$import->site_id,array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Veri Tipi</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"import_type_id","required","name","id","import_types","","","name asc","",$import->import_type_id,array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Veri Alanı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"import_field_id","required","name","id","import_fields","","","name asc","",$import->import_field_id,array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Veri</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><a href="<?php echo PATH;?>static/download.php?type=import&fileName=<?php echo $import->id.$import->extension;?>" target="_blank">İndir</a></td>
</tr>
<tr>
	<td width="25%" align="left">Veri Dosyası</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="file" name="filename" id="filename" /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Güncelle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
