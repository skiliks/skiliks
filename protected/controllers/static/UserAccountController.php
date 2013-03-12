<?php

/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAccountController extends YumController
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
                    $this->sendRegistrationEmail($this->user);
                    //Yum::setFlash('Thank you for your registration. Please check your email.');
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
        $this->render('afterRegistration', [
            'user' => $this->user
        ]);
    }

    /**
     * User registration default errors handler
     */
    public function actionErrorDuringRegistration()
    {
        $this->render('errorDuringRegistration', [
            'user' => $this->user
        ]);
    }

    /**
     * User registration Error "You Has Already Choose Account"
     */
    public function actionErrorYouHasAlreadyChooseAccount()
    {
        $this->checkUser();

        $this->render('errorDuringRegistration', [
            'error' => 'You has already choose account.',
            'user'  => $this->user
        ]);
    }

    /**
     * User registration Error "Your Account Not Active"
     */
    public function actionErrorYourAccountNotActive()
    {
        $this->checkUser();

        $this->render('errorDuringRegistration', [
            'error' => 'Your account is not active.',
            'user'  => $this->user
        ]);
    }

    /**
     * User registration Error "Please sing-is or register"
     */
    public function actionErrorSingInOrRegister()
    {
        $this->checkUser();

        $this->render('errorSingInOrRegister', [
            'user' => $this->user
        ]);
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
        $lang = substr(Yii::app()->language, 0, 2);

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
        foreach (Industry::model()->findAllByAttributes(['language' => $lang]) as $industry) {
            $industries[$industry->id] = $industry->label;
        }

        $positions = [];
        foreach (Position::model()->findAllByAttributes(['language' => $lang]) as $position) {
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

    public function sendRegistrationEmail($user) {
        if (!isset($user->profile->email)) {
            throw new CException(Yum::t('Email is not set when trying to send Registration Email'));
        }
        $activation_url = $user->getActivationUrl();

        $body = strtr("Здравствуйте уважаемый пользователь, Ваша сслыка активации аккаунта <a href='{activation_url}'>перейти</a>", array(
            '{activation_url}' => $activation_url));

        $mail = array(
            'from' => Yum::module('registration')->registrationEmail,
            'to' => $user->profile->email,
            'subject' => "Активация на Skiliks",
            'body' => $body,
        );
        $sent = YumMailer::send($mail);

        return $sent;
    }

    /**
     * Activation of an user account. The Email and the Activation key send
     * by email needs to correct in order to continue. The Status will
     * be initially set to 1 (active - first Visit) so the administrator
     * can see, which accounts have been activated, but not yet logged in
     * (more than once)
     */
    public function actionActivation($email, $key) {
        // If already logged in, we dont activate anymore
        if (!Yii::app()->user->isGuest) {
            Yum::setFlash('You are already logged in, please log out to activate your account');
            $this->redirect(Yii::app()->user->returnUrl);
        }

        // If everything is set properly, let the model handle the Validation
        // and do the Activation
        $status = YumUser::activate($email, $key);


        if($status instanceof YumUser) {
            if(Yum::module('registration')->loginAfterSuccessfulActivation) {
                $login = new YumUserIdentity($status->username, false);
                $login->authenticate(true);
                Yii::app()->user->login($login);
            }

            $this->render(Yum::module('registration')->activationSuccessView);
        }
        else
            $this->render(Yum::module('registration')->activationFailureView, array(
                'error' => $status));
    }

    public function actionResults()
    {
        $this->render('results');
    }

}

