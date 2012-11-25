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
            $this->sendJSON(array(
                'result' => 0,
                'message' => 'Не могу найти пользователя с заданным емейл'
            ));
            return;
        }
        
        $password = md5(time());
        
        $user->password = $user->encryptPassword($password);
        $user->save();
        
        // отправляем нотификацию
        $message = "{$user->email}, ваш новый пароль {$password}";
        MailSender::send($email, 'Skiliks : восстановление пароля', $message, 
                'skiliks', 'info@skiliks.com');
        
        $result = array(
            'result' => 1
        );
        $this->sendJSON($result);
    }
}

?>
