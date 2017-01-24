<?php

require_once 'db.php';

class Apps
{
	private $id;
	private $name;
	private $phone;
	private $content;
	
	public function __construct($id)
	{
		$this->id = $id;
	}
	
	public function checkAppsData($name, $phone, $content)
	{
		$name = htmlspecialchars(stripslashes(trim($name)));
		$phone = htmlspecialchars(stripslashes(trim($phone)));
		$content = htmlspecialchars(stripslashes(trim($content)));
		
		if($name == '' || $name == ' ' ){ 
			unset($name);
		}
		
		if($phone == '' || $phone == ' ' ){ 
			unset($phone);
		}

		if (empty($name)) { //если пользователь не ввел "Наименование заявки", то выдаем ошибку и останавливаем скрипт
			return $data = 'Вы не ввели обязательное поле - "Наименование заявки".';
		}
		
		if (empty($phone)) { //если пользователь не ввел "Наименование заявки", то выдаем ошибку и останавливаем скрипт
			return $data = 'Вы не ввели обязательное поле - "Контакный телефон".';
		}

		if(mb_strlen($content) < 11){ 
			return $data = 'Минимальная длинна обязательное поля "Краткое описание проблемы" 10 символов.';
		}
		
		$this->name = $name; 
		$this->phone = $phone; 
		$this->content = $content; 
		
		$this->saveApp();
	}
	
	private function saveApp()
	{
		Global $pdo;
		$pcrappstmnt = $pdo->prepare(   "INSERT INTO applications("
													. "name, content, user_id, "
													. "phone, image) VALUES ( "
													. ":app_name,"
													. ":app_cont,"
													. ":user_id,"
													. ":phone,"
													. ":image)");
													
		$image = $this->uploadImg();
		
		$pcrappstmnt->execute( array(
						':app_name' 	=>	$this->name,
						':app_cont' 	=>	$this->content,
						':user_id' 		=>  $this->id,
						':phone' 		=>  $this->phone,
						':image' 		=>  $image ));

		$_SESSION['UserAddApps'] = "Add";
		header("Location: index.php");
		
		return $pdo->lastInsertId();
	}
	
	private function generateStr($length = 64) 
	{
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i <= $length; $i++) {
            $result .= $characters[mt_rand (0, strlen ($characters) - 1)];
        }
        return $result;
    }
	
	private function uploadImg()
	{
		if (isset($_FILES['uploadfile'])){
			$uploaddir = 'G:\\USR\\www\\temp_img\\';
			$file = $this->generateStr(20)."-".basename($_FILES['uploadfile']['name']);
			$uploadfile = $uploaddir.$file;	
			if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
			    $uploadresult = "Файл корректен и был успешно загружен.";
				return "temp_img/".$file;	
			} else {
				$uploadresult = "Файл не был загружен.";
				return "";	
			}
		} else {
			return "";	
		}
	}
	
	public function selectApps()
	{
		Global $pdo;
		
		$xmlButton = "";
		$selectLastAps = "";
		
		if ($_SESSION['type_id'] == 1) {
			$stmnt = $pdo->query("SELECT a.id, a.name, a.content, a.phone, a.image, u.login AS u_login
                                  FROM users u 
                                  INNER JOIN applications a ON a.user_id = u.id ORDER BY a.id DESC" );
			
			$stmnt->execute();
			$xmlButton = "&nbsp;<input name='submitXml' type = 'submit' formaction='index.php' value = 'Выгрузка XML.'>";
			
		} else {
			$stmnt = $pdo->prepare(     "SELECT a.id, a.name, a.content, a.phone, a.image, u.login AS u_login
										 FROM users u  
										 INNER JOIN applications a ON a.user_id = u.id AND a.user_id= :app_id ORDER BY a.id DESC" );
										 
			$stmnt->execute(array(':app_id' => $this->id));
			
		}
		
		$result = $stmnt->fetchAll();
		
		$html = "";
		
		if($result) {
		    $html .= "<br><H1>Заявок на ремонт</H1><br><form name='myForm1'  method='POST'>
			         <table width='94%' border='1' align='center' valign='top' cellpadding='0' cellspacing='0' style='margin-left:20px;margin-top:20px;margin-right:20px;' >
                        <tr align='center'> 
                            <td width='20px'>
                            <td width='50px'><div style='margin-left:4px;'><b>Пользователь</b></div>
                            <td width='300px'><div style='margin-left:4px;'><b>Название</b></div>
                            <td><div style='margin-left:4px;'><b>Файл</b></div>
                        </tr>";	
			
			foreach ($result as $row) {
				$image = $row['image'];
				
				if ($image != "") {
					$image = "<a href=".$image." target='_blank'>Просмотр</a>";
				} else {
					$image = "";
				}

				if (isset($_SESSION['UserAddApps']) AND $_SESSION['UserAddApps'] == "Add" AND $_SESSION['type_id'] == 2) {
					$selectLastAps = "bgcolor='#C0C0C0'";
					$_SESSION["UserAddApps"] = "";
                }
				
				$html .= "<tr valign='center' align='center' height='40' ".$selectLastAps.">
			                  <td><input type='radio' name='appsId' value=".$row['id'].">
                              <td><div style='margin-left:4px;'>".$row['u_login']."</div>
                              <td><div style='margin-left:4px;'>".$row['name']."</div>
                              <td><div style='margin-left:4px;'>".$image."</td>";
				$selectLastAps = "";			  
			}
			
			$html .= "</table>";
			
			$html .= "<br><br><input name='submitSelect' type = 'submit' value = 'Просмотр заявки.' formaction='appsview.php' method='post'>";
			
			$html .= $xmlButton."</form>";
		} else {
			$html = "<br><H1>Заявок нет</H1>";
		}

		return $html;
	}

	public function selectAppsById($appsId)
	{
		Global $pdo;
		
		$stmnt = $pdo->prepare(     "SELECT a.id, a.name, a.content, a.phone, a.image, u.login AS u_login
									 FROM users u 
								     INNER JOIN applications a ON a.user_id = u.id AND a.id = :app_id" );
		$data = array();
		
		$stmnt->execute(array(':app_id' => $appsId));
		
		$result = $stmnt->fetch();
		
		if ($result){
			$data['login'] = $result['u_login'];
			$data['phone'] = $result['phone'];
			$data['content'] = $result['content'];
			$data['nameapps'] = $result['name'];
			$data['image'] = $result['image'];
				
			return $data;	
        }				
		
		return $data;
	}


	
}

?>