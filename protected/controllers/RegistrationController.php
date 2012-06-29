<?php




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
        $users->password = md5($password);
        $users->email = $email;
        $r = (int)$users->insert();
        if ($r == 0) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'cant insert user'
            )));
        }
        
        // отправляем пользователю уведомление что все хорошо
        if (!$this->_notifyUser(array(
            'email' => $users->email,
            'login' => $users->login,
            'password' => $password
        ))) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'cant email user'
            )));
        }
        
	$rows = array(
            'result' => 1,
            'rows' => $r,
            "login"=>$login
        );
        
	$this->_sendResponse(200, CJSON::encode($rows));
    }
    
    protected function _notifyUser($params) {
        $message = "Поздравляем {$params['login']}, вы успешно зарегистрированы и ваш пароль {$params['password']}";
        return MailSender::send($params['email'], 'Регистрация завершена', $message, 
                'skiliks', 'info@skiliks.com');
    }
}

?>
