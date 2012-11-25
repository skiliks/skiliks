<?php
/**
 * Контроллер авторизации
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class AuthController extends AjaxController
{

    /**
     * Авторизация пользователя
     */
    public function actionAuth()
    {
        $email      = Yii::app()->request->getParam('email');
        $password   = Yii::app()->request->getParam('pass');        
        $isByCookie = Yii::app()->request->getParam('is_by_cookie', false);  
        
        $result = array(
            'result'  => 0,
            'message' => 'Неизвестная ошибка.'
        );
        
        try {
            $user = Users::model()->findByAttributes(array('email' => $email));
            if(null === $user) throw new Exception('Пользователь не найден');
            
            
            if ($user->is_active == 0) {
                throw new Exception('Пользователь не активирован');
            }
            
            $identity = new BackendUserIdentity($email, $password, $isByCookie);
            if($identity->authenticate()) {
                Yii::app()->user->login($identity, 3600*12);
            } else {
                throw new Exception('Неправильное имя пользователя или пароль.');
            }
            
            $result = array(
                'result'      => 1,
                'sid'         => $this->_startSession($user->id),
                'simulations' => UserService::getGroups($user->id),
                'user-email'  => $user->email
             );

        } catch (Exception $exc) {
            $result = array(
                'message' => $exc->getMessage()
            );            
        }
        
        $this->sendJSON($result);
        
        return;
    }

    protected function _startSession($uid)
    {
        Yii::app()->session['sid'] = Yii::app()->session->sessionID;
        Yii::app()->session['uid'] = $uid;

        return Yii::app()->session->sessionID;
    }

    protected function _stopSession($sid)
    {
        Yii::app()->session->clear();
        Yii::app()->session->destroy();

        return true;
    }

    public function actionLogout()
    {
        $sid = Yii::app()->request->getParam('sid', false);
        $this->sendJSON(array(
            'result' => (int)$this->_stopSession($sid)
        ));
    }
}
