<?php


include_once('protected/controllers/AjaxController.php');

/**
 * Контроллер регистрации
 *
 * @author dorian
 */
class RegistrationController extends AjaxController{
    
    /**
     * Регистрация пользоваталя.
     */
    public function actionSave()
    {
        $login = Yii::app()->request->getParam('login', false);
        $password = Yii::app()->request->getParam('pass1', false);
        $email = Yii::app()->request->getParam('email', false);
        
        $connection = Yii::app()->db;
        
        $users = new Users();
        $users->login = $login;
        $users->password = $password;
        $users->email = $email;
        $r = $users->insert();
        
	$rows = array(
            'result' => 1,
            'rows' => $r,
            "login"=>$login
        );
        
	$this->_sendResponse(200, CJSON::encode($rows));
    }
}

?>
