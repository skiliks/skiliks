<?php

/**
 * Кабинет пользователя
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserAuthController extends YumController
{
    /**
     * @var YumUser
     */
    public $user;
    public $signInErrors = [];

    /**
     *
     */
    public function actionLogout()
    {
        if (false === Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }

        $this->redirect('/');
    }

    public function actionLogoutAndRegistration()
    {
        if (false === Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }

        $this->redirect('/registration');
    }

    /**
     * User registration step 1
     */
    public function actionRegistration()
    {
        $user = Yii::app()->user->data();
        /* @var $user YumUser */
        if($user->isAuth()){
            $this->redirect('/dashboard');
        }
        $this->user = new YumUser('registration');
        $profile    = new YumProfile('registration');
        $error = null;

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');

        if(null !== $YumUser && null !== $YumProfile)
        {
            $this->user->attributes = $YumUser;
            $profile->attributes    = $YumProfile;
            //$this->user->is_check = (int)$YumUser['is_check'];
            $this->user->setUserNameFromEmail($profile->email);
            $profile->updateFirstNameFromEmail();

            // Protect from "Wrong username" message - we need "Wrong email", from Profile form
            if (null == $this->user->username) {
                $this->user->username = 'DefaultName';
            }

            $existProfile = YumProfile::model()->findByAttributes([
                'email' => $profile->email
            ]);
                // we need profile validation even if user invalid
            $this->user->createtime = time();
            $this->user->lastvisit = time();
            $this->user->lastpasswordchange = time();
            $isUserValid = $this->user->validate();
            $isProfileValid = $profile->validate(['email', 'general_error']);

            if($isUserValid && $isProfileValid) {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $this->sendRegistrationEmail($this->user);

                    $this->redirect(['afterRegistration']);
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
                'error' => $error
            ]
        );
    }

    /**
     * @param string $code
     */
    public function actionRegisterByLink($code)
    {
        $invite = Invite::model()->findByCode($code);
        if (empty($invite)) {
            Yii::app()->user->setFlash('error', 'Код приглашения неверный.');
            $this->redirect('/');
        }

        if((int)$invite->status === Invite::STATUS_EXPIRED){
            Yii::app()->user->setFlash('error', 'Истёк срок ожидания ответа на приглашение');
            $this->redirect('/');
        }

        if((int)$invite->status === Invite::STATUS_DECLINED){
            //Yii::app()->user->setFlash('error', 'Приглашение уже отклонено.'); TODO:Проблемный попап
            $this->redirect('/');
        }

        if((int)$invite->status !== Invite::STATUS_PENDING){
            //Yii::app()->user->setFlash('error', 'Пользователь по данному приглашению уже зарегистрирован.');
            $this->redirect('/dashboard');
        }

        if ($invite->receiverUser || YumProfile::model()->findByAttributes(['email' => $invite->email])) {
            Yii::app()->user->setFlash('error', 'Пользователь по данному приглашению уже зарегистрирован');
            $this->redirect('/');
        }

        Yii::app()->user->logout();
        sleep(0.5); // for safety

        $this->user = new YumUser('registration');

        $profile = new YumProfile('registration');
        $profile->firstname = $invite->firstname;
        $profile->lastname = $invite->lastname;

        $account = new UserAccountPersonal();
        $error = null;

        $YumUser = Yii::app()->request->getParam('YumUser');
        $YumProfile = Yii::app()->request->getParam('YumProfile');
        $UserAccount = Yii::app()->request->getParam('UserAccountPersonal');

        if(null !== $YumUser && null !== $YumProfile && null !== $UserAccount)
        {
            $this->user->attributes = $YumUser;
            $profile->attributes = $YumProfile;
            $account->attributes = $UserAccount;

            $profile->email = strtolower($invite->email);
            $this->user->setUserNameFromEmail($profile->email);

            // Protect from "Wrong username" message - we need "Wrong email", from Profile form
            if (null == $this->user->username) {
                $this->user->username = 'DefaultName';
            }

            $userValid = $this->user->validate();
            $profileValid = $profile->validate();
            $accountValid = $account->validate(['professional_status_id']);

            if ($userValid && $profileValid && $accountValid) {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $account->user_id = $this->user->id;
                    $account->save(false);

                    $invite->receiver_id = $this->user->id;
                    $invite->save();

                    YumUser::activate($profile->email, $this->user->activationKey);
                    $this->user->authenticate($YumUser['password']);

                    $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);

                    $permission = new YumPermission();
                    $permission->principal_id = $this->user->id;
                    $permission->subordinate_id = $this->user->id;
                    $permission->action = $action->id;
                    $permission->type = 'user';
                    $permission->template = 1; // magic const
                    $permission->save(false);

                    UserService::assignAllNotAssignedUserInvites(Yii::app()->user->data());

                    $this->redirect('/dashboard');
                } else {
                    $this->user->password = '';
                    $this->user->password_again = '';

                    Yii::app()->user->setFlash('error', 'Ошибки регистрации. Обратитесь в <a href="/contacts">службу поддержки</a>.');
                    $this->redirect('/');
                }
            }
        }

        $industries = ['' => 'Выберите область деятельности'];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }

        $statuses = ['' => 'Выберите статус'];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = Yii::t('site', $status->label);
        }

        $this->render(
            'registration_by_link',
            [
                'invite'     => $invite,
                'user'       => $this->user,
                'profile'    => $profile,
                'account'    => $account,
                'industries' => $industries,
                'statuses'   => $statuses,
                'error'      => $error
            ]
        );
    }

    /**
     * User registration step 1 - handle form
     */
    public function actionAfterRegistration()
    {
        $this->render('afterRegistration', ['isGuest' => Yii::app()->user->isGuest]);
    }

    /**
     * User registration step 1 - handle form
     */
    public function actionAfterRegistrationCorporate()
    {
        if (false === Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }

        $this->render('afterRegistrationCorporate');
    }

    /**
     * User registration default errors handler
     */
    public function actionErrorDuringRegistration()
    {
        Yii::app()->user->setFlash(
            'error',
            Yii::t('site','Something went wrong please try to %s register again %s.')
        );

        $this->render('emptyPage', [
            'user' => $this->user
        ]);
    }

    /**
     * User registration Error "You Has Already Choose Account"
     */
    public function actionErrorYouHasAlreadyChooseAccount()
    {
        $this->checkUser();

        Yii::app()->user->setFlash('error', 'Вы уже выбрали тип аккаунта');

        $this->render('emptyPage', [
            'user'  => $this->user
        ]);
    }

    /**
     * User registration Error "Your Account Not Active"
     */
    public function actionErrorYourAccountNotActive()
    {
        $this->checkUser();

        Yii::app()->user->setFlash('error', 'Ваш аккаунт неактивен');

        $this->render('emptyPage', [
            'user'  => $this->user
        ]);
    }

    /**
     * User registration Error "Please sing-is or register"
     */
    public function actionErrorSingInOrRegister()
    {
        Yii::app()->user->setFlash(
            'error',
            Yii::t('site', 'You not authorized. Please %s sing-in %s or %s register %s.')
        );

        $this->render('emptyPage');
    }

    /**
     * User registration - "Account Type Saves Successfully" message
     */
    public function actionAccountTypeSavesSuccessfully()
    {
        $this->checkUser();

        if ($this->user->isHasAccount() ) {
            $this->redirect('/dashboard');
            return;
        }

        /*Yii::app()->user->setFlash( 'success', $message );*/

        $this->render('emptyPage', [
            'user' => $this->user
        ]);
    }

    /**
     * User registration - choose account type
     */
    public function actionChooseAccountType()
    {
        $this->checkUser();

        if (Yii::app()->user->isGuest) {
            $this->redirect('/user/auth');
        }

        // only activated user can choose account type
        if (false == $this->user->isActive()) {
            Yii::app()->user->setFlash('error', 'Ваш аккаунт неактивен');

            $this->redirect('/');
        }

        // user can choose account type once only
        if (true == $this->user->isHasAccount()) {
            $this->redirect('/dashboard');
        }

        // get exists profile
        $profile    = YumProfile::model()->findByAttributes(['user_id' => $this->user->id]);
        $YumProfile = Yii::app()->request->getParam('YumProfile');
        $profile->firstname = $YumProfile['firstname'];
        $profile->lastname  = $YumProfile['lastname'];
        $profile->timestamp = gmdate("Y-m-d H:i:s");

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
                $isUserAccountPersonalValid = $accountPersonal->validate(['user_id', 'industry_id', 'professional_status_id']);

                if($isUserAccountPersonalValid && $isProfileValid)
                {
                    // grands permission to start full simulation {
                    try {
                    $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);
                    $permission = new YumPermission();
                    $permission->principal_id = Yii::app()->user->data()->id;
                    $permission->subordinate_id = Yii::app()->user->data()->id;
                    $permission->type = 'user';
                    $permission->action = $action->id;
                    $permission->template = 1;
                    $permission->save();
                    } catch(CDbException $e) {
                        // duplicated records:
                        // this possible for developers only,
                        // when you remove your personal account and choose account type as personal again
                        //
                    }
                    // grands permission to start full simulation }

                    $profile->save();
                    $accountPersonal->save(true, ['user_id', 'industry_id', 'professional_status_id']);

                    SimulationService::assignAllNotAssignedUserInvites(Yii::app()->user->data());

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

                $isUserAccountCorporateValid  = $accountCorporate->validate(['corporate_email', 'industry_id', 'user_id']);

                if (UserService::isCorporateEmail($profile->email)) {
                    $accountCorporate->is_corporate_email_verified = 1;

                    // todo: take care about user timezone
                    $accountCorporate->corporate_email_verified_at = date('Y-m-d H:i:s');
                }

                if($isUserAccountCorporateValid && $isProfileValid)
                {
                    $profile->save();
                    $accountCorporate->default_invitation_mail_text = 'Вопросы относительно тестирования вы можете задать по адресу '.$profile->email.', куратор тестирования - '.$profile->firstname.' '. $profile->lastname .'.';
                    $accountCorporate->generateActivationKey();
                    $accountCorporate->save(false);

                    // set Lite tariff by default
                    $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);

                    // update account tariff
                    $accountCorporate->setTariff($tariff, true);

                    $this->user->refresh();

                    if (false === (bool)$accountCorporate->is_corporate_email_verified) {
                        $this->sendCorporationEmailVerification($this->user);
                        $this->redirect('afterRegistrationCorporate');
                    } else {
                        $this->redirect('/dashboard');
                    }
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

        $statuses = [];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = Yii::t('site', $status->label);
        }

        $simPassed = Simulation::model()->getLastSimulation($this->user, Scenario::TYPE_LITE) === null ? false : true;

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
                'statuses'             => $statuses,
                'profile'              => $profile,
                'isPersonalSubmitted'  => (null !== Yii::app()->request->getParam('personal')),
                'isCorporateSubmitted' => (null !== Yii::app()->request->getParam('corporate')),
                'simPassed'            => $simPassed
            ]
        );
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

        $body = $this->renderPartial('//global_partials/mails/registration', ['link' => $activation_url], true);

        $mail = array(
            'from' => Yum::module('registration')->registrationEmail,
            'to' => $user->profile->email,
            'subject' => 'Активация на сайте skiliks.com',
            'body' => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mailtopangela.png',
                    'cid'      => 'mail-top-angela',
                    'name'     => 'mailtopangela',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mailanglabtm.png',
                    'cid'      => 'mail-bottom-angela',
                    'name'     => 'mailbottomangela',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ],
        );
        $sent = MailHelper::addMailToQueue($mail);

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

        $body = $this->renderPartial('//global_partials/mails/verification', [
            'link' => $activation_url,
            'name' => $user->getFormattedFirstName()
        ], true);

        $mail = array(
            'from'    => Yum::module('registration')->registrationEmail,
            'to'      => $user->getAccount()->corporate_email,
            'subject' => 'Регистрация корпоративного пользователя на skiliks.com',
            'body'    => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mailtopangela.png',
                    'cid'      => 'mail-top-angela',
                    'name'     => 'mailtopangela',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mailanglabtm.png',
                    'cid'      => 'mail-bottom-angela',
                    'name'     => 'mailbottomangela',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ],
        );
        $sent = MailHelper::addMailToQueue($mail);

        return $sent;
    }

    /**
     * @param YumUser $user
     *
     * @return bool
     *
     * @throws CException
     */
    public function sendPasswordRecoveryEmail($user)
    {
        if (!isset($user->profile->email)) {
            throw new CException(Yum::t('Email is not set when trying to send Recovery Email'));
        }

        $recoveryUrl = $this->createAbsoluteUrl(
            $this->createUrl('static/userAuth/recovery', [
                'key' => $user->activationKey,
                'email' => $user->profile->email
            ])
        );

        $body = $this->renderPartial('//global_partials/mails/recovery', [
            'name' => $user->getFormattedFirstName(),
            'link' => $recoveryUrl
        ], true);

        $mail = [
            'from' => Yum::module('registration')->recoveryEmail,
            'to' => $user->profile->email,
            'subject' => 'Восстановление пароля к skiliks.com', //Yii::t('site', 'You requested a new password'),
            'body' => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mailtopclean.png',
                    'cid'      => 'mail-top-clean',
                    'name'     => 'mailtopclean',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mailchair.png',
                    'cid'      => 'mail-chair',
                    'name'     => 'mailchair',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ],
        ];

        $sent = MailHelper::addMailToQueue($mail);

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
        /* @var $userAccountCorporate->user YumUser */
        /* @var $userAccountCorporate UserAccountCorporate */
        $userAccountCorporate->is_corporate_email_verified = 1;
        $userAccountCorporate->corporate_email_verified_at = date('Y-m-d H:i:s');
        $userAccountCorporate->save(true, ['is_corporate_email_verified', 'corporate_email_verified_at']);

        $login = new YumUserIdentity($userAccountCorporate->user->username, false);
        $login->authenticate(true);
        Yii::app()->user->login($login);

        $redirect = Yii::app()->request->getParam('redirect', null);
        if($redirect !== null){
            $this->redirect('/'.$redirect);
        }else{
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Just error message
     */
    public function actionPleaseConfirmCorporateEmail()
    {
        $this->checkUser();

        $this->render('emptyPage');
    }

    /**
     * Activation of an user account. The Email and the Activation key send
     * by email needs to correct in order to continue. The Status will
     * be initially set to 1 (active - first Visit) so the administrator
     * can see, which accounts have been activated, but not yet logged in
     * (more than once)
     */
    public function actionActivation($email, $key) {

        $email = trim($email);
        $email = str_replace(' ', '+', $email);

        if (false === Yii::app()->user->isGuest && Yii::app()->user->data()->profile->email !== $email) {
            Yii::app()->user->setFlash(
                'error',
                sprintf(Yii::t('site',
                    'You are already logged in (%s), please log out to activate your account %s') ,
                    Yii::app()->user->data()->profile->email,
                    $email
                )
            );
            $this->redirect('/');
        }

        $YumUser    = Yii::app()->request->getParam('YumUser');
        $YumProfile = YumProfile::model()->findByAttributes(['email'=>$email]);

        if(null !== $YumUser) {
            $user = YumUser::model()->findByAttributes(['id'=>$YumProfile->user_id]);
            $user->is_check = $YumUser['is_check'];
            $user->update();

            if ((int)$YumUser['is_check'] === YumUser::CHECK) {
                $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);
                $invite = Invite::addFakeInvite(Yii::app()->user->data(), $liteScenario);
                $this->redirect(['/simulation/promo/'.Scenario::TYPE_LITE.'/'.$invite->id], false);
            } else if((int)$YumUser['is_check'] === YumUser::NOT_CHECK) {
                $this->redirect(['/registration/choose-account-type'], false);
            } else {
                throw new Exception("Bug");
            }
            return;
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

            $this->render(Yum::module('registration')->activationSuccessView, ['user'=>$YumProfile->user]);
        } else {
            if(Yii::app()->user->isGuest){
                $this->layout = false;
                Yii::app()->user->setFlash(
                    (-1 == $status) ? 'error' : 'success',
                    $this->render(
                        Yum::module('registration')->activationFailureView,
                        array('error' => $status),
                        true
                    )
                );
            }
            $this->redirect('/');
        }
    }

    /**
     * @param $email
     */
    public function actionResendActivation($profileId)
    {
        $profile = YumProfile::model()->findByPk($profileId);

        if ($profile && !$profile->user->isActive()) {
            $this->sendRegistrationEmail($profile->user);
            $this->redirect(['afterRegistration']);
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

    public function actionRecovery($email = null, $key = null)
    {
        $recoveryForm = new YumPasswordRecoveryForm;
        $passwordForm = new YumUserChangePassword;

        $YumPasswordRecoveryForm = Yii::app()->request->getParam('YumPasswordRecoveryForm');
        $YumUserChangePassword = Yii::app()->request->getParam('YumUserChangePassword');


        if (null !== $email && null !== $key && null !== $YumUserChangePassword) {
            $profile = YumProfile::model()->findByAttributes(['email' => $email]);
            if ($profile && $profile->user->status > 0 && $profile->user->activationKey == $key) {
                $user = $profile->user;

                $passwordForm->attributes = $YumUserChangePassword;

                if ($passwordForm->validate()) {
                    $user->activationKey = 1;
                    $user->setPassword($passwordForm->password, $user->salt);

                    Yii::app()->user->setFlash('success password-recovery-step-4', 'Новый пароль успешно сохранен');
                    if (Yum::module('registration')->loginAfterSuccessfulRecovery) {
                        $login = new YumUserIdentity($user->username, false);
                        $login->authenticate(true);
                        Yii::app()->user->login($login);
                    }

                    $this->redirect('/');
                }
            }
        }

        if (null !== $email && null !== $key) {
            /* @var $profile->user YumUser */
            $profile = YumProfile::model()->findByAttributes(['email' => $email]);
            if(Yii::app()->user->data()->isAuth()) {
                Yii::app()->user->setFlash('notice', 'Вы уже авторизированы');
                $this->redirect('/dashboard');
            }
            if ($profile && $profile->user->status > 0 && $profile->user->activationKey == $key) {
                $this->render('setPassword', [
                    'passwordForm' => $passwordForm
                ]);

                Yii::app()->end();
            } else {
                Yii::app()->user->setFlash('notice', 'Пароль уже востановлен');
                $this->redirect('/');
            }
        }

        if (null !== $YumPasswordRecoveryForm) {
            $recoveryForm->attributes = $YumPasswordRecoveryForm;
            if(isset($_POST['ajax']) && $_POST['ajax']==='password-recovery-form')
            {
                echo CActiveForm::validate($recoveryForm);
                Yii::app()->end();
            }
            if ($recoveryForm->validate() && $recoveryForm->user instanceof YumUser && $recoveryForm->user->status > 0) {
                $user = $recoveryForm->user;
                $user->generateActivationKey();
                $result = $this->sendPasswordRecoveryEmail($user);

                if ($result) {
                    Yii::app()->user->setFlash('recovery-popup', 'На ваш email выслана инструкция по смене пароля.');
                    if (!Yii::app()->request->getIsAjaxRequest()) {
                        $this->redirect('/');
                    } else {
                        Yii::app()->end();
                    }
                } else {
                    Yii::app()->user->setFlash('error', Yii::t('site','There was an error sending recovery email'));
                }
            }
        }

        $this->render('recovery', [
            'recoveryForm' => $recoveryForm
        ]);
    }

}

