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
        //$login = Yii::app()->request->getParam('login', false);
        $password = Yii::app()->request->getParam('pass1', false);
        $password2 = Yii::app()->request->getParam('pass2', false);
        $email = Yii::app()->request->getParam('email', false);
        
        try {
            
            if (Users::model()->byEmail($email)->find()) 
                throw new Exception("Пользователь с емейлом {$email} уже существует");
            
            if ($password != $password2)                
                throw new Exception('Введенные пароли не совпадают');
            
            $users = new Users();
            //$users->login = $login;
            $users->password = md5($password);
            $users->email = $email;
            $r = (int)$users->insert();
            if ($r == 0) 
                throw new Exception('Немогу зарегистрировать пользователя');

            // отправляем пользователю уведомление что все хорошо
            if (!$this->_notifyUser(array(
                'email' => $users->email,
                //'login' => $users->login,
                'password' => $password
            ))) 
                throw new Exception("Немогу отправить емейл пользователю {$users->email}");

            $rows = array('result' => 1, 'rows' => $r, "login"=>$login);
            return $this->_sendResponse(200, CJSON::encode($rows));
            
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage()
            )));
        }

    }
    
    protected function _notifyUser($params) {
        $message = "Поздравляем {$params['email']}, вы успешно зарегистрированы и ваш пароль {$params['password']}";
        return MailSender::send($params['email'], 'Регистрация завершена', $message, 
                'skiliks', 'info@skiliks.com');
    }
}

?>
