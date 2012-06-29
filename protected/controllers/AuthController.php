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
        $login = Yii::app()->request->getParam('login', false);
        $password = Yii::app()->request->getParam('pass', false);
        
        $result = array('result' => 0);
        
        $user = Users::model()->findByAttributes(array('login'=>$login));
        if($user===null) {
            $result['message'] = 'no user found';
        }
        else {
            if($user->password !== md5($password)) {
                $result['message'] = 'wrong password';
            }
            else {
                $result['result'] = 1;
                $result['sid'] = $this->_startSession($user->id);
            }
        }
        
        $this->_sendResponse(200, CJSON::encode($result));
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
        if ($user) $session->delete();

        Yii::app()->session['sid'] = false;
        
        return true;
    }
    
    public function actionLogout() {
        $sid = Yii::app()->request->getParam('sid', false);
        $this->_sendResponse(200, CJSON::encode(array(
            'result' => $this->_stopSession($sid)
        )));
    }
}

?>
