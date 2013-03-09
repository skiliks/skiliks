<?php

/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    /**
     *
     */
    public function actionRegistration()
    {
        $this->user    = new YumUser('registration');
        $profile = new YumProfile('registration');

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');

        if(null !== $YumUser && null !== $YumProfile)
        {
            $this->user->attributes    = $YumUser;
            $profile->attributes = $YumProfile;

            $this->user->setUserNameFromEmail($profile->email);

            if($this->user->validate() && $profile->validate())
            {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $this->redirect(['afterRegistration', 'userId' => $this->user->id]);
                } else {
                    echo 'Can`t register.';
                }
            }
        }

        $this->render(
            'registration' ,
            [
                'user'    => $this->user,
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

