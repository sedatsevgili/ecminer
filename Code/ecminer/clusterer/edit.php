<?php
require_once("../utils/config.php");
if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/Clusterer.php");
$id = intval($_GET["id"]);
$clusterer = new Clusterer($db);
try {
	$clusterer->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editClustererForm').validate();
});

</script>

<form action="run.php?do=updateClusterer&id=<?php echo $id;?>" method="POST" name="editClustererForm" id="editClustererForm"  enctype="multipart/form-data">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Kümelendirici Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Kümelendirici Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="clusterername" id="clusterername" maxlength="60" value="<?php echo $clusterer->name;?>"/></td>
</tr>
<tr>
	<td width="25%" align="left">Kümelendirici Dosyası</td>
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
