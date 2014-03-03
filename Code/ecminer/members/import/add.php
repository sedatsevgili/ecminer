<?php
require_once("../../utils/config.php");
if(!Lib::isMember()) {
	Lib::redirect(PATH."members/login.php");
}

include_once(PATH."headers/member.php");

Error::write();

require_once(PATH."bean/Model.php");
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#addImportForm').validate();
});
</script>

<form action="run.php?do=addImport" method="POST" name="addImportForm" id="addImportForm" enctype="multipart/form-data">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Veri Ekle</td></tr>
<tr>
	<td width="25%" align="left">Site</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"site_id","required","url","id","sites","","account_id=".$_SESSION["MemberId"],"url asc","","",array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Veri Tipi</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"import_type_id","required","name","id","import_types","","","name asc","","",array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Veri Alanı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"import_field_id","required","name","id","import_fields","","","name asc","","",array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Veri Dosyası</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="file" name="filename" id="filename" /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Ekle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/member.php");
