<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect(PATH."admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#addImportTypeForm').validate();
});
</script>

<form action="run.php?do=addImportType" method="POST" name="addImportTypeForm" id="addImportTypeForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Veri Tipi Ekle</td></tr>
<tr>
	<td width="25%" align="left">Veri Tipi Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="importTypename" id="importTypename" maxlength="60" /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Ekle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
