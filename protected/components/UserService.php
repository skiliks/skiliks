<?php

class UserService {

    const CAN_START_SIMULATION_IN_DEV_MODE = 'start_dev_mode';
    const CAN_START_FULL_SIMULATION = 'run_full_simulation';

    public static $developersEmails = [
        "'r.kilimov@gmail.com'",
        "'andrey@kostenko.name'",
        "'personal@kostenko.name'",
        "'a.levina@gmail.com'",
        "'gorina.mv@gmail.com'",
        "'v.logunov@yahoo.com'",
        "'nikoolin@ukr.net'",
        "'leah.levina@gmail.com'",
        "'lea.skiliks@gmail.com'",
        "'andrey3@kostenko.name'",
        "'skiltests@yandex.ru'",
        "'didmytime@bk.ru'",
        "'gva08@yandex.ru'",
        "'tony_acm@ukr.net'",
        "'tony_perfectus@mail.ru'",
        "'N_ninok1985@mail.ru'",
        "'tony.pryanichnikov@gmail.com'",
        "'svetaswork@gmail.com'",
        "'tatyana_pryan@mail.ru'",
    ];

    /**
     * Получить список режимов запуска симуляции доступных пользователю: {promo, developer}
     * @param int $uid 
     * @return array
     */
    public static function getModes($user)
    {
        $modes = [];
        $modes[1] = Simulation::MODE_PROMO_LABEL;

        if ($user->can(self::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $modes[2] = Simulation::MODE_DEVELOPER_LABEL;
        }
        
        return $modes;
    }
    
    public static function addUserSubscription($email)
    {
        $response = ['result'  => 0];

        if(empty($email)) {
                $response['message'] =  "Enter your email address";
        }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] =  "Email entered incorrectly";
        } elseif (EmailsSub::model()->findByEmail($email)) {
            $response['message'] =  "Email - ${email} has been already added before!";
        } else {
            $subscription = new EmailsSub();
            $subscription->email = strtolower($email);
            $subscription->save();

            $response['result'] =  1;
            $response['message'] =  "Email ${email} has been successfully added!";
        }

