<?php

require_once 'db.php';

class User
{
	private $login;
	private $password;
	
	public function authorization($login, $password)
	{
		$data = $this->checkUser($login, $password);

		if($data["login_state"] == "access_granted"){
			session_start();
			$_SESSION['user_login'] = $data['user_login'];
			$_SESSION['user_id'] 	= $data['user_id'];
			$_SESSION['type_id'] 	= $data['type_id'];
			echo $_SESSION['user_login'];
			echo $_SESSION['user_id'];
			echo $_SESSION['type_id'];
			if($data['type_id'] == 1) {
				header("Location: index.php");
				exit;
			} elseif($data['type_id'] == 2) {
				header('Location: index.php');
				exit;
			}				
	    }			
	}
	
	public function newUser($login, $password, $repeatpassword)
	{
		$data = $this->saveNewUser($login, $password, $repeatpassword);

		if($data == "reg_successfull"){
			$this->authorization($this->login, $this->password);
		} else {
			return $data;							
	    }			
	}
	
	private function checkUser($login, $password) 
	{
		Global $pdo;
		$login = trim($login);
		if($login == '' || $login == ' '){ 
			unset($login);
		}
		if($password == ''){ 
			unset($password);
		}
		if (empty($login) || empty($password)) { //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
			$data['login_state'] = 'access_denied';
			return $data;
		}
		if(!preg_match("|^[-a-zA-Z0-9_]+$|i", $login)){
			$data['login_state'] = 'access_denied';
			return $data;
		}
		$pas = sha1($password);
		$stmnt = $pdo->prepare("SELECT * FROM users WHERE login= :login AND password= :pas");
		$stmnt->execute(array(':login' => $login,':pas' => $pas));
		$result = $stmnt->fetch();
		if($result){
			if($result['type_id'] == 1 || $result['type_id'] == 2){
				$data['user_login'] = $result['login'];
				$data['user_id'] = $result['id'];
				$data['type_id'] = $result['type_id'];
				$data['login_state'] = 'access_granted';
				return $data;
			}
			else {
				$data['login_state'] = 'access_denied';
				return $data;
				exit;
			}
		} else{
			$data['login_state'] = 'access_denied';
			return $data;
		}        
	}
	
	private function saveNewUser($login, $password, $repeatpassword) 
	{
		Global $pdo;
		$login = trim($login);
		
		if($login == '' || $login == ' ') unset($login);
		if($password == '') unset($password);
		
		if (empty($login) || empty($password)) { //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
			
			return $data = 'Вы не ввели логин или пароль.';
		}
		
		if(!preg_match( "|^[-a-zA-Z0-9_]+$|i", $login)){
			return $data = 'Логин содержит недопустимые символы.';
		}
		
		if(strcmp($password, $repeatpassword)) {
			return $data = "Ваши пароли не совпадают";
		}
		
		$stmnt = $pdo->prepare("SELECT * FROM users WHERE login=:login");
		$stmnt->execute(array(':login' => $login));
		$result = $stmnt->fetchAll();
		if (count($result)) {                        
			return $data = "Пользователь с таким логином уже существует.";
		} else {
			$pas = sha1($password);			
			$stmnt = $pdo->prepare("INSERT INTO users (login, password, type_id) VALUES(:login, :pas, '2')");
			$stmnt->execute(array(':login' => $login, ':pas' => $pas));
			
			$this->login = $login;
			$this->password = $password;
			
			return $data = "reg_successfull";
		}
	}
	
	public function uploadToXML()
	{	
        Global $pdo;	
		$stmnt = $pdo->query(   "SELECT a.id, a.name, a.content, a.phone, a.image, u.login AS u_login, u.id AS u_id
										FROM users u
										INNER JOIN applications a ON a.user_id = u.id" );
        $prepXML = "<doc></doc>";
		
		$appData = new SimpleXMLElement($prepXML);
		for($i = 0; $row = $stmnt->fetch(); $i++){
			$appData->addChild('application');
			$appData->application[$i]->addChild("id",       $row['id']);
			$appData->application[$i]->addChild("name",     $row['name']);
			$appData->application[$i]->addChild("content",  $row['content']);
			$appData->application[$i]->addChild("user",     $row['u_login'])->addAttribute("id", $row['u_id']);        
			$appData->application[$i]->addChild("image",    $row['image']);
		}
		$filename = './temp/applications.xml';
		$appData->saveXML($filename);

		$this->file_download('temp/applications.xml');

		return 'Заявки успешно выгружены на сервер в файл ['.$filename.'] в каталог по умолчанию.';  
    }

	
    public static function checkLoggedIn()
	{
		session_start();
        if ((!isset($_SESSION["user_login"])) OR (!isset($_SESSION["user_id"])) OR (!isset($_SESSION["type_id"]))){
            header("Location: login.php");
            exit;
        }
        return true;
   }
     
    public static function logoutUser() 
    {	
		session_start();
	    unset($_SESSION["user_login"]);
	    unset($_SESSION["user_id"]);
	    unset($_SESSION["type_id"]);
	    session_destroy();
	    return true;
    } 
	
	public static function sessionValue($name) 
    {	
	    return $_SESSION[$name];
    }
  
	public function file_download($filename, $mimetype='application/octet-stream') 
	{
	    if (file_exists($filename)) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
            header('Content-Type: ' . $mimetype);
            header('Last-Modified: ' . gmdate('r', filemtime($filename)));
            header('ETag: ' . sprintf('%x-%x-%x', fileinode($filename), filesize($filename), filemtime($filename)));
            header('Content-Length: ' . (filesize($filename)));
            header('Connection: close');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
            
			// Открываем искомый файл
            $f=fopen($filename, 'r');
            while(!feof($f)) {
				 // Читаем килобайтный блок, отдаем его в вывод и сбрасываем в буфер
                echo fread($f, 1024);
                flush();
            }
			// Закрываем файл
            fclose($f);
			// Удаляем файл
	        unlink($filename);
        } else {
		    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
		    header('Status: 404 Not Found');
        }
        exit;
    }

	
}

?>