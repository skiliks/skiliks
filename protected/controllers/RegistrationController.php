<?php


include_once('protected/controllers/AjaxController.php');

/**
 * Контроллер регистрации
 *
 * @author dorian
 */
class RegistrationController extends AjaxController{
    public function actionSave()
    {
        $login = $_POST['login'];
        $password = $_POST['pass1'];
        
        $dsn = "mysql:host=localhost;dbname=skiliks";
        $connection=new CDbConnection($dsn, 'root', '');
        // устанавливаем соединение. Можно попробовать try…catch возможных исключений
        $connection->active=true;
        
        $sql = "insert into users (login, password) values (:login, :password)";
        $command = $connection->createCommand($sql);
        $command->bindParam(":login", $login, PDO::PARAM_STR);
        $command->bindParam(":password", $password, PDO::PARAM_STR);
        $result = $command->execute();
        
        
	$rows = array(
            'result' => 1,
            'rows' => $result,
            "login"=>$login
        );
        
	$this->_sendResponse(200, CJSON::encode($rows));
    }
}

?>
