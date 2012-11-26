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
        $password = Yii::app()->request->getParam('pass1', false);
        $password2 = Yii::app()->request->getParam('pass2', false);
        $email = Yii::app()->request->getParam('email', false);
        
        try {
            
            if (Users::model()->byEmail($email)->isActive()->find()) 
                throw new Exception("Пользователь с емейлом {$email} уже существует");
            
            if ($password != $password2)                
                throw new Exception('Введенные пароли не совпадают');
            
            $users = new Users();
            $users->password    = $users->encryptPassword($password);
            $users->email       = $email;
            $users->is_active   = 1;
            $r = (int)$users->insert();
            if ($r == 0) 
                throw new Exception('Немогу зарегистрировать пользователя');
            
            $activationCode = $this->_generateActivationCode();
            $usersActivationCode = new UsersActivationCode();
            $usersActivationCode->uid = $users->id;
            $usersActivationCode->code = $activationCode;
            $usersActivationCode->insert();
            
            // Добавить группы пользователей
            UserService::addGroupToUser($users->id, 1);
            //UserService::addGroupToUser($users->id, 2);
            
            
            Logger::debug("activation code : {$activationCode}");

            // отправляем пользователю уведомление что все хорошо
            if (!$this->_notifyUser(array(
                'email' => $users->email,
                'password' => $password,
                'uid' => $users->id,
                'code' => $activationCode
            ))) 
                throw new Exception("Немогу отправить емейл пользователю {$users->email}");

            $rows = array('result' => 1, 'rows' => $r, "email"=>$email);
            return $this->sendJSON($rows);
            
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage()
            )));
        }

    }
    
    /**
     * Активация пользователя по коду
     */
    public function actionActivate() {
        $code = Yii::app()->request->getParam('code', false);
        
        try {
            $model = UsersActivationCode::model()->byCode($code)->find();
            if (!$model) throw new Exception('Немогу найти пользователя по данному коду');
            
            $user = Users::model()->byId($model->uid)->find();
            if (!$user) throw new Exception('Не могу найти пользователя');
            
            // если пользователь уже активирован
            if ($user->is_active == 1) {
                return $this->_sendResponse(200, 'Аккаунт уже активирован', 'text/html');
            }
            
            $user->is_active = 1;
            $user->save();
            
            $url = Yii::app()->params['frontendUrl'].'index.html?message=Поздравляю, вы успешно активированы';
            $this->redirect($url);
            
            return $this->_sendResponse(200, 'Поздравляю, вы успешно активированы', 'text/html');
            
        } catch (Exception $exc) {
            return $this->_sendResponse(200, $exc->getMessage(), 'text/html');
        }

        
    }
    
    /**
     * Генерация кода активации
     * @return string
     */
    protected function _generateActivationCode() {
        return md5(time() + rand(1, 1000000));
    }
    
    protected function _notifyUser($params) {
        
        $url = "http://backend.skiliks.com/index.php?r=registration/activate&code={$params['code']}";
        
        $message = "Поздравляем {$params['email']}, вы успешно зарегистрированы и ваш пароль {$params['password']}. 
        Для активации перейдите по <a href='{$url}'>ссылке</a>";
        return MailSender::send($params['email'], 'Регистрация завершена', $message, 
                'skiliks', 'info@skiliks.com');
    }
}

?>
