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
            $isProfileValid = $profile->validate(['email']);

            if($isUserValid && $isProfileValid)
            {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $this->sendRegistrationEmail($this->user);

                    $this->redirect(['afterRegistration', 'userId' => $this->user->id]);
                } else {
                    $this->user->password = '';
                    $this->user->password_again = '';


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

        // get exists profile
        $profile    = YumProfile::model()->findByAttributes(['user_id' => $this->user->id]);
        $YumProfile = Yii::app()->request->getParam('YumProfile');
        $profile->firstname = $YumProfile['firstname'];
        $profile->lastname  = $YumProfile['lastname'];

        $accountCorporate = new UserAccountCorporate;
        $accountCorporate->user_id = $this->user->id;

        $accountPersonal = new UserAccountPersonal;
        $accountPersonal->user_id = $this->user->id;

        // ---

        if (null !== Yii::app()->request->getParam('personal')) {
            $isProfileValid     = $profile->validate(['firstname', 'lastname']);

            $UserAccountPersonal = Yii::app()->request->getParam('UserAccountPersonal');

            if(null !== $UserAccountPersonal && null !== $YumProfile)
            {
                $accountPersonal->attributes = $UserAccountPersonal; //$_POST['UserAccountPersonal'];
                $isUserAccountPersonalValid = $accountPersonal->validate();

                if($isUserAccountPersonalValid && $isProfileValid)
                {
                    $profile->save();
                    $accountPersonal->save();
                    $this->redirect(['registration/account-type/added']);
                }
            }
        }

        if (null !== Yii::app()->request->getParam('corporate')) {
            $isProfileValid     = $profile->validate(['firstname', 'lastname']);

            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');

            if(null!== $UserAccountCorporate & null !== $YumProfile)
            {
                $accountCorporate->attributes = $UserAccountCorporate; //$_POST['UserAccountCorporate'];
                $isUserAccountCorporateValid  = $accountCorporate->validate();

                if($isUserAccountCorporateValid && $isProfileValid)
                {
                    $profile->save();
                    $accountCorporate->save();
                    $this->redirect(['registration/account-type/added']);
                }
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

        // clean up validation errors if not POST request
        if (false === Yii::app()->request->isPostRequest) {
            $profile->validate([]);
        }

        $this->render(
            'chooseAccountType',
            [
                'accountPersonal'      => $accountPersonal,
                'accountCorporate'     => $accountCorporate,
                'industries'           => $industries,
                'positions'            => $positions,
                'profile'              => $profile,
                'isPersonalSubmitted'  => (null !== Yii::app()->request->getParam('personal')),
                'isCorporateSubmitted' => (null !== Yii::app()->request->getParam('corporate')),
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

        $body = strtr("Здравствуйте! Вы успешно зарегистрированы. Для активации аккаунта пройдите по ссылке:  <a href='{activation_url}'>\"Подтвердить регистрацию\"</a>", array(
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
        if(SessionHelper::isAuth()){
            $user = SessionHelper::getUserBySid();
            if($user->isAnonymous()){
                $this->redirect(['registration/choose-account-type']);
            }else{
                $this->render('results');
            }
        }else{
            $this->redirect(['registration/error/sign-in-or-register']);
        }
    }

    public function actionOffice()
    {
        $this->checkUser();

        $this->render('office', [
            'user' => $this->user
        ]);
    }
}

