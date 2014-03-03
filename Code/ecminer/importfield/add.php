<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");
require_once(PATH."bean/Model.php");

Error::write();
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#addImportFieldForm').validate();
});
</script>

<form action="run.php?do=addImportField" method="POST" name="addImportFieldForm" id="addImportFieldForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Veri Alanı Ekle</td></tr>
<tr>
	<td width="25%" align="left">Veri Alanı Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="importFieldname" id="importFieldname" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Veri Alanı Sınıfı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
	<?php echo Model::getSelectBox($db,"class_id","required","name","id","import_classes","","","name asc","","",array("name"=>"--Seçiniz","value"=>"0"))?>
	</td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Ekle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
