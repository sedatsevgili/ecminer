<?php
require_once("../../utils/config.php");
include_once(PATH."headers/member.php");

Error::write();
?>
<form action="run.php?do=chooseClassifier" method="post">
<table class="Table" width="100%" cellpadding="0" cellspacing="1" border="0" >
	<tr class="Header">
		<td colspan="3" align="left" style="padding-left:10px;">Analiz Metodu Seçiniz</td>
	</tr>
	<tr>
		<td width="25%" align="left">Analiz Metodu</td>
		<td width="5%" align="left">:</td>
		<td width="70%" align="left">
			<?php 
			require_once(PATH."bean/Model.php");
			echo Model::getSelectBox($db,"classifier_id","","name","id","classifiers","","","name asc","","",array("name"=>"--Seçiniz--","value=0"));
			?>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="left"><input type="submit" value="Devam" /></td>
	</tr>
</table>
</form>


<?php 
include_once(PATH."footers/member.php");