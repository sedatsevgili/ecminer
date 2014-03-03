<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect(PATH."admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/Classifier.php");
$id = intval($_GET["id"]);
$classifier = new Classifier($db);
try {
	$classifier->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editClassifierForm').validate();
});

</script>

<form action="run.php?do=updateClassifier&id=<?php echo $id;?>" method="POST" name="editClassifierForm" id="editClassifierForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Sınıflandırıcı Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Sınıflandırıcı Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="classifiername" id="classifiername" maxlength="60" value="<?php echo $classifier->name;?>"/></td>
</tr>
<tr>
	<td width="25%" align="left">Sınıflandırıcı Dosyası <a href="../static/download.php?type=classifier&fileName=<?php echo $classifier->fileName;?>" target="_blank">(indir)</a></td>
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
?>