        return $response;
    }

    public static function isCorporateEmail($email)
    {
        $domain = substr($email, strpos($email, '@') + 1);

        $counter = FreeEmailProvider::model()->countByAttributes([
            'domain' => $domain
        ]);

        if(0 != $counter) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Добавляет лог о состоянии баланса инвайтов по сккаунту
     *
     * @param string, $action
     * @param UserAccountCorporate $account
     * @param $amountBeforeTransaction
     * @param $isAdd
     * @param null $comment
     */
    public static function logCorporateInviteMovementAdd($message, $account, $amountBeforeTransaction, $comment = null )
    {
        if (null == $account) {
            return false;
        }

        if (false === $account instanceof UserAccountCorporate) {
            return false;
        }

        $log = new LogAccountInvite();
        $log->message = $message;
        $log->user_id = $account->user_id;
        $log->direction = ($account->getTotalAvailableInvitesLimit() > $amountBeforeTransaction) ? 'увеличено' : 'уменьшено';
        $log->limit_after_transaction = $account->invites_limit;
        $log->invites_limit_referrals = $account->referrals_invite_limit;
        $log->amount = $amountBeforeTransaction;
        $log->date = date('Y-m-d H:i:s');
        if(false == (Yii::app() instanceof CConsoleApplication) && Yii::app()->user->data()->id !== null) {
            try {
                $log->comment = $comment.'. Инициатор, пользователь '.Yii::app()->user->data()->id.', '.
                    Yii::app()->user->data()->profile->firstname.' '.Yii::app()->user->data()->profile->lastname.'.';
            } catch (Exception $e) {
                $log->comment = $comment;
            }
        } else {
            $log->comment = $comment;
        }

        $log->save(false);
    }

    /**
     * @param YumUser $user
     */
    public static function assignAllNotAssignedUserInvites(YumUser $user)
    {
        $invites = Invite::model()->findAllByAttributes([
            'email' => strtolower($user->profile->email)
        ]);

        foreach ($invites as $invite) {
            if (null !== $invite->receiver_id) {
                continue;
            }
            $invite->receiver_id = $user->id;
            $invite->receiverUser = $user;
            $invite->save(false);
        }
    }

    /**
     * Returns personal user statuses form
     * @return array
     */

    public static function getStatusesForm() {
        $statuses = [''=>'Выберите статус'];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = Yii::t('site', $status->label);
        }
        return $statuses;
    }

    /**
     * Returns corporate user industries form
     * @return array
     */

    public static function getIndustriesForm() {
        $industries = [''=>'Выберите отрасль'];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = Yii::t('site', $industry->label);
        }
        return $industries;
    }

    /**
     * Sends user registration email.
     * @param $user
     * @return bool
     * @throws CException
     */

    public static function sendRegistrationEmail(YumUser $user)
    {

        Yii::app()->session->add("email", $user->profile->email);
        Yii::app()->session->add("user_id", $user->id);
        if (!isset($user->profile->email)) {
            throw new CException(Yum::t('Email is not set when trying to send Registration Email'));
        }
        $activation_url = $user->getActivationUrl();

        $body = Yii::app()->getController()->renderPartial('//global_partials/mails/registration', ['link' => $activation_url], true);

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

    public static function createCorporateAccount(YumUser &$user, YumProfile &$profile, UserAccountCorporate &$account_corporate) {

        if(self::createUserAndProfile($user, $profile)
            && $account_corporate->validate(['industry_id'])
            && $user->register($user->username, $user->password, $profile)) {

            $user->save(false);
            $profile->user_id = $user->id;
            $profile->save(false);

            $account_corporate->user_id = $user->id;
            $account_corporate->default_invitation_mail_text = 'Вопросы относительно тестирования вы можете задать по адресу '.$profile->email.', куратор тестирования - '.$profile->firstname.' '. $profile->lastname .'.';
            $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);
            $account_corporate->setTariff($tariff, true);

            $account_corporate->invites_limit = Yii::app()->params['initialSimulationsAmount'];
            $account_corporate->save(false);

            UserService::logCorporateInviteMovementAdd(
                sprintf('Количество симуляций для нового аккаунта номер %s, емейл %s, задано равным %s по тарифному плану %s.',
                    $account_corporate->user_id, $profile->email, $account_corporate->getTotalAvailableInvitesLimit(), $tariff->label
                ),
                $account_corporate,
                $account_corporate->getTotalAvailableInvitesLimit()
            );
            return true;
        }
        return false;
    }

    public static function createPersonalAccount(YumUser &$user, YumProfile &$profile, UserAccountPersonal &$account_personal) {
        $isValidUserAndProfile = self::createUserAndProfile($user, $profile);
        $isValidCorporate = $account_personal->validate(['professional_status_id']);
        if( $isValidUserAndProfile
            && $isValidCorporate ) {
            if(!$user->register($user->username, $user->password, $profile)){
                return false;
            }
            $user->save(false);
            $profile->user_id = $user->id;
            $profile->save(false);

            $account_personal->user_id = $user->id;

            $account_personal->save(false);

            return true;
        }
        return false;
    }

    public static function createUserAndProfile(YumUser &$user, YumProfile &$profile) {
        $user->setUserNameFromEmail($profile->email);
        $user->createtime = time();
        $user->lastvisit = time();
        $user->lastpasswordchange = time();
        $profile->timestamp = time();
        $isValidUser = $user->validate(['password', 'password_again', 'agree_with_terms']);
        $isValidProfile =  $profile->validate(['firstname', 'lastname', 'email']);
        return $isValidUser && $isValidProfile;
    }

    public static function sendInvite(YumUser $user, $profile, Invite &$invite, $is_display_results) {

        $validPrevalidate = null;
        if($profile !== null && $profile->user->isCorporate()) {
            $validPrevalidate = false;
            Yii::app()->user->setFlash('error', sprintf(
                'Данный пользователь с e-mail: '.$invite->email.' является корпоративным. Вы можете отправлять
                     приглашения только персональным и незарегистрированным пользователям'
            ));
        } else {

            $invite->code = uniqid(md5(mt_rand()));
            $invite->owner_id = $user->id;
            $invite->can_be_reloaded = true;

            // What happens if user is registered, but not activated??
            $profile = YumProfile::model()->findByAttributes([
                'email' => strtolower($invite->email)
            ]);
            if ($profile) {
                $invite->receiver_id = $profile->user->id;
            }

            $invite->scenario_id = Scenario::model()
                ->findByAttributes(['slug' => Scenario::TYPE_FULL])
                ->getPrimaryKey();

            $invite->tutorial_scenario_id = Scenario::model()
                ->findByAttributes(['slug' => Scenario::TYPE_TUTORIAL])
                ->getPrimaryKey();
            $user->getAccount()->refresh();
            // send invitation
            if ($invite->validate() && 0 < $user->getAccount()->getTotalAvailableInvitesLimit()) {
                $invite->markAsSendToday();
                $user->account_corporate->default_invitation_mail_text = $invite->message;
                $user->account_corporate->save();
                $invite->message = preg_replace('/(\r\n)/', '<br>', $invite->message);
                $invite->message = preg_replace('/(\n\r)/', '<br>', $invite->message);
                $invite->message = preg_replace('/\\n|\\r/', '<br>', $invite->message);
                $invite->is_display_simulation_results = (int) !$is_display_results;
                $invite->setExpiredAt();
                $invite->save(false);
                InviteService::logAboutInviteStatus($invite, sprintf(
                    'Приглашение для %s создано в корпоративном кабинете пользователя %s.',
                    $invite->email,
                    $user->profile->email
                ));

                self::sendEmailInvite($invite);
                $initValue = $user->getAccount()->getTotalAvailableInvitesLimit();

                // decline corporate user invites_limit
                $user->getAccount()->decreaseLimit();
                $user->getAccount()->save();
                $user->refresh();

                UserService::logCorporateInviteMovementAdd(sprintf("Симуляция списана за отправку приглашения номер %s для %s",
                    $invite->id, $invite->email), $user->getAccount(), $initValue);

                return true;
                //$this->redirect('/dashboard');
            } elseif ($user->getAccount()->getTotalAvailableInvitesLimit() < 1 ) {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Беспплатный тарифный план использован. Пожалуйста, <a class="feedback">свяжитесь с нами</a>>, чтобы приобрести пакет симуляций'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Неизвестная ошибка.<br/>Приглашение не отправлено.'));
            }
        }
        return $validPrevalidate;

    }

    public static function sendEmailInvite(Invite $invite) {

        if (empty($invite->email)) {
            throw new CException(Yum::t('Email is not set when trying to send invite email. Wrong invite object.'));
        }

        $inviteEmailTemplate = Yii::app()->params['emails']['inviteEmailTemplate'];

        $body = self::renderEmailPartial($inviteEmailTemplate, [
            'invite' => $invite
        ]);

        $mail = [
            'from'        => Yum::module('registration')->registrationEmail,
            'to'          => $invite->email,
            'subject'     => 'Приглашение пройти симуляцию на Skiliks.com',
            'body'        => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top.png',
                    'cid'      => 'mail-top',
                    'name'     => 'mailtop',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top-2.png',
                    'cid'      => 'mail-top-2',
                    'name'     => 'mailtop2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-1.png',
                    'cid'      => 'mail-right-1',
                    'name'     => 'mailright1',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-2.png',
                    'cid'      => 'mail-right-2',
                    'name'     => 'mailright2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-3.png',
                    'cid'      => 'mail-right-3',
                    'name'     => 'mailright3',
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

        $invite->markAsSendToday();
        $invite->save();

        $sent = MailHelper::addMailToQueue($mail);
        return $sent;
    }

    public static function renderEmailPartial($_partial_ ,$_data_=null)
    {
          $_viewFile_ = __DIR__.'/../views/global_partials/mails/'.$_partial_.'.php';
          if(!file_exists($_viewFile_)) {
              throw new Exception("Email partial {$_partial_} not found in path {$_viewFile_}");
          }
          if( is_array($_data_) ) {
                extract($_data_,EXTR_PREFIX_SAME,'data');
                ob_start();
                ob_implicit_flush(false);
                require($_viewFile_);
                return ob_get_clean();
          } else {
              throw new Exception("Bad data, must be array");
          }

    }

    public static function renderPartial($_partial_ ,$_data_=null)
    {
        $_viewFile_ = __DIR__.'/../views/'.$_partial_.'.php';
        if(!file_exists($_viewFile_)) {
            throw new Exception("Email partial {$_partial_} not found in path {$_viewFile_}");
        }
        if( is_array($_data_) ) {
            extract($_data_,EXTR_PREFIX_SAME,'data');
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        } else {
            throw new Exception("Bad data, must be array");
        }

    }


    public static function getInviteHimSelf(YumUser $user, Scenario $scenario) {
        // check and add trial full version {

        $notUsedSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => $user->id,
            'scenario_id' => $scenario->id,
            'email'       => strtolower($user->profile->email),
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedSimulations)) {
            $i = 0;
            foreach ($notUsedSimulations as $key => $notUsedSimulation) {

                if (0 < $i) {
                    $notUsedSimulation->delete();
                    unset($notUsedSimulations[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedSimulations)) {

            $notUsedSimulations[] = Invite::addFakeInvite($user, $scenario);

        }
        return $notUsedSimulations;
    }


    /**
     * @param YumUser $user
     * @param $assetsUrl
     * @param $start
     * @param $mode
     * @param $type
     * @param $invite_id
     * @return SimulationChecks
     */
    public static function getSimulationContentsAndConfigs(YumUser $user, $assetsUrl, $mode, $type, $invite_id, $start = null) {

        $result = new SimulationChecks();

        if (!$user->isAuth() && $type != Scenario::TYPE_LITE) {
            return $result->setRedirect('/user/auth');
        }

        if (Simulation::MODE_DEVELOPER_LABEL == $mode
            && false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            return $result->setRedirect('/dashboard');
        }

        if ($mode !== Simulation::MODE_PROMO_LABEL && $mode !== Simulation::MODE_DEVELOPER_LABEL) {
            return $result->setRedirect('/dashboard');
        }

        if (null === $invite_id && false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            return $result->setRedirect('/dashboard');
        }

        if (null !== $invite_id) {
            /** @var Invite $invite */
            $invite = Invite::model()->findByAttributes(['id' => $invite_id]);
            if (null === $invite) {
                Yii::app()->user->setFlash('error', 'Выберите приглашение по которому вы хотите начать симуляцию');
                return $result->setRedirect('/dashboard');
            }

            $invite->refresh(); // Important! Prevent caching
            MailHelper::sendEmailIfSuspiciousActivity($invite);
        }

        if (isset($invite) &&
            $invite->scenario->slug == Scenario::TYPE_FULL &&
            false == $invite->canUserSimulationStart()
        ) {
            Yii::app()->user->setFlash('error', 'У вас нет прав для старта этой симуляции');
            return $result->setRedirect('/dashboard');
        }

        if (isset($invite) && $invite->receiver_id !== $user->id) {
            return $result->setRedirect('/dashboard');
        }

        if (isset($invite) && false == $invite->can_be_reloaded) {
            Yii::app()->user->setFlash('error',
                'Прохождение симуляции было прервано. <br/> Свяжитесь с работодателем ' .
                'чтобы он выслал вам новое приглашение или со службой тех.поддержки ' .
                'чтобы восстановить доступ к прохождению симуляции.'
            );
            return $result->setRedirect('/dashboard');
        }

        if ( isset($invite)
            && $start !== 'full'
            && null !== $invite->tutorial
            && $mode !== 'developer'
            && null === $invite->tutorial_finished_at) {
            $type = $invite->tutorial->slug;
            $tutorial = true;
            $invite->tutorial_displayed_at = date('Y-m-d H:i:s');
            $invite->save(false);
            InviteService::logAboutInviteStatus($invite, 'Пользователь прошел туториал');
        }

        /** @var Scenario $scenario */
        $scenario = Scenario::model()->findByAttributes([
            'slug' => $type
        ]);

        $scenarioConfigLabelText = $scenario->scenario_config->scenario_label_text;

        if (null === $scenario) {
            return $result->setRedirect('/dashboard');
        }

        if (isset($invite) && Scenario::TYPE_TUTORIAL == $type
            && $user->isCorporate() && (int)$user->account_corporate->getTotalAvailableInvitesLimit() == 0
        ) {
            Yii::app()->user->setFlash('error', 'У вас закончились приглашения');
            return $result->setRedirect('/profile/corporate/tariff');
        }

        $config = array_merge(
            Yii::app()->params['public'],
            [
                'assetsUrl' => $assetsUrl,
                'mode' => $mode,
                'type' => $type,
                'start' => $scenario->scenario_config->game_start_timestamp,
                'end' => $scenario->scenario_config->game_end_workday_timestamp,
                'finish' => $scenario->scenario_config->game_end_timestamp,
                'badBrowserUrl' => '/old-browser',
                'oldBrowserUrl' => '/old-browser',
                'dummyFilePath' => $assetsUrl . '/img/kotik.jpg',
                'invite_id'     => $invite_id,
                'game_date_text'=>$scenario->scenario_config->game_date_text,
                'game_date_data'=>$scenario->scenario_config->game_date_data
            ]
        );

        if (!empty($tutorial)) {
            $config['result-url'] = Yii::app()->createUrl('static/site/simulation', [
                'mode' => $mode,
                'type' => isset($invite) ? Scenario::model()->findByPk($invite->scenario_id)->slug : Scenario::TYPE_FULL,
                'invite_id' => $invite_id,
            ]);
        }

        return $result->setData([
            'config'        => CJSON::encode($config),
            'assetsUrl'     => $assetsUrl,
            'inviteId'      => (null === $invite_id) ? 'null' : $invite_id,
            'scenarioLabel' => $scenarioConfigLabelText
        ]);
    }

    public static function inviteExpired(){
        //Invites
        $time = time() - Yii::app()->params['cron']['InviteExpired'];

        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        /** @var $invites Invite[] */
        $invites = Invite::model()->findAll(
            sprintf("status IN (%s, %s, %s) AND '%s' >= expired_at AND (owner_id != receiver_id OR receiver_id is NULL) AND scenario_id = %s",
                Invite::STATUS_PENDING,
                Invite::STATUS_ACCEPTED,
                Invite::STATUS_IN_PROGRESS,
                date("Y-m-d H:i:s"),
                $fullScenario->id
            ));

        foreach($invites as $invite){

            $initValue = $invite->ownerUser->getAccount()->getTotalAvailableInvitesLimit();

            if ($invite->inviteExpired()) {
                echo sprintf("%s mark invite as expired \n", $invite->id);
                $invite->ownerUser->getAccount()->refresh();

                UserService::logCorporateInviteMovementAdd(sprintf("Приглашения номер %s для %s устарело. В аккаунт возвращена одна симуляция.",
                    $invite->id, $invite->email),  $invite->ownerUser->getAccount(), $initValue);
            }
        }

        /* @var $users UserAccountCorporate[] */
        $accounts = UserAccountCorporate::model()->findAll(
            sprintf("'%s' < tariff_expired_at AND tariff_expired_at <= '%s'",
                date("Y-m-d 00:00:00"),
                date("Y-m-d 23:59:59")
            ));

        if(null !== $accounts){
            /* @var $user UserAccountCorporate */
            foreach($accounts as $account) {
                $account->is_display_tariff_expire_pop_up = 1;
                if((int)$account->invites_limit !== 0) {
                    $initValue = $account->getTotalAvailableInvitesLimit();

                    $account->invites_limit = 0;
                    $account->update();

                    UserService::logCorporateInviteMovementAdd('Тарифный план '.$account->tariff->label.' истёк. Количество доступных симуляция обнулено.', $account, $initValue);
                }

                // send email for any account {
                $emailTemplate = Yii::app()->params['emails']['tariffExpiredTemplateIfInvitesZero'];

                $body = self::renderEmailPartial($emailTemplate, [
                    'user' => $account->user
                ]);

                $mail = [
                    'from'        => 'support@skiliks.com',
                    'to'          => $account->user->profile->email,
                    'subject'     => 'Истёк тарифный план',
                    'body'        => $body,
                    'embeddedImages' => [
                        [
                            'path'     => Yii::app()->basePath.'/assets/img/mail-top.png',
                            'cid'      => 'mail-top',
                            'name'     => 'mailtop',
                            'encoding' => 'base64',
                            'type'     => 'image/png',
                        ],[
                            'path'     => Yii::app()->basePath.'/assets/img/mail-top-2.png',
                            'cid'      => 'mail-top-2',
                            'name'     => 'mailtop2',
                            'encoding' => 'base64',
                            'type'     => 'image/png',
                        ],[
                            'path'     => Yii::app()->basePath.'/assets/img/mail-right-1.png',
                            'cid'      => 'mail-right-1',
                            'name'     => 'mailright1',
                            'encoding' => 'base64',
                            'type'     => 'image/png',
                        ],[
                            'path'     => Yii::app()->basePath.'/assets/img/mail-right-2.png',
                            'cid'      => 'mail-right-2',
                            'name'     => 'mailright2',
                            'encoding' => 'base64',
                            'type'     => 'image/png',
                        ],[
                            'path'     => Yii::app()->basePath.'/assets/img/mail-right-3.png',
                            'cid'      => 'mail-right-3',
                            'name'     => 'mailright3',
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

                try {
                    MailHelper::addMailToQueue($mail);
                    echo $account->user->profile->email."\n";
                } catch (phpmailerException $e) {
                    echo $e;
                }
                // send email for any account }
            }
        }
    }

}


