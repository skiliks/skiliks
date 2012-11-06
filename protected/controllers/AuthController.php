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
        $user = UsersSessions::model()->findByAttributes(array('user_id'=>$uid));
        if ($user) $user->delete();
        
        $user = new UsersSessions();
        $user->user_id = $uid;
        $user->session_id = md5(time());
        $user->start_time = time();
        $user->insert();
        
        Yii::app()->session['sid'] = $user->session_id;
        
        return $user->session_id;
    }
    
    protected function _stopSession($sid) {
        $session = UsersSessions::model()->findByAttributes(array('session_id'=>$sid));
        if ($session) $session->delete();

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
