<?php


/**
 * Контроллер восстановления пароля
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PasswordController extends AjaxController{
    
    /**
     * Восстановить пароль
     */
    public function actionRemember() {
        $email = Yii::app()->request->getParam('email', false);
        
        $user = Users::model()->findByAttributes(array('email'=>$email));
        if (!$user) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу найти пользователя с заданным емейл'
            )));
        }
        
        $password = md5(time());
        
        $user->password = md5($password);
        $user->save();
        
        // отправляем нотификацию
        $message = "{$user->email}, ваш новый пароль {$password}";
        MailSender::send($email, 'Skiliks : восстановление пароля', $message, 
                'skiliks', 'info@skiliks.com');
        
        $result = array(
            'result' => 1
        );
        $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
