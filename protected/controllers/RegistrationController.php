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
        $email = Yii::app()->request->getParam('email', null);
        $password = Yii::app()->request->getParam('pass1', null);
        $passwordAgain = Yii::app()->request->getParam('pass2', 0); // to make passwords different by default  
        
        // validation: true or Error message
        $isValid = UserService::validateNewUserData($email, $password, $passwordAgain);
        if (true !== $isValid) {
            $this->_sendResponse(200, CJSON::encode(array(
                'result'  => self::STATUS_ERROR, 
                'message' => $isValid,
            )));
        }
        
        // create user
        $user = UserService::registerUser($email, $password);
        if(null === $user) {
            $this->_sendResponse(200, CJSON::encode(array(
                'result'  => self::STATUS_ERROR, 
                'message' => 'Не удалось создать пользователя.',
            )));  
        }
        
        // отправляем пользователю уведомление что все хорошо
        if (false === MailSender::notifyUser(array(
            'email'    => $user->email,
            'password' => $password,
            'uid'      => $user->id,
            'code'     => $user->activationCode
        ))) {
           $this->_sendResponse(200, CJSON::encode(array(
                'result'  => self::STATUS_ERROR, 
                'message' => "Немогу отправить емейл пользователю {$user->email}",
            ))); 
        }

        // send success responce
        return $this->sendJSON(array(
            'result' => 1, 
            'rows'   => true, // WTF? it shall be true
            "email"  =>$email
        ));
    }
    
    /**
     * Активация пользователя по коду
     */
    public function actionActivate() {
        $code = Yii::app()->request->getParam('code', false);
        
        try {
            $model = UsersActivationCode::model()->byCode($code)->find();
            if (!$model) throw new CException('Немогу найти пользователя по данному коду');
            
            $user = Users::model()->byId($model->uid)->find();
            if (!$user) throw new CException('Не могу найти пользователя');
            
            $user->is_active = true;
            $user->save();
            
            //$url = Yii::app()->params['frontendUrl'].'index.html?message=Поздравляю, ваш пользователь успешно активирован';
            //$this->redirect($url);
            
            return $this->_sendResponse(200, 'Поздравляю, ваш пользователь успешно активирован', 'text/html');
            
        } catch (CException $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }
}


