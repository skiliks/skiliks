<?php

/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends AjaxController
{

    /**
     * 
     * @return type
     */
    protected function _getUser()
    {
        $sid = Yii::app()->request->getParam('sid', false);

        return SessionHelper::getUserBySid();
    }

    /**
     * 
     * @return type
     */
    public function actionChangeEmail()
    {

        $email1 = Yii::app()->request->getParam('email1', false);
        $email2 = Yii::app()->request->getParam('email2', false);
        if ($email1 != $email2) {
            $this->sendJSON(array(
                'result' => 0,
                'message' => 'Введенные емейлы не совпадают'
            ));
            return;
        }

        try {
            $user = $this->_getUser();
        } catch (Exception $exc) {
            $this->sendJSON(array(
                'result' => 0,
                'message' => $exc->getMessage()
            ));
            return;
        }

        $user->email = $email1;


        $result = array(
            'result' => (int) $user->save()
        );
        $this->sendJSON($result);
    }

    /**
     * 
     * @return type
     */
    public function actionChangePassword()
    {
        $pass1 = Yii::app()->request->getParam('pass1', false);
        $pass2 = Yii::app()->request->getParam('pass2', false);

        if ($pass1 != $pass2) {
            $this->sendJSON(array(
                'result' => 0,
                'message' => 'Введенные пароли не совпадают'
            ));
            return;
        }

        try {
            $user = $this->_getUser();
        } catch (Exception $exc) {
            $this->sendJSON(array(
                'result' => 0,
                'message' => $exc->getMessage()
            ));
            return;
        }

        $user->password = md5($pass1);


        $result = array(
            'result' => (int) $user->save()
        );
        $this->sendJSON($result);
    }

    /**
     *
     */
    public function actionRegistration()
    {
        $user    = new YumUser('registration');
        $profile = new YumProfile('registration');

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');

        if(null !== $YumUser && null !== $YumProfile)
        {
            $user->attributes    = $YumUser;
            $profile->attributes = $YumProfile;

            $user->setUserNameFromEmail($profile->email);

            if($user->validate() && $profile->validate())
            {
                $result = $user->register($user->username, $user->password, $profile);

                if (false !== $result) {
                    $this->redirect(['afterRegistration', 'userId' => $user->id]);
                } else {
                    echo 'Can`t register.';
                }
            }
        }

        $this->render(
            'registration' ,
            [
                'user'    => $user,
                'profile' => $profile,
            ]
        );
    }

    public function actionAfterRegistration()
    {
        $user = YumUser::model()->findByPk(Yii::app()->request->getParam('userId'));

        if (null === $user) {
            $this->redirect(['errorDuringRegistration']);
        }

        $this->render('afterRegistration', ['user' => $user]);
    }

    public function actionErrorDuringRegistration()
    {
        $this->render('errorDuringRegistration');
    }
}

