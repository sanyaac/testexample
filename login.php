<?php

require_once 'func.php';
require_once 'user.php';

if(isset($_POST["submitLogin"])){
	$User = new User();
	$User->authorization($_POST["login"], $_POST["password"]);
}
?>


<html>
<head>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<title>Страница авторизации</title>

<body>

<?php ViewPages::bannerPrint(); ?>
  <td align="center">
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
	<br>
	<table>
	<tr><td>Логин</td><td><input type="text" name="login"
	value="<?php echo isset($_POST["login"]) ? $_POST["login"] : "" ; ?>"
	maxlength="15"></td></tr>
	<tr><td>Пароль:</td><td><input type="password" name="password" value="" maxlength="15"></td></tr>
	<tr><td>&nbsp;</td><td><input name="submitLogin" type="submit" value="Войти">
	<a href="join.php">Регистрация</a></td></tr>
	</table>
	</form>
  </td>
</tr>
  <td>
<?php
	ViewPages::footerPrint();
?>

</body>
</html>

