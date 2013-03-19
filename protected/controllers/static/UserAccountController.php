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
        $error = null;

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

            $existProfile = YumProfile::model()->findByAttributes([
                'email' => $profile->email
            ]);

            if ($existProfile && !$existProfile->user->isActive()) {
                $error = sprintf(
                    Yii::t('site',  'Email already exists, but not activated. <a href="%s?email=%s">Send activation again</a>'),
                    $this->createUrl('static/userAccount/resendActivation'),
                    $profile->email
                );
            } else {
                // we need profile validation even if user invalid
                $isUserValid = $this->user->validate();
                $isProfileValid = $profile->validate(['email']);

                if($isUserValid && $isProfileValid) {
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
        }

        $this->render(
            'registration' ,
            [
                'user'    => $this->user,
                'profile' => $profile,
                'error' => $error
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

        // --- personal

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

        // --- corporate

        if (null !== Yii::app()->request->getParam('corporate')) {
            $isProfileValid     = $profile->validate(['firstname', 'lastname']);

            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');

            if(null!== $UserAccountCorporate & null !== $YumProfile)
            {
                $accountCorporate->attributes = $UserAccountCorporate; //$_POST['UserAccountCorporate'];

                $isUserAccountCorporateValid  = $accountCorporate->validate();

                if (UserService::isCorporateEmail($profile->email)) {
                    $accountCorporate->is_corporate_email_verified = 1;

                    // todo: take care about user timezone
                    $accountCorporate->corporate_email_verified_at = date('Y-m-d H:i:s');
                }

                if($isUserAccountCorporateValid && $isProfileValid)
                {
                    $profile->save();

                    $accountCorporate->generateActivationKey();
                    $accountCorporate->save();

                    $this->user->refresh();

                    if (false === (bool)$accountCorporate->is_corporate_email_verified) {
                        $this->sendCorporationEmailVerification($this->user);
                    }

                    $this->redirect(['registration/account-type/added']);
                }
            }
        }

        // set email for corporate account, if email is corporate
        if (UserService::isCorporateEmail($profile->email)) {
            $accountCorporate->corporate_email = $profile->email;
        }

        $industries = [];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }

        $positions = [];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = Yii::t('site', $position->label);
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

        echo "<br><br><a href='/office'>Вернуться на страницу аккаунта.</a><br><br>Done!<br>";

        die;
    }

    /**
     * @param YumUser $user
     *
     * @return bool
     *
     * @throws CException
     */
    public function sendRegistrationEmail($user)
    {
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
     * @param YumUser $user
     *
     * @return bool
     *
     * @throws CException
     */
    public function sendCorporationEmailVerification($user)
    {
        // check user
        if (null === $user) {
            throw new CException(Yum::t('Email is not set when trying to send Corporation Email Verification. Wrong user object.'));
        }

        // check user account {
        if (null === $user->getAccount()) {
            throw new CException(Yum::t('Email is not set when trying to send Corporation Email Verification. User account not specified at all.'));
        }

        if (false === $user->isCorporate()) {
            throw new CException(Yum::t('Email is not set when trying to send Corporation Email Verification.This is not corporate user.'));
        }
        // check user account }

        // check corporate_email
        if (null === $user->getAccount()->corporate_email) {
            throw new CException(Yum::t('Email is not set when trying to send Corporation Email Verification. User account not specified at all. Corporate email is not specified.'));
        }

        $activation_url = $user->getCorporationEmailVerificationUrl();

        $body = sprintf(
            'Для подтверждения существования вашего корпоративного e-mail пройдите по ссылке:
            <a href="%s">"Подтвердить корпоративный e-mail"</a>.',
            $activation_url
        );

        $mail = array(
            'from'    => Yum::module('registration')->registrationEmail,
            'to'      => $user->getAccount()->corporate_email,
            'subject' => "Подтверждение существования корпоративного e-mail, для Skiliks.com",
            'body'    => $body,
        );
        $sent = YumMailer::send($mail);

        return $sent;
    }

    /**
     * @param Invite $invite
     * @return bool
     * @throws CException
     */
    public function sendInviteEmail($invite)
    {
        if (empty($invite->email)) {
            throw new CException(Yum::t('Email is not set when trying to send invite email. Wrong invite object.'));
        }

        $body = [
            'Уважаемый ' . $invite->getFullname(),
            $invite->message,
            'Пройдите по ссылке чтобы начать симуляцию',
            sprintf(
                '<a href="%1$s" target="_blank">%1$s</a>',
                Yii::app()->createAbsoluteUrl($invite->invited_user_id ? '/office' : '/accept-invite/' . $invite->code)
            ),
            $invite->signature
        ];

        $mail = [
            'from'    => Yum::module('registration')->registrationEmail,
            'to'      => $invite->email,
            'subject' => 'Приглашение пройти симуляцию на Skiliks.com',
            'body'    => implode("<br />", $body)
        ];

        $invite->sent_time = time();
        $invite->save();

        $sent = YumMailer::send($mail);

        return $sent;
    }

    /**
     * @param string $email, corporate email address
     *
     * http://skiliks.loc/registration/confirm-corporate-email?email=ss@3e.com
     */
    public function actionConfirmCorporateEmail()
    {
        $userAccountCorporate = UserAccountCorporate::model()->findByAttributes([
            'corporate_email_activation_code' => Yii::app()->request->getParam('activation-code'),
        ]);

        // 1. check account: if it not exists or already verified - redirect
        // 2. we redirect to homepage if email is already verified
        // - to protect against malefactor that use this controller/action to find what emails exist in our system
        if (null == $userAccountCorporate
            || null == $userAccountCorporate->user
            || null == $userAccountCorporate->user->getAccount()
            || $userAccountCorporate->is_corporate_email_verified) {
            $this->redirect('/');
        }

        $userAccountCorporate->user->getAccount()->is_corporate_email_verified = 1;
        $userAccountCorporate->user->getAccount()->corporate_email_verified_at = date('Y-m-d H:i:s');
        $userAccountCorporate->user->getAccount()->save();

        // redirect to success message page
        $this->redirect('/registration/confirm-corporate-email-success');
    }

    /**
     * Just success message
     */
    public function actionConfirmCorporateEmailSuccess()
    {
        $this->render('confirmCorporateEmailSuccess');
    }
    
    /**
     * Just error message
     */
    public function actionPleaseConfirmCorporateEmail()
    {
        $this->checkUser();

        $this->render('pleaseConfirmCorporateEmail', [
            'user' => $this->user
        ]);
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

    /**
     * @param $email
     */
    public function actionResendActivation($email)
    {
        $profile = YumProfile::model()->findByAttributes([
            'email' => $email
        ]);

        if ($profile && !$profile->user->isActive()) {
            $this->sendRegistrationEmail($profile->user);
            $this->redirect(['afterRegistration', 'userId' => $profile->user->id]);
        } else {
            $this->redirect('/');
        }
    }

    /**
     * Display simulation result marks
     */
    public function actionResults()
    {
        // check is user authenticated
        if (Yii::app()->user->isGuest) {
            $this->redirect(['registration/error/sign-in-or-register']);
        }

        $this->user = Yii::app()->user->data();

        // user must specify account to see simulation results
        if (false == $this->user->isHasAccount()) {
            $this->redirect(['registration/choose-account-type']);
        }

        // corporate user must have verified corporate email to see simulation results
        if ($this->user->isCorporate() && false == (bool)$this->user->getAccount()->is_corporate_email_verified) {
            $this->redirect(['registration/please-confirm-corporate-email']);
        }

        $results = [];

        $simulation = Simulation::model()->findByAttributes([
            'user_id' => $this->user->id
        ],
        [
            'order' => 'id DESC'
        ]);

        if (null !== $simulation) {
            $results = AssessmentAggregated::model()->findAllByAttributes([
                'sim_id' => $simulation->id
            ]);
        }

        // all checks passed - render simulation results
        $this->render('results', [
            'results' => $results
        ]);
    }

    /**
     * User private page -> "user cabinet"
     */
    public function actionOffice()
    {
        $this->checkUser();

        $this->render('office', [
            'user' => $this->user
        ]);
    }

    public function actionDashboard()
    {
        $this->checkUser();

        $lang = substr(Yii::app()->language, 0, 2);
        $invite = new Invite();
        $valid = false;

        if (null !== Yii::app()->request->getParam('prevalidate')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $valid = $invite->validate(['firstname', 'lastname', 'email']);
            $profile = YumProfile::model()->findByAttributes(['email' => $invite->email]);
            $position_label = Yii::t('site',$invite->position->label);
            if ($profile) {
                $invite->message = <<<MSG
Зайдите пожалуйста в ваш кабинет, работодатель отправил вам приглашение пройти ассессмент на позицию $position_label.
MSG;
            } else {
                $invite->message = <<<MSG
Работодатель заинтересован в вашей кандидатуре на позицию $position_label. Для кандидата на данную позицию обязательным условием
является прохождение ассессмента для определениям уровня менеджерских навыков. Для этого вам необходимо пройти по ссылке,
зарегистрироваться и запустить ассессмент.
MSG;
            }

            $invite->signature = 'Best regards';
        }

        if (null !== Yii::app()->request->getParam('send')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');

            $invite->code = uniqid(md5(mt_rand()));
            $invite->inviting_user_id = $this->user->id;

            // What happens if user is registered, but not activated??
            $profile = YumProfile::model()->findByAttributes([
                'email' => $invite->email
            ]);
            if ($profile) {
                $invite->invited_user_id = $profile->user->id;
            }

            if ($invite->validate()) {
                $invite->save();
                $this->sendInviteEmail($invite);

                $this->redirect('/');
            }
        }

        $positions = [];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = $position->label;
        }

        $this->render('dashboard', [
            'user' => $this->user,
            'invite' => $invite,
            'positions' => $positions,
            'valid' => $valid
        ]);
    }

    public function actionAcceptInvite($code)
    {
        $invite = Invite::model()->findByCode($code);
        if (empty($invite)) {
            $this->redirect('/');
        }

        $this->user = new YumUser('registration');
        $profile = new YumProfile('registration');
        $account = new UserAccountPersonal();
        $lang = substr(Yii::app()->language, 0, 2);
        $error = null;

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');
        $UserAccount = Yii::app()->request->getParam('UserAccountPersonal');

        if(null !== $YumUser && null !== $YumProfile && null !== $UserAccount)
        {
            $this->user->attributes = $YumUser;
            $profile->attributes = $YumProfile;
            $account->attributes = $UserAccount;

            $profile->email = $invite->email;
            $this->user->setUserNameFromEmail($profile->email);

            // Protect from "Wrong username" message - we need "Wrong email", from Profile form
            if (null == $this->user->username) {
                $this->user->username = 'DefaultName';
            }

            $userValid = $this->user->validate();
            $profileValid = $profile->validate();

            if ($userValid && $profileValid) {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $account->user_id = $this->user->id;
                    $account->save();

                    $invite->status = Invite::STATUS_ACCEPTED;
                    $invite->save();

                    $activation_result = YumUser::activate($profile->email, $this->user->getActivationUrl());
                    assert($activation_result);
                    $this->user->authenticate($YumUser['password']);
                    // TODO: Change redirection point
                    $this->redirect('/registration/account-type/added');
                } else {
                    $this->user->password = '';
                    $this->user->password_again = '';

                    echo 'Can`t register.';
                }
            }
        }

        $industries = [];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = $industry->label;
        }

        $positions = [];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = $position->label;
        }

        $this->render(
            'registrationByLink',
            [
                'invite' => $invite,
                'user' => $this->user,
                'profile' => $profile,
                'account' => $account,
                'industries' => $industries,
                'positions' => $positions,
                'error' => $error
            ]
        );
    }

    public function actionDeclineInvite($code)
    {
        $invite = Invite::model()->findByCode($code);
        if (empty($invite)) {
            $this->redirect('/');
        }

        $reason = Yii::app()->request->getParam('reason');
        $reasonDesc = Yii::app()->request->getParam('reason-desc');

        $invite->status = Invite::STATUS_DECLINED;
        $invite->save();

        // TODO: Change redirection point
        $this->redirect('/');
    }
}

