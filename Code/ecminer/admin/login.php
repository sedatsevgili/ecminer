<?php
require_once("../utils/config.php");

if(Lib::isAdmin()) {
	Lib::redirect("default.php");
}

include_once(PATH."headers/default.php");

Error::write();
?>

<form action="run.php?do=login" method="post" id="loginForm" style="margin-top:110px;">
<center>
<fieldset style="width:450px;">
<legend>Yönetici Girişi</legend>
<table width="400" cellpadding="1" cellspacing="1" border="0">
	<tr>
		<td align="left" width="120">Kullanıcı Adı: </td>
		<td align="left" width="380"><input type="text" name="username" /></td>
	</tr>
	<tr>
		<td align="left" width="120">Şifre: </td>
		<td align="left" width="380"><input type="password" name="pass" /></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" value="Giriş Yap" /></td>
	</tr>
</table>
</fieldset>
</center>
</form>
<?php 
include_once(PATH."footers/default.php");
?>