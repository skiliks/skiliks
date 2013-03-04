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
        $email = Yii::app()->request->getParam('email');
        $password = Yii::app()->request->getParam('pass');

        $result = array(
            'result' => 0,
            'message' => 'Неизвестная ошибка.'
        );

        try {
            $user = Users::model()->findByAttributes(array('email' => $email));
            if (null === $user) throw new CHttpException(200, 'Пользователь не найден');


            if ($user->is_active == 0) {
                throw new CHttpException(200, 'Пользователь не активирован');
            }

            $identity = new BackendUserIdentity($email, $password);
            if ($identity->authenticate()) {
                Yii::app()->user->login($identity, 3600 * 12);
            } else {
                throw new CHttpException(200, 'Неправильное имя пользователя или пароль.');
            }

            $result = array(
                'result' => 1,
                'sid' => $this->_startSession($user->id),
                'simulations' => UserService::getGroups($user->id),
            );

        } catch (CHttpException $exc) {
            $result = array(
                'message' => $exc->getMessage()
            );
        }

        $this->sendJSON($result);

        return;
    }

    public function actionCheckSession()
    {
        $user_id = Yii::app()->session['uid'];
        $user = Users::model()->findByPk($user_id);
        if (null === $user) {
            $this->sendJSON(array('result' => 0, 'message' => 'User not found'));
            return;
        }
        $this->sendJSON(array(
            'result'      => 1,
            'sid'         => Yii::app()->session->sessionID,
            'simulations' => UserService::getGroups($user->id),
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
}
