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
	$('#addAccountForm').validate();
});
</script>

<form action="run.php?do=addAccount" method="POST" name="addAccountForm" id="addAccountForm">
<table class="Table" cellpadding="1" cellspacing="0" border="0" width="100%">
<tr><td class="Header" colspan="3"> Hesap Ekle</td></tr>
<tr>
	<td width="25%" align="left">Kullanıcı Adı</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="username" id="username" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Email Adresi</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required email" name="email" id="email" maxlength="90" /></td>
</tr>
<tr>
	<td width="25%" align="left">Şifre</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="password" class="required" name="pass" id="pass" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Ad</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="firstname" id="firstname" maxlength="60" /></td>
</tr>
<tr>
	<td width="25%" align="left">Soyad</td>
	<td width="5%" align="left">:</td>
	<td width="70%" align="left"><input type="text" class="required" name="lastname" id="lastname" maxlength="60" /></td>
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
include_once(PATH."footers/admin.php");
