<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}


include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/Site.php");
$id = intval($_GET["id"]);
$site = new Site($db);
try {
	$site->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editSiteForm').validate();
});

</script>

<form action="run.php?do=updateSite&id=<?php echo $id;?>" method="POST" name="editSiteForm" id="editSiteForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Site Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Kullanıcı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">
		<?php echo Model::getSelectBox($db,"account_id","required","username","id","accounts","","","username asc","",$site->account_id,array("name"=>"--Seçiniz--","value"=>"0"));?>
	</td>
</tr>
<tr>
	<td width="25%" align="left">Adres</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" name="url" id="url" class="required" value="<?php echo $site->url;?>"/></td>
</tr>
<tr>
	<td width="25%" align="left">Durumu</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">Aktif<input type="radio" name="status" value="1" <?php if($site->status == "1") { echo "checked"; } ?> /> Pasif<input type="radio" name="status" value="0" <?php if($site->status == "0")  { echo "checked"; }?> /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Kaydet" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
