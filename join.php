<?php

require_once 'func.php';
require_once 'user.php';

$Error = "";

if(isset($_POST["submitcanselJoin"])){
    header("Location: login.php");
}

if(isset($_POST["submitJoin"])){
	$User = new User();
	$Error = $User->newUser($_POST["login"], $_POST["password"], $_POST["repeatpassword"]);
}

?>

<html>
<head>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<title>Регистрация</title>

<body>

<?php ViewPages::bannerPrint(); ?>
<td align="center">
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST"><br>
		
    <table>
        <tr><td>Логин</td><td><input type="text" name="login"
		value="<?php print isset($_POST["login"]) ? $_POST["login"] : "" ; ?>"
		maxlength="15"></td></tr>
		<tr><td>Пароль:</td><td><input type="password" name="password" value="" maxlength="15"></td></tr>
		<tr><td>Повторить пароль:</td><td><input type="password" name="repeatpassword" value="" maxlength="15"></td></tr>
		<tr><td>&nbsp;</td><td><input name="submitJoin" type="submit" value="Зарегистрировать">
		                       <input name = "submitcanselJoin" type = "submit" value = "Отмена"></td></tr>
		
		</table>
		</form>
</td></tr>
<td>
<?php
    echo "&nbsp;&nbsp;".<$Error."<br>";
	
	ViewPages::footerPrint();
?>
</body>
</html>