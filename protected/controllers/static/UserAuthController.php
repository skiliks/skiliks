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

        $userReferralRecord = UserReferral::model()->findByAttributes(['uniqueid' => $refHash]);
        if($userReferralRecord !== null) {

            $existUser = YumProfile::model()->findByAttributes(["email" => $userReferralRecord->referral_email]);
            if($existUser !== null) {
                Yii::app()->user->setFlash('error', 'Пользователь '.$userReferralRecord->referral_email.' уже зарегистрирован.');
                $this->redirect('/');
            }

            $user  = new YumUser('registration');
            $user->setAttributes($this->getParam('YumUser'));
            $profile  = new YumProfile('registration_corporate');
            $profile->setAttributes($this->getParam('YumProfile'));
            $account_corporate = new UserAccountCorporate('corporate');
            $account_corporate->setAttributes($this->getParam('UserAccountCorporate'));
            if (Yii::app()->request->isPostRequest) {
                $user_password = $user->password;
                if(UserService::createReferral($user, $profile, $account_corporate, $userReferralRecord)) {
                    $user->authenticate($user_password);
                    Yii::app()->user->setFlash('success', 'Вы успешно зарегистрированы');
                    $this->redirect('/dashboard');
                }
            }
            $industries = UserService::getIndustriesForm();
            $this->render(
                'referral_registration',
                [
                    'refHash'          => $refHash,
                    'user'             => $user,
                    'profile'          => $profile,
                    'accountCorporate' => $account_corporate,
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
        $invite = Invite::model()->findByAttributes([ 'code' => $code ]);

        if (empty($invite)) {
            Yii::app()->user->setFlash('error', 'Код приглашения неверный.');
            $this->redirect('/');
        }

        if((int)$invite->status === Invite::STATUS_EXPIRED){
            Yii::app()->user->setFlash('error', 'Истёк срок ожидания ответа на приглашение');
            $this->redirect('/');
        }

        if((int)$invite->status === Invite::STATUS_DECLINED){
            Yii::app()->user->setFlash('error', 'Приглашение уже отклонено.'); // TODO:Проблемный попап
            $this->redirect('/');
        }

        if((int)$invite->status !== Invite::STATUS_PENDING){
            Yii::app()->user->setFlash('error', 'Пользователь по данному приглашению уже зарегистрирован.');
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

        if(Yii::app()->request->isPostRequest) {

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
                $username = $invite->email . date('Ymdhis') . rand(1000,9999);
                $username = preg_replace("/[^A-Za-z0-9 ]/", '', $username);
                $username = substr($username, 0, 199);
                $this->user->username = $username;
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

                    //YumUser::activate($profile->email, $this->user->activationKey);
                    // $this->user->authenticate($YumUser['password']);

                    $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);

                    $permission = new YumPermission();
                    $permission->principal_id = $this->user->id;
                    $permission->subordinate_id = $this->user->id;
                    $permission->action = $action->id;
                    $permission->type = 'user';
                    $permission->template = 1; // magic const
                    $permission->save(false);

                    UserService::assignAllNotAssignedUserInvites($this->user);

                    $this->redirect(['afterRegistration']);
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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/_page-registration.css');
        $this->addSiteCss('pages/_page-registration-1024.css');

        $this->addSiteJs('_page-registration.js');
        $this->addSiteJs('_terms-and-agreements.js');
        $this->addSiteJs('_decine-invite.js');

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
     * @param string $code
     */
    public function actionRegistrationSingleAccount()
    {
        Yii::app()->user->logout();
        sleep(0.5); // for safety

        $this->user = new YumUser('registration');

        $profile = new YumProfile('registration');

        $account = new UserAccountPersonal();
        $error = null;

        $YumUser     = Yii::app()->request->getParam('YumUser');
        $YumProfile  = Yii::app()->request->getParam('YumProfile');
        $UserAccount = Yii::app()->request->getParam('UserAccountPersonal');

        // if(null !== $YumUser && null !== $YumProfile && null !== $UserAccount) {
        if(Yii::app()->request->isPostRequest) {
            $this->user->attributes = $YumUser;
            $profile->attributes = $YumProfile;
            if(!empty($YumProfile['email'])) {
                $profile->email = strtolower($YumProfile['email']);
            }
            $account->attributes = $UserAccount;

            // Protect from "Wrong username" message - we need "Wrong email", from Profile form
            if (null == $this->user->username) {
                $username = $YumProfile['email'] . date('Ymdhis') . rand(1000,9999);
                $username = preg_replace("/[^A-Za-z0-9 ]/", '', $username);
                $username = substr($username, 0, 199);
                $this->user->username = $username;
            }

            $userValid = $this->user->validate();
            $profileValid = $profile->validate();

            if ($userValid && $profileValid) {
                $result = $this->user->register($this->user->username, $this->user->password, $profile);

                if (false !== $result) {
                    $account->user_id = $this->user->id;
                    $account->save(false);

                    $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);

                    $permission = new YumPermission();
                    $permission->principal_id = $this->user->id;
                    $permission->subordinate_id = $this->user->id;
                    $permission->action = $action->id;
                    $permission->type = 'user';
                    $permission->template = 1; // magic const
                    $permission->save(false);

                    UserService::assignAllNotAssignedUserInvites($this->user);

                    Yii::app()->session["user_id"] = $this->user->id;

                    $this->redirect(['afterRegistration']);
                } else {
                    $this->user->password = '';
                    $this->user->password_again = '';

                    Yii::app()->user->setFlash(
                        'error',
                        'Ошибки регистрации. Обратитесь в <a href="/contacts">службу поддержки</a>.'
                    );
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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/_page-registration.css');
        $this->addSiteCss('pages/_page-registration-1024.css');

        $this->addSiteJs('_page-registration.js');
        $this->addSiteJs('_terms-and-agreements.js');

        $this->render(
            'registration_single_account',
            [
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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/_page-registration.css');
        $this->addSiteCss('pages/_page-registration-1024.css');

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

        // getting parameters from
        $account_type = $this->getParam('account-type', 'corporate');
        $user  = new YumUser('registration');
        $user->setAttributes($this->getParam('YumUser'));
        $profile  = new YumProfile(($account_type === 'corporate')?'registration_corporate':'registration');
        $profile->setAttributes($this->getParam('YumProfile'));
        $account_corporate = new UserAccountCorporate($account_type);
        $account_corporate->setAttributes($this->getParam('UserAccountCorporate'));
        $account_personal = new UserAccountPersonal($account_type);
        $account_personal->setAttributes($this->getParam('UserAccountPersonal'));
        $industries = UserService::getIndustriesForm();
        $statuses   = UserService::getStatusesForm();

        // Getting user simulation id to display the simulation result if user had completed the demo
        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        // preparing the registration

        // If user didn't send any data
        if (Yii::app()->request->isPostRequest) {
            if($account_type === 'corporate') {
                if(UserService::createCorporateAccount($user, $profile, $account_corporate)){
                    UserService::sendRegistrationEmail($user);
                    $this->redirect(['afterRegistration']);
                }
            }elseif($account_type === 'personal'){
                if(UserService::createPersonalAccount($user, $profile, $account_personal)){
                    UserService::sendRegistrationEmail($user);
                    $this->redirect(['afterRegistration']);
                }
            }else{
                throw new Exception("Не выбран тип аккаунта");
            }
        }

        // rendering the view
        $this->render(
            'registration',
            [
                'accountPersonal'             => $account_personal,
                'accountCorporate'            => $account_corporate,
                'profile'                     => $profile,
                'user'                        => $user,
                'account_type'                => $account_type,
                'industries'                  => $industries,
                'statuses'                    => $statuses,
                'display_results_for'         => $simulationToDisplayResults
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

        $mailOptions          = new SiteEmailOptions();
        $mailOptions->from    = Yum::module('registration')->recoveryEmail;
        $mailOptions->to      = $user->profile->email;
        $mailOptions->subject = 'Восстановление пароля для сайта ' . Yii::app()->params['server_domain_name'];
        $mailOptions->h1      = sprintf('Приветствуем, %s!', $user->getFormattedFirstName());
        $mailOptions->text1   = '
            <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
            Вы просили обновить данные вашего аккаунта.</p>
            <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
            Пожалуйста, зайдите в <a  style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva,
            sans-serif;font-size:14px;" href="'.$recoveryUrl.'">ваш кабинет</a> для восстановления пароля и/или логина.</p>
        ';

        $sent = UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_FIKUS);

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
     *
     * @parameter string $email
     * @parameter string $key
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
                UserService::authenticate($user);
//                $login = new YumUserIdentity($user->username, false);
//                $login->authenticate(true);
//                Yii::app()->user->login($login, 60);
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

            UserService::sendRegistrationEmail($profile->user);
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

    /**
     * Восстановление пароля
     *
     * @param string $email, null - значение по умолчанию необходимо
     * @param string $key  , null - значение по умолчанию необходимо
     */
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
                          UserService::authenticate($user);
//                        $login = new YumUserIdentity($user->username, false);
//                        $login->authenticate(true);
//                        Yii::app()->user->login($login, 60);
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
            if(isset($_POST['ajax']) && $_POST['ajax'] === 'password-recovery-form')
            {
                $errors = json_decode(CActiveForm::validate($recoveryForm));

                if (0 < count($errors)) {
                    echo json_encode($errors);
                    Yii::app()->end();
                }
            }

            if ($recoveryForm->validate() && $recoveryForm->user instanceof YumUser && $recoveryForm->user->status > 0) {
                $user = $recoveryForm->user;

                if($recoveryForm->user->isBanned()) {
                    Yii::app()->user->setFlash('error', 'Ваш аккаунт заблокирован');
                    $this->redirect('/');
                }

                $user->generateActivationKey();
                $result = $this->sendPasswordRecoveryEmail($user);

                if ($result) {
                    Yii::app()->user->setFlash('password-recovery', 'На ваш email выслана инструкция по смене пароля.');
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

