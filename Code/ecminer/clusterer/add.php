<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#addClustererForm').validate();
});
</script>

<form action="run.php?do=addClusterer" method="POST" name="addClustererForm" id="addClustererForm" enctype="multipart/form-data">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Kümelendirici Ekle</td></tr>
<tr>
	<td width="25%" align="left">Kümelendirici Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="clusterername" id="clusterername" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Kümelendirici Dosyası</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="file" name="filename" id="filename" /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Ekle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
?>