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
        $username = Yii::app()->request->getParam('username');
        $email = Yii::app()->request->getParam('email');
        $password = Yii::app()->request->getParam('pass');

        $result = array(
            'result' => 0,
            'message' => 'Неизвестная ошибка.'
        );

        try {

//            $user = YumUser::model()->find(
//                'upper(username) = :username',
//                [
//                    ':username' => strtoupper($username)
//                ]
//            );

            $profile = YumProfile::model()->find('email = :email', array(':email' => $email));
            $user = $profile->user;

            if($user) {
                $this->authenticate($user, $password);
            } else {
                throw new CHttpException(200, '1 Неправильное имя пользователя или пароль.');
            }

            $result = array(
                'result'      => 1,
                'sid'         => $this->_startSession($user->id),
                'simulations' => UserService::getModes($user),
            );
        } catch (CHttpException $exc) {
            $result = array(
                'message' => $exc->getMessage()
            );
        }

        $this->sendJSON($result);
    }

    public function actionCheckSession()
    {
        $user_id = Yii::app()->session['uid'];

        $user = YumUser::model()->findByPk($user_id);

        if (null === $user) {
            $this->sendJSON(array('result' => 0, 'message' => 'User not found'));
            return;
        }

        $this->sendJSON(array(
            'result'      => 1,
            'sid'         => Yii::app()->session->sessionID,
            'simulations' => UserService::getModes($user),
        ));
    }

    protected function _startSession($uid)
    {
        Yii::app()->session['sid'] = Yii::app()->session->sessionID;
        Yii::app()->session['uid'] = $uid;

        return Yii::app()->session->sessionID;
    }

    protected function _stopSession($sid)
    {
        session_id(Yii::app()->request->getParam('sid', false));
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

    public function authenticate($user, $password)
    {
        $identity = new YumUserIdentity($user->username, $password);
        $identity->authenticate();

        switch($identity->errorCode) {
            case YumUserIdentity::ERROR_EMAIL_INVALID:
                throw new CHttpException(200, '2 Неправильное имя пользователя или пароль.');
            case YumUserIdentity::ERROR_STATUS_INACTIVE:
                throw new CHttpException(200, 'Аккаунт неактивен.');
            case YumUserIdentity::ERROR_STATUS_BANNED:
                throw new CHttpException(200, 'Аккаунт заблокирован.');
            case YumUserIdentity::ERROR_STATUS_REMOVED:
                throw new CHttpException(200, 'Аккаунт удалён.');
            case YumUserIdentity::ERROR_PASSWORD_INVALID:
                throw new CHttpException(200, '3 Неправильное имя пользователя или пароль.');
        }
    }
}
