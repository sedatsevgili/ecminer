<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect(PATH."admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/ImportType.php");
$id = intval($_GET["id"]);
$importType = new ImportType($db);
try {
	$importType->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editImportTypeForm').validate();
});

</script>

<form action="run.php?do=updateImportType&id=<?php echo $id;?>" method="POST" name="editImportTypeForm" id="editImportTypeForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Veri Tipi Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Veri Tipi Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="importTypename" id="importTypename" maxlength="60" value="<?php echo $importType->name;?>"/></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Güncelle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
