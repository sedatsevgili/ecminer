<?php
require_once("../../../utils/config.php");
if(!Lib::isMember()) {
	Lib::redirect("members/login.php");
}

include_once(PATH."headers/member.php");
Error::write();

include_once(PATH."controller/AjaxTableController.php");

unset($_SESSION["DecisionTree"]);

?>

<form action="run.php?do=chooseDataSet" name="chooseDataSet" id="chooseDataSet" method="POST">
<table class="Table" width="100%" cellpadding="1" cellspacing="0"  border="0" >
<tr><td align="left" valign="top" colspan="3">
<?php 
$tc = new AjaxTableController($db,"ImportFieldTable",
array(
	"im.id"=>"id",
	"s.url"=>"Adres",
	"imt.name as typename"=>"Veri Tipi",
	"imf.name as fieldname"=>"Veri Alanı",
	"im.create_time"=>"Eklenme Tarihi",
	"im.file_size"=>"Dosya Boyutu",
	"CONCAT(im.id,im.extension) AS filename"=>"Veri"
),
array(
	"id"=>"",
	"url"=>"",
	"typename"=>"",
	"fieldname"=>"",
	"create_time"=>"",
	"file_size"=>"",
	"filename"=>"'<a href=\"".PATH."static/download.php?type=import&fileName='.<%filename%>.'\" target=\"_blank\">indir</a>';"
),
"imports im",
"
INNER JOIN import_types imt ON (im.import_type_id=imt.id)
INNER JOIN import_fields imf ON (im.import_field_id=imf.id)
INNER JOIN sites s ON (im.site_id=s.id)",
"",
"im.id desc",
"",
"Veri Seti Seçiniz",
"",
"add.php",
"",
"Import",
"",
"import_id"
);
$tc->run();
?>
</td></tr>
<tr>
	<td height="10" colspan="3">&nbsp;</td>
</tr>
<tr>
	<td align="left" colspan="3">
		<input type="checkbox" name="prep_check" id="prep_check" value="1" onclick="javascript:if(this.checked) { $('#prep_box').show(); $('#prep2_box').show(); } else { $('#prep_box').hide(); $('#prep2_box').hide(); }" /> Ön işleme yapılacak
	</td>
</tr>
<tr id="prep_box" style="display:none;">
	<td align="left" colspan="3">
		<input type="radio" name="prep_choice" value="1" checked/> Boş veri analizi yapılmayacak
		<input type="radio" name="prep_choice" value="2" style="margin-left: 15px;" />Boş veriler ortalama ile doldurulacak
	</td>
</tr>
<tr id="prep2_box" style="display:none;">
	<td align="left" colspan="3">
		<input type="radio" name="prep_error_choice" value="1" checked/> Hatalı veri analizi yapılmayacak
		<input type="radio" name="prep_error_choice" value="2" style="margin-left: 15px;" />Hatalı veriler ortalama ile doldurulacak
	</td>
</tr>
<tr>
	<td align="left" colspan="3">
		<input type="radio" name="test_choice" value="1" checked/> Test edilecek veriler yeni girilecek
		<input type="radio" name="test_choice" value="2" style="margin-left: 15px;" />Test verileri veri setinden seçilecek
	</td>
</tr>
<tr>
	<td height="10" colspan="3">&nbsp;</td>
</tr>
<tr>
	<td align="center" colspan="3">
		<input type="submit" value="Tamam" />
	</td>
</tr>
<tr>
	<td colspan="3" height="20">&nbsp;</td>
</tr>
</table>
</form>

<?php 


include_once(PATH."footers/member.php");