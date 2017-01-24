<?php 
require_once 'func.php';
require_once 'User.php';
require_once 'Apps.php';

User::checkLoggedIn();

if(isset($_POST["submitXml"])){
	$User = new User();
	echo $User->uploadToXML();
}
?>

<html>
<head>
<link href="style.css" rel="stylesheet" type="text/css">

</head>

<body>

<?php
ViewPages::bannerPrint();
ViewPages::MenuPrint();

?>
 <td valign='top' align='center'>     
<?php

$Apps = new Apps(User::sessionValue("user_id"));
echo $Apps->selectApps();
	
if (isset($_SESSION['errSelectApps']) AND $_SESSION['errSelectApps'] ==  "err"){
	echo "<b>Не выбрана заявка.<b><br><br>";
}
	
ViewPages::footerPrint(); 
?>

</body>
</html>

