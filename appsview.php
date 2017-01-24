<?php 
require_once 'func.php';
require_once 'User.php';
require_once 'Apps.php';
require_once 'db.php';

$Error = "";
$nameapps = "";
$phone = "";
$content = "";
$login = "";
$image = "";
$disabled = false;

User::checkLoggedIn();

if ($_SESSION['type_id'] == 1) {
	if (!isset($_POST["submitSelect"])) {
		$_SESSION['errSelectApps'] =  "err";
		header("Location: index.php");
	} else {
		if (isset($_POST["submitSelect"]) AND isset($_POST["appsId"])) {
		    $Apps = new Apps(User::sessionValue("user_id"));
		    $data = $Apps->selectAppsById($_POST["appsId"]);
		    $nameapps = $data["nameapps"];
		    $phone =$data["phone"];
		    $content = $data["content"];
		    $login = $data["login"];
			$image = $data["image"];
			$disabled = true;
			$_SESSION['errSelectApps'] =  "";
		}
		
		if (isset($_POST["submitSelect"]) AND !isset($_POST["appsId"])) {
			$_SESSION['errSelectApps'] =  "err";
			header("Location: index.php");
		}
	}
} else {
	if (isset($_POST["submitSelect"]) AND isset($_POST["appsId"])) {
		$Apps = new Apps(User::sessionValue("user_id"));
		$data = $Apps->selectAppsById($_POST["appsId"]);
		$nameapps = $data["nameapps"];
		$phone =$data["phone"];
		$content = $data["content"];
		$login = $data["login"];
		$image = $data["image"];
		$disabled = true;
		$_SESSION['errSelectApps'] =  "";
		
	} else {
		if (isset($_POST["submitSelect"]) AND !isset($_POST["appsId"])) {
			$_SESSION['errSelectApps'] =  "err";
			header("Location: index.php");
		}
		$login = User::sessionValue("user_login");
    }	
}


if (isset($_POST["submitcanselapps"])){
	header("Location: index.php");
}
	
if (isset($_POST["submitsaveapps"])){
	$Apps = new Apps(User::sessionValue("user_id"));
    $Error = $Apps->checkAppsData($_POST["nameapps"], $_POST["phone"], $_POST["content"]);
	$nameapps = $_POST["nameapps"];
    $phone = $_POST["phone"];
    $content = $_POST["content"];
	$login = User::sessionValue("user_id");
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
  
<td>
<div id="infodata">
</div> 
<form name="myForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype='multipart/form-data'>
    <table width = '95%' border = '0' align='center' cellpadding='0' cellspacing='0' style="margin-left:20px;margin-top:20px;margin-right: 10px;" >
        <tr><td width ='100px'><b>Пользователь</b>
            <td><input type='text' name='userapps' disabled style='width:337px;margin-top:20px;' maxlength='20'
			    value = "<?php echo $login;?>">
                <input type="hidden" name='userID' value = "<?php echo User::sessionValue("user_id");?>"><br><br>        
		<tr><td width ='100px'><b>Название заявки</b>
            <td><input type='text' name='nameapps' <?php if ($disabled) echo "disabled"; ?> style='width:337px;margin-top:20px;' maxlength='20'
			    value = "<?php echo isset($nameapps) ? $nameapps : "" ; ?>"> <br><br>
        <tr><td><b>Контактный телефон</b>
            <td><input type='text' name='phone' <?php if ($disabled) echo "disabled"; ?> style='width:337px;margin-top:20px;' maxlength='20'
			    value = "<?php echo isset($phone) ? $phone : "" ; ?>"><br><br>
        <tr><td><b>Краткое описание проблемы</b>
            <td><textarea rows="25" cols="45" <?php if ($disabled) echo " disabled ";?> name="content"><?php echo isset($content) ? $content : "" ;?></textarea><br><br>
        <tr><td><b>Изображение</b>
            <td><?php if (!$disabled) {
				          echo "<input type='file' name='uploadfile' accept='image/*,image/jpeg'><br><br>";
			          } else {
						  if ($image != "") echo "<a href=".$image." target='_blank'>Просмотр</a><br><br>";
					  }
				 ?>
		<tr><td><td><?php if (!$disabled) echo "<input name = 'submitsaveapps' type = 'submit' value = 'Сохранить'>";?>
                    <input name = "submitcanselapps" type = "submit" value = "Отмена">		
</table>
	  
<?php
    echo "<br><p align = 'center'><b>".$Error."</b></p>";
    ViewPages::footerPrint(); 
?>

</body>
</html>


