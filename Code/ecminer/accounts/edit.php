<?php
require_once("../utils/config.php");

if(!Lib::isAdmin()) {
	Lib::redirect("admin/login.php");
}

include_once(PATH."headers/admin.php");

Error::write();

require_once(PATH."bean/Account.php");
$id = intval($_GET["id"]);
$account = new Account($db);
try {
	$account->load($id);
} catch (Exception $exception) {
	Error::set($exception->getMessage());
	Error::write();
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#editAccountForm').validate();
});

function checkNewPass() {
	if($('#newpass').attr('checked')) {
		$('#pass').attr('class','required');
		$('#pass2').attr('class','required');
		$('#pass2').rules('add',{
			equalTo: '#pass'
		});
	} else {
		$('#pass').attr('class','');
		$('#pass2').attr('class','');
		$('#pass2').rules('remove',{
			equalTo: '#pass'
		});
	}
}
</script>

<form action="run.php?do=updateAccount&id=<?php echo $id;?>" method="POST" name="editAccountForm" id="editAccountForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Hesap Güncelle</td></tr>
<tr>
	<td width="25%" align="left">Kullanıcı Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="username" id="username" maxlength="60" value="<?php echo $account->username;?>"/></td>
</tr>
<tr>
	<td width="25%" align="left">Email Adresi</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required email" name="email" id="email" maxlength="90" value="<?php echo $account->email;?>" /></td>
</tr>
<tr>
	<td width="25%" align="left">Yeni Şifre İçin Seçiniz</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="checkbox" name="newpass" id="newpass" onclick="checkNewPass()" /></td>
</tr>
<tr>
	<td width="25%" align="left">Şifre</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="password" class="" name="pass" id="pass" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Şifre(Tekrar)</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="password" class="" name="pass2" id="pass2" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Ad</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="firstname" id="firstname" maxlength="60" value="<?php echo $account->firstname;?>" /></td>
</tr>
<tr>
	<td width="25%" align="left">Soyad</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="lastname" id="lastname" maxlength="60" value="<?php echo $account->lastname;?>" /></td>
</tr>
<tr>
	<td width="25%" align="left">Durumu</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left">Aktif<input type="radio" name="status" value="1" <?php if($account->status == "1") { echo "checked"; } ?> /> Pasif<input type="radio" name="status" value="0" <?php if($account->status == "0") { echo "checked"; } ?> /></td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" value="Güncelle" /></td>
</tr>
</table>
</form>

<?php 
include_once(PATH."footers/admin.php");
