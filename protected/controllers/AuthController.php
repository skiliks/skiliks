<?php


/**
 * Контроллер авторизации
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class AuthController extends AjaxController{
    
    /**
     * Авторизация пользователя
     */
    public function actionAuth() {
        $email = Yii::app()->request->getParam('email', false);
        $password = Yii::app()->request->getParam('pass', false);
        
        
        try {
            $user = Users::model()->findByAttributes(array('email'=>$email));
            if(!$user) throw new Exception('Пользователь не найден');
            
            
            if ($user->is_active != 1) 
                throw new Exception('Пользователь не активирован');

            if($user->password !== md5($password)) 
                throw new Exception('Неверный пароль');
            
            
            
            $result = array();
            $result['result']       = 1;
            $result['sid']          = $this->_startSession($user->id);
            $result['simulations']  = UserService::getGroups($user->id);
            $this->sendJSON($result);

        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            $this->sendJSON($result);
        }
        return;
    }
    
    protected function _startSession($uid) {
        Yii::app()->session['sid'] = Yii::app()->session->sessionID;
        Yii::app()->session['uid'] = $uid;
        
        return Yii::app()->session->sessionID;
    }
    
    protected function _stopSession($sid) {
        unset(Yii::app()->session);
        Yii::app()->session['sid'] = false;
        
        return true;
    }
    
    public function actionLogout() {
        $sid = Yii::app()->request->getParam('sid', false);
        $this->sendJSON(array(
            'result' => (int)$this->_stopSession($sid)
        ));
    }
}

?>
