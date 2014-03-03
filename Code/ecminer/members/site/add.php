<?php
require_once("../../utils/config.php");
if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}
include_once(PATH."headers/member.php");

Error::write();

require_once(PATH."bean/Model.php");
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#addSiteForm').validate();
});
</script>

<form action="run.php?do=addSite" method="POST" name="addSiteForm" id="addSiteForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Site Ekle</td></tr>
<tr>
	<td width="25%" align="left">Adres</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" name="url" id="url" class="required"/></td>
</tr>
<tr>
	<td width="25%" align="left">Durumu</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">Aktif<input type="radio" name="status" value="1" /> Pasif<input type="radio" name="status" value="0" /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Ekle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/member.php");
