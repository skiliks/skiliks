<?php

/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends AjaxController
{
    /**
     * @var YumUser
     */
    public $user;
    public $signInErrors = [];

    /**
     * User registration step 1
     */
    public function actionRegistration()
    {
        $this->user = new YumUser('registration');
        $profile    = new YumProfile('registration');

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');

        if(null !== $YumUser && null !== $YumProfile)
        {
            $this->user->attributes = $YumUser;
            $profile->attributes    = $YumProfile;

            $this->user->setUserNameFromEmail($profile->email);
            $profile->updateFirstNameFromEmail();

            // Protect from "Wrong username" message - we need "Wrong email", from Profile form
            if (null == $this->user->username) {
                $this->user->username = 'DefaultName';
            }

            // we need profile validation even if user invalid
            $isUserValid = $this->user->validate();
            $isProfileValid = $profile->validate();

            if($isUserValid && $isProfileValid)
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

    /**
     * User registration step 1 - handle form
     */
    public function actionAfterRegistration()
    {
        $this->checkUser();

        $this->render('afterRegistration', ['user' => $this->user]);
    }

    /**
     * User registration default errors handler
     */
    public function actionErrorDuringRegistration()
    {
        $this->render('errorDuringRegistration');
    }

    /**
     * User registration Error "You Has Already Choose Account"
     */
    public function actionErrorYouHasAlreadyChooseAccount()
    {
        $this->render('errorDuringRegistration', [
            'error' => 'You has already choose account.'
        ]);
    }

    /**
     * User registration Error "Your Account Not Active"
     */
    public function actionErrorYourAccountNotActive()
    {
        $this->render('errorDuringRegistration', [
            'error' => 'Your account is not active.'
        ]);
    }

    /**
     * User registration Error "Please sing-is or register"
     */
    public function actionErrorSingInOrRegister()
    {
        $this->render('errorSingInOrRegister');
    }

    /**
     * User registration - "Account Type Saves Successfully" message
     */
    public function actionAccountTypeSavesSuccessfully()
    {
        $this->checkUser();

        $this->render('accountTypeSavesSuccessfully', [
            'user' => $this->user
        ]);
    }

    /**
     * User registration - choose account type
     */
    public function actionChooseAccountType()
    {
        $this->checkUser();

        // only activated user can choose account type
        if (false == $this->user->isActive()) {
            $this->redirect(['registration/error/active']);
        }

        // user can choose account type once only
        if (true == $this->user->isHasAccount()) {
            $this->redirect(['registration/error/has-account']);
        }

        // ---

        $accountPersonal = new UserAccountPersonal;
        $accountPersonal->user_id = $this->user->id;

        if(isset($_POST['UserAccountPersonal']))
        {
            $accountPersonal->attributes=$_POST['UserAccountPersonal'];
            if($accountPersonal->validate())
            {
                $accountPersonal->save();
                $this->redirect(['registration/account-type/added']);
            }
        }

        $accountCorporate = new UserAccountCorporate;
        $accountCorporate->user_id = $this->user->id;

        if(isset($_POST['UserAccountCorporate']))
        {
            $accountCorporate->attributes=$_POST['UserAccountCorporate'];
            if($accountCorporate->validate())
            {
                $accountCorporate->save();
                $this->redirect(['registration/account-type/added']);
            }
        }

        $industries = [];
        foreach (Industry::model()->findAllByAttributes(['language' => Yii::app()->language]) as $industry) {
            $industries[$industry->id] = $industry->label;
        }

        $positions = [];
        foreach (Position::model()->findAllByAttributes(['language' => Yii::app()->language]) as $position) {
            $positions[$position->id] = $position->label;
        }

        $this->render(
            'chooseAccountType',
            [
                'accountPersonal'  => $accountPersonal,
                'accountCorporate' => $accountCorporate,
                'industries'       => $industries,
                'positions'        => $positions,
            ]
        );
    }

    /**
     * Action for testing - allow reset authorized user account type
     */
    public function actionCleanUpAccount()
    {
        $this->checkUser();

        if (null !== $this->user->account_personal) {
            $this->user->account_personal->delete();
            echo "<br>Personal accoutn removed.<br>";
        }

        if (null !== $this->user->account_corporate) {
            $this->user->account_corporate->delete();
            echo "<br>Corporate accoutn removed.<br>";
        }

        echo "<br>Done!<br>";

        die;
    }
}

