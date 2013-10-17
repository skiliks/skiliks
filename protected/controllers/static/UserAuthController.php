<?php

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
     * @param int $id
     */

    public function actionRegisterReferral($refHash=false) {

        if (false === Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }

        $user = new YumUser('registration');
        $profile = new YumProfile('registration');
        $accountCorporate = new UserAccountCorporate('registration');

        $userReferralRecord = UserReferral::model()->findByAttributes(['uniqueid' => $refHash]);

            if($userReferralRecord !== null) {

                $existUser = YumProfile::model()->findByAttributes(["email" => $userReferralRecord->referral_email]);
                if($existUser !== null) {
                    Yii::app()->user->setFlash('error', 'Пользователь '.$userReferralRecord->referral_email.' уже зарегистрирован.');
                    $this->redirect('/');
                }

                $YumUserData     = Yii::app()->request->getParam('YumUser');
                $YumProfileData  = Yii::app()->request->getParam('YumProfile');
                $UserAccountCorporateData = Yii::app()->request->getParam('UserAccountCorporate');

                if(null !== $YumUserData && null !== $YumProfileData && null !== $UserAccountCorporateData)
                {
                    $user->attributes = $YumUserData;
                    $profile->attributes = $YumProfileData;
                    $accountCorporate->attributes = $UserAccountCorporateData;

                        $profile->email = strtolower($userReferralRecord->referral_email);
                        $user->setUserNameFromEmail($profile->email);

                        $isUserValid = $user->validate();
                        $isProfileValid = $profile->validate();
                        $isAccountCorporate = $accountCorporate->validate();

                        if ($isUserValid && $isProfileValid && $isAccountCorporate) {
                            $result = $user->register($user->username, $user->password, $profile);

                            if (false !== $result) {
                                $profile->save();

                                $user->status = YumUser::STATUS_ACTIVE;
                                $user->update();


                                // set Lite tariff by default
                                $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);

                                // update account
                                $accountCorporate->user_id = $user->id;
                                $accountCorporate->setTariff($tariff);
                                $accountCorporate->invites_limit = Yii::app()->params['initialSimulationsAmount'];
                                
                                UserService::logCorporateInviteMovementAdd(
                                    sprintf('Количество симуляций для нового аккаунта номер %s, емейл %s, задано равным %s по тарифному плану %s.',
                                        $accountCorporate->user_id, $profile->email, $accountCorporate->getTotalAvailableInvitesLimit(), $tariff->label
                                    ),
                                    $accountCorporate,
                                    $accountCorporate->getTotalAvailableInvitesLimit()
                                );
                                
                                $accountCorporate->save();

                                $userReferralRecord->referral_id = $user->id;
                                $userReferralRecord->approveReferral();
                                $userReferralRecord->rejectAllWithSameEmail();
                                $userReferralRecord->save();

                                YumUser::activate($profile->email, $user->activationKey);
                                $user->authenticate($YumUserData['password']);



                                Yii::app()->user->setFlash('success', 'Вы успешно зарегистрированы!');
                                $this->redirect('/dashboard');
                            }
                        }

                }

                $industries = ['' => 'Выберите отрасль'];
                foreach (Industry::model()->findAll() as $industry) {
                    $industries[$industry->id] = Yii::t('site', $industry->label);
                }

                $this->render(
                    'referral_registration',
                    [
                        'refHash'            => $refHash,
                        'user'             => $user,
                        'profile'          => $profile,
                        'accountCorporate' => $accountCorporate,
                        'industries'       => $industries,
                    ]
                );
        } else {
            Yii::app()->user->setFlash('error', 'Вы не являетесь реферралом!');
            $this->redirect('/dashboard');
        }

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

        if ($invite->receiverUser || YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)])) {
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

        $YumUser     = Yii::app()->request->getParam('YumUser');
        $YumProfile  = Yii::app()->request->getParam('YumProfile');
        $UserAccount = Yii::app()->request->getParam('UserAccountPersonal');

        if(null !== $YumUser && null !== $YumProfile && null !== $UserAccount)
        {
            $this->user->attributes = $YumUser;
            $profile->attributes = $YumProfile;
            if(!empty($YumProfile['email'])) {
                $profile->email = strtolower($YumProfile['email']);
            }
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
        $user_id = Yii::app()->session->get("user_id");
        $profile = YumProfile::model()->findByAttributes(['user_id' => $user_id]);

        $this->render('afterRegistration', [
            'isGuest' => Yii::app()->user->isGuest,
            'profile' => $profile,
        ]);
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

    public function actionRegistration()
    {
        if (false === Yii::app()->user->isGuest) {
            $this->redirect('/dashboard');
        }
        $account_type = Yii::app()->request->getParam('account-type', 'corporate');

        $UserAccountCorporateData = Yii::app()->request->getParam('UserAccountCorporate');
        $UserAccountPersonalData  = Yii::app()->request->getParam('UserAccountPersonal');
        $YumProfileData = Yii::app()->request->getParam('YumProfile');
        $YumUserData    = Yii::app()->request->getParam('YumUser');

        $user       = new YumUser('registration');
        $profile    = new YumProfile(($account_type === 'corporate')?'registration_corporate':'registration');
        $accountCorporate = new UserAccountCorporate($account_type);
        $accountPersonal = new UserAccountPersonal($account_type);

        $profile->firstname = $YumProfileData['firstname'];
        $profile->lastname  = $YumProfileData['lastname'];
        $profile->timestamp = gmdate("Y-m-d H:i:s");

        $emailIsExistAndNotActivated = false;

        if (Yii::app()->request->isPostRequest) {
            $user->attributes    = $YumUserData;
            $profile->attributes = $YumProfileData;

            $user->setUserNameFromEmail($profile->email);

            $user->createtime = time();
            $user->lastvisit = time();
            $user->lastpasswordchange = time();

            $isUserValid     = $user->validate(['password', 'password_again', 'agree_with_terms']);
            $isProfileValid  = $profile->validate(['firstname', 'lastname', 'email']);

            $accountPersonal->attributes = $UserAccountPersonalData;
            $isUserAccountPersonalValid  = $accountPersonal->validate(['professional_status_id']);
            $accountCorporate->attributes = $UserAccountCorporateData;
            $isUserAccountCorporateValid  = $accountCorporate->validate(['industry_id']);

            $emailIsExistAndNotActivated = YumProfile::model()->emailIsNotActiveValidationStatic($profile->email);
            if($emailIsExistAndNotActivated) {
                $profile->clearErrors();
            }

            $isUserBanned = YumProfile::model()->isAccountBannedStatic($profile->email);

            /**
             * if User is banned we need to replace email error with banned error
             */

            if($isUserBanned) {
                $emailIsExistAndNotActivated = $isUserBanned;
            }

            if($isUserAccountPersonalValid && $isUserAccountCorporateValid && $isProfileValid && $isUserValid) {
                $is_success_registration = $user->register($user->username, $user->password, $profile);

                if ($is_success_registration) {
                    $profile->user_id = $user->id;

                    if(false === $profile->save()) {
                        throw new Exception("Profile not saved!");
                    }

                    if($account_type === 'personal') {
                        $accountPersonal->user_id = $user->id;
                        if (false === $accountPersonal->save(true, ['user_id', 'professional_status_id'])) {
                            throw new Exception("Personal account not saved!");
                        }
                    } elseif($account_type === 'corporate') {
                        $accountCorporate->user_id = $user->id;
                        $accountCorporate->default_invitation_mail_text = 'Вопросы относительно тестирования вы можете задать по адресу '.$profile->email.', куратор тестирования - '.$profile->firstname.' '. $profile->lastname .'.';
                        $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);
                        $accountCorporate->setTariff($tariff, true);

                        $accountCorporate->invites_limit = Yii::app()->params['initialSimulationsAmount'];
                        $accountCorporate->save();

                        UserService::logCorporateInviteMovementAdd(
                            sprintf('Количество симуляций для нового аккаунта номер %s, емейл %s, задано равным %s по тарифному плану %s.',
                                $accountCorporate->user_id, $profile->email, $accountCorporate->getTotalAvailableInvitesLimit(), $tariff->label
                            ),
                            $accountCorporate,
                            $accountCorporate->getTotalAvailableInvitesLimit()
                        );
                        
                        if(false === $accountCorporate->save(true, ['user_id','default_invitation_mail_text','industry_id'])){
                            throw new Exception("Corporate account not saved!");
                        }
                    } else {
                        throw new Exception("Bad registration profile type.");
                    }

                    $this->sendRegistrationEmail($user);
                    Yii::app()->session->add("email", $profile->email);
                    Yii::app()->session->add("user_id", $profile->user_id);
                    $this->redirect(['afterRegistration']);
                } else {
                    throw new Exception("Registration is fail!");
                }
            }
        }

        $industries = [''=>'Выберите отрасль'];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }

        $statuses = [''=>'Выберите статус'];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = Yii::t('site', $status->label);
        }

        /**
         * Getting user simulation id to display the simulation result if user had completed the demo
         */
        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        $this->render(
            'registration',
            [
                'accountPersonal'             => $accountPersonal,
                'accountCorporate'            => $accountCorporate,
                'industries'                  => $industries,
                'statuses'                    => $statuses,
                'profile'                     => $profile,
                'user'                        => $user,
                'emailIsExistAndNotActivated' => $emailIsExistAndNotActivated,
                'account_type'                => $account_type,
                'display_results_for'         => $simulationToDisplayResults,
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
    public function sendPasswordRecoveryEmail($user)
    {
        if (!isset($user->profile->email)) {
            throw new CException(Yum::t('Email is not set when trying to send Recovery Email'));
        }

        $recoveryUrl = $this->createAbsoluteUrl(
            $this->createUrl('static/userAuth/recovery', [
                'key' => $user->activationKey,
                'email' => strtolower($user->profile->email)
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

        $email = strtolower(trim($email));
        $email = str_replace(' ', '+', $email);

        if (false === Yii::app()->user->isGuest && strtolower(Yii::app()->user->data()->profile->email) !== $email) {
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
        $YumProfile = YumProfile::model()->findByAttributes(['email'=>strtolower($email)]);

        if(null !== $YumUser) {
            $user = YumUser::model()->findByAttributes(['id'=>$YumProfile->user_id]);
            $user->update();
            return;
        }

        // If everything is set properly, let the model handle the Validation
        // and do the Activation
        $user = YumUser::activate($email, $key);

        if($user instanceof YumUser) {
            if(Yum::module('registration')->loginAfterSuccessfulActivation) {
                $login = new YumUserIdentity($user->username, false);
                $login->authenticate(true);
                Yii::app()->user->login($login);
            }

            if ($user->isPersonal()) {
                UserService::assignAllNotAssignedUserInvites(Yii::app()->user->data());
            }

            $this->redirect('/dashboard');
        } else {
            if(Yii::app()->user->isGuest){
                $this->layout = false;
                Yii::app()->user->setFlash(
                    (-1 == $user) ? 'error' : 'success',
                    $this->render(
                        Yum::module('registration')->activationFailureView,
                        array('error' => $user),
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

        if ($profile && !$profile->user->isActive() && !$profile->user->isBanned()) {
            $this->sendRegistrationEmail($profile->user);
            Yii::app()->session->add("email", strtolower($profile->email));
            Yii::app()->session->add("user_id", $profile->user_id);
            $this->redirect(['afterRegistration']);
        } else {
            if($profile->user->isBanned()) {
                Yii::app()->user->setFlash('error', 'Невозможно восстановить пароль - ваш аккаунт заблокирован');
            }
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
            $profile = YumProfile::model()->findByAttributes(['email' => strtolower($email)]);
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
            $profile = YumProfile::model()->findByAttributes(['email' => strtolower($email)]);
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

                if($recoveryForm->user->status->isBanned) {
                    Yii::app()->user->setFlash('error', 'Ваш аккаунт заблокирован');
                    $this->redirect('/');
                }

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

