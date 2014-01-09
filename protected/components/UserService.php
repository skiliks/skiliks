<?php

/**
 * Class UserService
 */
class UserService {

    /**
     *
     */
    const CAN_START_SIMULATION_IN_DEV_MODE = 'start_dev_mode';
    /**
     *
     */
    const CAN_START_FULL_SIMULATION = 'run_full_simulation';

    /**
     * @var array
     */
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

    /**
     * Заносит email в базу подписчиков
     * @param $email
     * @return array данные для фронтэнда 1 успешно, 0 нет
     */
    public static function addUserSubscription($email)
    {
        $response = ['result'  => 0];

        if(empty($email)) {
                $response['message'] =  "Enter your email address";
        }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] =  "Email entered incorrectly";
        } elseif (EmailsSub::model()->findByAttributes(['email' => $email])) {
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

    /**
     * Проверка по базе что email корпоративный
     * @param string $email
     * @return bool
     */
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
        // "email" и "user_id" заносятся в сессию для авторизации --
        // или они потом используются для авротизяции, после перезагрузки страницы?
        Yii::app()->session->add("email", $user->profile->email);
        Yii::app()->session->add("user_id", $user->id);

        if (!isset($user->profile->email)) {
            throw new CException(Yum::t('Email is not set when trying to send Registration Email'));
        }

        $mailOptions          = new SiteEmailOptions();
        $mailOptions->from    = Yum::module('registration')->registrationEmail;
        $mailOptions->to      = $user->profile->email;
        $mailOptions->subject = 'Активация на сайте skiliks.com';

        $mailOptions->h1      = 'Благодарим вас за выбор skiliks!';
        $mailOptions->text1   = '
            <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
            Пожалуйста, <a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
            href="' . $user->getActivationUrl() .' ?>">
            активируйте</a> ваш аккаунт.</p>
        ';

        $sent = UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_ANJELA);

        return $sent;
    }

    /**
     * Создает копоративного пользователя
     * @param YumUser $user
     * @param YumProfile $profile
     * @param UserAccountCorporate $account_corporate
     * @return bool
     */
    public static function createCorporateAccount(YumUser &$user, YumProfile &$profile, UserAccountCorporate &$account_corporate) {

        $isValidUserAndProfile = self::createUserAndProfile($user, $profile);
        $isValidCorporate = $account_corporate->validate(['industry_id']);

        if( $isValidUserAndProfile
            && $isValidCorporate) {
            if(!$user->register($user->username, $user->password, $profile)){
                return false;
            }
            $user->save(false);
            $profile->user_id = $user->id;
            $profile->save(false);

            $account_corporate->user_id = $user->id;
            $account_corporate->default_invitation_mail_text = 'Вопросы относительно тестирования вы можете задать по адресу '.$profile->email.', куратор тестирования - '.$profile->firstname.' '. $profile->lastname .'.';
            $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE_FREE]);
            $account_corporate->setTariff($tariff, true);
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

    /**
     * Создает персонального пользователя
     * @param YumUser $user
     * @param YumProfile $profile
     * @param UserAccountPersonal $account_personal
     * @return bool
     */
    public static function createPersonalAccount(YumUser &$user, YumProfile &$profile, UserAccountPersonal &$account_personal) {
        $isValidUserAndProfile = self::createUserAndProfile($user, $profile);
        $isValidPersonal = $account_personal->validate(['professional_status_id']);
        if( $isValidUserAndProfile
            && $isValidPersonal ) {
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

    /**
     * Создает профиль
     * @param YumUser $user
     * @param YumProfile $profile
     * @return bool
     */
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

    /**
     * Наполняет приглашение правильными данными.
     * Валидирует отправителя и получателя. (количество доступных симуляций, типы профилей, т.п.)
     *
     * @param YumUser $user - кто шлёт приглашение
     * @param $profile - кому шлют приглашение
     * @param Invite $invite - пустой объект приглашения
     * @param $is_display_results - это опция в приглашении
     *
     * @return bool|null
     */
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
                $invite->setTariffPlan();
                $invite->setExpiredAt();
                $invite->save(false);

                // check is display pop up about referral`s model {
                $userInvitesCount = Invite::model()->countByAttributes([
                    'owner_id'    => $user->id,
                    'scenario_id' => $invite->scenario_id,
                ]);

                $countOfInvitesToShowPopup = Yii::app()->params['countOfInvitesToShowReferralPopup'];
                if($userInvitesCount == $countOfInvitesToShowPopup) {
                    $user->getAccount()->is_display_referrals_popup = 1;
                    $user->getAccount()->save();
                }
                // check is display pop up about referral`s model }

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

    /**
     * Ставит в очередь писем письмо-приглашение пройти симуляцию.
     *
     * @param Invite $invite
     *
     * @return bool
     *
     * @throws CException
     */
    public static function sendEmailInvite(Invite $invite) {

        if (empty($invite->email)) {
            throw new CException(Yum::t('Email is not set when trying to send invite email. Wrong invite object.'));
        }

        $innerText = '';

        if ($invite->receiverUser && !$invite->receiverUser->isActive()) {
            $innerText .= 'Пожалуйста, <a href="' . $invite->receiverUser->getActivationUrl() . '">активируйте ваш аккаунт</a>,
            выберите индивидуальный профиль, войдите в свой кабинет
            и примите приглашение на тестирование для прохождения симуляции.';
        } elseif ($invite->receiverUser && $invite->receiverUser->isPersonal()) {
            $innerText .= 'Пожалуйста,
            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
            href="' . Yii::app()->createAbsoluteUrl('/user/auth') . '">
                зайдите
            </a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.';
        } elseif ($invite->receiverUser && $invite->receiverUser->isCorporate()) {
            $innerText .= 'Пожалуйста,
            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
            href="' . $invite->getInviteLink() . '">
                создайте личный профиль
            </a> или
            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
            href="' . Yii::app()->createAbsoluteUrl('/dashboard') . '">войдите в личный кабинет</a>
            и примите приглашение на тестирование для прохождения симуляции.';
        } else {
            $innerText .= 'Пожалуйста,
            <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
            href="' . $invite->getInviteLink() . '">зарегистрируйтесь</a>,
            войдите в свой кабинет и примите приглашение на тестирование для прохождения симуляции.';
        }

        $mailOptions          = new SiteEmailOptions();
        $mailOptions->from    = Yum::module('registration')->registrationEmail;
        $mailOptions->to      = $invite->email;
        $mailOptions->subject = 'Приглашение пройти симуляцию на ' . Yii::app()->params['server_domain_name'];
        $mailOptions->h1      = $invite->getReceiverFirstName() . ', приветствуем вас!';
        $mailOptions->setText('
            <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                Компания '. $invite->ownerUser->account_corporate->company_name .' предлагает вам пройти тест "Базовый менеджмент".<br/>
                <a target="_blank" style="text-decoration: none;" href="' . Yii::app()->createAbsoluteUrl('static/pages/product') .'">"Базовый менеджмент"</a>
                - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.<br/>
            </p>
             <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
            '.$invite->message.
            '</p>
             <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">'
            . $innerText .
            '</p>');

        $invite->markAsSendToday();
        $invite->save();

        $sent = UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_ANJELA);

        return $sent;
    }

    /**
     * Скопирован с Yii.
     * В Yii нет метода рендера вьюхи в компоненте.
     *
     * @param $_partial_
     * @param null $_data_
     *
     * @return string
     *
     * @throws Exception
     */
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


    /**
     * Скопирован с Yii.
     * Делает то же что и renderPartial, но путь $_viewFile_ уточнён "/global_partials/mails/"
     * @param string $_partial_ название шаблона
     * @param array $_data_ данные
     * @return string данные шаблона
     * @throws Exception
     */
    public static function renderEmailPartial($_partial_ ,$_data_ = [])
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

    /**
     * Возвращает не использованые инвайты сама себе
     * @param YumUser $user
     * @param Scenario $scenario
     *
     * @return Invite[]
     */
    public static function getSelfToSelfInvite(YumUser $user, Scenario $scenario) {
        // check and add trial full version {

        $notUsedInvites = Invite::model()->findAllByAttributes([
            'receiver_id' => $user->id,
            'scenario_id' => $scenario->id,
            'email'       => strtolower($user->profile->email),
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim invite {
        if (1 < count($notUsedInvites)) {
            $i = 0;
            foreach ($notUsedInvites as $key => $notUsedInvite) {

                if (0 < $i) {
                    $notUsedInvite->delete();
                    unset($notUsedInvite[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedInvites)) {
            $notUsedInvites[] = Invite::addFakeInvite($user, $scenario);
        }

        return $notUsedInvites;
    }


    /**
     * Возвращает данные и конфиги, необходимые для рендера SiteController->actionSimulation
     *
     * @param YumUser $user
     * @param string $assetsUrl
     * @param string $start
     * @param string $mode
     * @param string $type
     * @param integer $invite_id
     *
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
            MailHelper::sendEmailAboutActivityToStudySimulation($invite);
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
                'oldBrowserUrl' => '/system-mismatch',
                'dummyFilePath' => $assetsUrl . '/img/kotik.jpg',
                'invite_id'     => $invite_id,
                'game_date_text'=>$scenario->scenario_config->game_date_text,
                'game_date_data'=>$scenario->scenario_config->game_date_data
            ]
        );

        $config['storageURL'] = $config['storageURL'].$scenario->slug;

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

    /**
     * Запускает устаревание тарифных планов
     *
     * @return array
     */
    public static function tariffExpired() {

        /* @var $users UserAccountCorporate[] */
        $tariff_plans = TariffPlan::model()->findAll(
            sprintf("'%s' < finished_at AND finished_at <= '%s' AND status = '%s'",
                date("Y-m-d 00:00:00"),
                date("Y-m-d 23:59:59"),
                TariffPlan::STATUS_ACTIVE
            ));

        $expiredAccounts = [];

        if( null !== $tariff_plans ) {
            /* @var $tariff_plan TariffPlan */
            foreach( $tariff_plans as $tariff_plan ) {
                $account = $tariff_plan->user->account_corporate;
                $initValue = $account->getTotalAvailableInvitesLimit();
                UserService::logCorporateInviteMovementAdd('Тарифный план '.$account->tariff->label.' истёк. Количество доступных симуляция обнулено.', $account, $initValue);

                // процесс смены тарифного плана при истечении предыдущего {
                $pending = $account->getPendingTariffPlan();
                if(null === $pending) {
                    $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_FREE]);
                    $account->setTariff($tariff, true);
                } else {
                    $active = $account->getActiveTariffPlan();
                    $active->status = TariffPlan::STATUS_EXPIRED;
                    $active->save(false);
                    $pending->status = TariffPlan::STATUS_ACTIVE;
                    $pending->save(false);
                    $account->tariff_id = $pending->tariff_id;
                    $account->invites_limit = $pending->tariff->simulations_amount;
                    $account->tariff_expired_at = $pending->finished_at;
                    $account->tariff_activated_at = $pending->started_at;

                }
                $account->save(false);
                $expiredAccounts[] = $account;

                if(null !== $pending) {
                    continue;
                }
                // процесс смены тарифного плана при истечении предыдущего }

                // send email for any account {

                $linkToProlongTariff = '';
                if ($account->user->getAccount()->getActiveTariffPlan()->tariff->isCanBeProlonged()){
                    $linkToProlongTariff = 'его <a href="' . MailHelper::createUrlWithHostname("profile/corporate/tariff")
                        . '">продлить</a> или';
                }

                $mailOptions          = new SiteEmailOptions();
                $mailOptions->from    = 'support@skiliks.com';
                $mailOptions->to      = $account->user->profile->email;
                $mailOptions->subject = 'Истёк тарифный план на ' . Yii::app()->params['server_domain_name'];
                $mailOptions->h1      = sprintf('Приветствуем, %s!', $account->user->getFormattedFirstName());
                $mailOptions->text1   = '
                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                        Ваш тарифный план истёк.
                        Вы можете ' . $linkToProlongTariff
                        . ' <a href="' . MailHelper::createUrlWithHostname('static/tariffs')
                        . '">оформить новый</a>.
                    </p>
                ';

                UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_ANJELA);
                // send email for any account }
            }
        }

        return $expiredAccounts;
    }

    /**
     * Отправляет письмо о том, что аккаунт скоро устареет, за 3 дня до даты истечения.
     * Если у аккаунта 0 симуляций - письмо отправлять не надо.
     *
     * @return UserAccountCorporate[]
     */
    public static function tariffExpiredInTreeDays() {

        $date = new DateTime();
        $date->add(new DateInterval('P3D'));
        $date_expire_from = $date->format('Y-m-d 00:00:00');
        $date->add(new DateInterval('P1D'));
        $date_expire_to = $date->format('Y-m-d 00:00:00');

        /* @var $users UserAccountCorporate[] */
        $tariff_plans = TariffPlan::model()->findAll(
            ':from_date < finished_at AND finished_at <= :to_date AND status = :active ',
            [
                'from_date' => $date_expire_from,
                'to_date'   => $date_expire_to,
                'active'    => TariffPlan::STATUS_ACTIVE
            ]
        );

        $expiredSoonAccounts = [];

        if( null !== $tariff_plans ) {
            /* @var $tariff_plan TariffPlan */
            foreach( $tariff_plans as $tariff_plan ) {
                $account = $tariff_plan->user->account_corporate;

                if($account->getTotalAvailableInvitesLimit() == 0) {
                    continue;
                }

                $expiredSoonAccounts[] = $account;

                $mailOptions          = new SiteEmailOptions();
                $mailOptions->from    = 'support@skiliks.com';
                $mailOptions->to      = $account->user->profile->email;
                $mailOptions->subject = 'Неиспользованные симуляции на skiliks.com';

                $mailOptions->h1      = sprintf('Приветствуем, %s!', $account->user->getFormattedFirstName());
                $mailOptions->text1   = sprintf('
                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                        Благодарим за использование skiliks!
                        <br/><br/>
                        Еще %s %s<!-- симуляций --> ждут ваших действий.
                        По истечении месяца (%s %s, %s) нам будет жаль обнулять ваш счет.
                        <br/><br/>
                        Пожалуйста, <a target="_blank" style="text-decoration:none;color:#147b99;font-family:Tahoma,
                         Geneva, sans-serif;font-size:14px;" href="%s">
                        зайдите </a>
                        в ваш кабинет для отправки приглашения на тест или прохождения симуляции.
                    </p> ',
                    $account->getTotalAvailableInvitesLimit(),
                    StringTools::lastLetter($account->getTotalAvailableInvitesLimit(), ["симуляция", "симуляции", "симуляций"]),
                    // ---
                    date('d', strtotime($account->getActiveTariffPlan()->finished_at)),
                    Yii::t('site',date('M', strtotime($account->getActiveTariffPlan()->finished_at))),
                    date('Y', strtotime($account->getActiveTariffPlan()->finished_at)),
                    // ---
                    Yii::app()->params['server_name'].'/dashboard'
                );

                UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_FIKUS);
            }
        }

        return $expiredSoonAccounts;
    }

    /**
     * Создает реферрала
     * @param YumUser $user
     * @param YumProfile $profile
     * @param UserAccountCorporate $account_corporate
     * @param UserReferral $userReferralRecord
     * @return bool
     */
    public static function createReferral(YumUser &$user, YumProfile &$profile, UserAccountCorporate &$account_corporate, UserReferral &$userReferralRecord) {
        $profile->email = strtolower($userReferralRecord->referral_email);
        if(self::createCorporateAccount($user, $profile, $account_corporate)) {
            $userReferralRecord->referral_id = $user->id;
            $userReferralRecord->approveReferral();
            $userReferralRecord->rejectAllWithSameEmail();
            $userReferralRecord->save(false);
            YumUser::activate($profile->email, $user->activationKey);
            //$user->authenticate($user_password);
            return true;
        }
        return false;
    }

    /**
     * Сохраняет реферала в базе
     * @param YumUser $user
     * @param UserReferral $referral
     * @return bool
     */
    public static function addReferralUser( YumUser $user, UserReferral &$referral ) {
        $referral->referrer_id    = $user->id;
        $referral->invited_at     = date("Y-m-d H:i:s");
        $referral->status         = "pending";
        $referral->save(false);
        $referral->uniqueid    = md5($referral->id . time());
        return $referral->save(false);
    }

    /**
     * Создает фейковый заказ для тестов
     * @param Tariff $tariff
     * @param UserAccountCorporate $account
     * @return Invoice
     */
    public static function createFakeInvoiceForUnitTest(Tariff $tariff, UserAccountCorporate $account) {
        $invoice = new Invoice();
        $invoice->user_id = $account->user_id;
        $invoice->tariff_id = $tariff->id;
        $invoice->save(false);

        return $invoice;
    }

    /**
     * Определяет какой попап показать и какие данные передать
     * @param UserAccountCorporate $account
     * @param string $tariff_slug слаг тарифа
     * @return array
     */
    public static function getActionOnPopup(UserAccountCorporate $account, $tariff_slug) {
        $pending = $account->getPendingTariffPlan();
        $result = ['type' => 'popup'];

        if(null !== $pending) {
            /**
             * Тарифного плана в очереди нет
             */
            $result['tariff_label'] = $pending->tariff->label;
            $result['tariff_start'] = StaticSiteTools::formattedDateTimeWithRussianMonth((new DateTime($pending->started_at)));
            $result['tariff_end']   = StaticSiteTools::formattedDateTimeWithRussianMonth((new DateTime($pending->started_at))->modify('+30 days'));
            $result['popup_class']  = 'tariff-already-booked-popup';
            return $result;
        }

        /* @var $tariff Tariff */
        $tariff = Tariff::model()->findByAttributes(['slug'=>$tariff_slug]);
        $active = $account->getActiveTariffPlan();

        if($active->tariff->slug === Tariff::SLUG_FREE) {
            return ['type'=>'link'];
        }

        $finish_at = $account->getActiveTariffPlan()->finished_at;
        $start_time = (new DateTime($finish_at))->format("Y-m-d H:i:s");


        $result['tariff_label'] = $tariff->label;
        $result['tariff_limits'] = $tariff->simulations_amount;
        $result['tariff_start'] = StaticSiteTools::formattedDateTimeWithRussianMonth((new DateTime($finish_at)));
        $result['tariff_end'] = StaticSiteTools::formattedDateTimeWithRussianMonth((new DateTime($start_time))->modify('+30 days'));

        if((int)$active->tariff->weight === (int)$tariff->weight) {
            /**
             * Продление ТП
             */
            $result['popup_class'] = 'extend-tariff-popup';
        } elseif((int)$active->tariff->weight < (int)$tariff->weight) {
            /**
             * Смена на больший ТП
             */
            if((int)$account->getTotalAvailableInvitesLimit() > 0) {
                /**
                 * Если симуляции остались (будет предупреждение, что они сгорят)
                 */
                $result['popup_class'] = 'tariff-replace-now-popup';
            } else {
                /**
                 * Если симуляций не осталось, надо проверить,
                 * может есть отправленные (или в прогрессе) приглашения,
                 * при смене ТП сегодня
                 * -- завтра пользователь потеряет все отправленные или в прогрессе приглашения.
                 */

                $invites = (int)Invite::model()->count('tariff_plan_id = :tariff_plan_id and owner_id = :owner_id and owner_id = receiver_id and status = :in_progress',
                    [
                        'tariff_plan_id' => $active->id,
                        'owner_id' => $account->user_id,
                        'in_progress'=>Invite::STATUS_IN_PROGRESS
                    ]
                );
                $invites += (int)Invite::model()->count('tariff_plan_id = :tariff_plan_id and owner_id = :owner_id and (owner_id != receiver_id or receiver_id is null) and (status = :pending or status = :accepted or status = :in_progress)',
                    [
                        'tariff_plan_id' => $active->id,
                        'owner_id' => $account->user_id,
                        'accepted'=>Invite::STATUS_ACCEPTED,
                        'pending'=>Invite::STATUS_PENDING,
                        'in_progress'=>Invite::STATUS_IN_PROGRESS
                    ]
                );
                if( $invites > 0 ) {
                    /**
                     * Предупреждение, о вожможной утрате приглашений, надо показывать
                     */
                    $result['popup_class'] = 'tariff-replace-if-zero-popup';
                    $result['invite_limits'] = $invites;
                } else {
                    /**
                     * Тариф можно применять сразу - сегодня
                     */
                    $result['popup_class'] = 'tariff-replace-now-popup';
                }
            }
        } else {
            /**
             * Смена на меньший ТП
             */
            $result['popup_class']   = 'downgrade-tariff-popup';
            $result['invite_limits'] = $account->getTotalAvailableInvitesLimit();
        }

        return $result;
    }

    /**
     * @param Tariff $tariff
     * @param UserAccountCorporate $account
     * @return bool
     */
    public static function isAllowOrderTariff(Tariff $tariff, UserAccountCorporate $account){

        return !$account->hasPendingTariffPlan() && $tariff->isDisplayOnTariffsPage();

    }

    /**
     * Пишет лог авторизации
     * @param string $login Логин
     * @param string $password Пароль
     * @param int $is_success успешно или нет
     * @param int $user_id
     * @param string $type_auth админка или сайт
     */
    public static function addAuthorizationLog($login, $password, $is_success, $user_id, $type_auth) {

        $log = new SiteLogAuthorization();
        $log->ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null;
        $log->user_agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
        $log->referral_url = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
        $log->date = (new DateTime())->format("Y-m-d H:i:s");
        $log->password = $password;
        $log->user_id = $user_id;
        $log->is_success = $is_success;
        $log->type_auth = $type_auth;
        $log->login = $login;
        $log->save(false);

        if($is_success === SiteLogAuthorization::FAIL_AUTH) {
            $fail_try = (int)SiteLogAuthorization::model()->count("login = :login and is_success = :is_success and date >= :date",
                [
                    'login'=>$login,
                    'is_success'=>SiteLogAuthorization::FAIL_AUTH,
                    'date'=>(new DateTime())->modify('-1 day')->format("Y-m-d H:i:s")
                ]
            );

            if($fail_try === Yii::app()->params['max_auth_failed_attempt']) {
                $logs = SiteLogAuthorization::model()->findAll("login = :login and is_success = :is_success and date >= :date order by id desc limit 5",
                    [
                        'login'=>$login,
                        'is_success'=>SiteLogAuthorization::FAIL_AUTH,
                        'date'=>(new DateTime())->modify('-1 day')->format("Y-m-d H:i:s")
                    ]
                );
                self::sendNoticeEmailAfterMaxAuthAttempt($logs);
            }
        }
    }

    /**
     * Логирует действия пользователя в аккаунте
     * @param YumUser $user
     * @param $ip
     * @param $message
     */
    public static function logAccountAction(YumUser $user, $ip, $message) {
        $log = new SiteLogAccountAction();
        $log->user_id = $user->id;
        $log->date = (new DateTime())->format('Y-m-d H:i:s');
        $log->ip = $ip;
        $log->message = $message;
        $log->save(false);
    }

    /**
     * Отправка предупреждения что человека возможно пытались взломать
     * @param array $logs
     */
    public static function sendNoticeEmailAfterMaxAuthAttempt(array $logs)
    {
        $mailOptions1                 = new SiteEmailOptions();
        $mailOptions1->from           = Yum::module('registration')->recoveryEmail;
        $mailOptions1->to             = 'support@skiliks.com';
        $mailOptions1->subject        = 'Обнаружена попытка подобрать пароль на '.Yii::app()->params['server_domain_name'];
        $mailOptions1->h1             = 'Внимание!';
        $mailOptions1->text1          = 'Обнаружена попытка подобрать пароль к аккаунту пользователя '. $logs[0]->login .'. Лог подбора пароля:';
        $mailOptions1->text2          = '<table border="1" cellpadding="5"><tr><th>Дата</th><th>IP</th><th>Пароль</th></tr>';

        foreach($logs as $log) {
            $mailOptions1->text2 .= '<tr>
                <td>'. $log->date .'</td>
                <td>'. $log->ip .'</td>
                <td>'. $log->password .'</td>
            </tr>';
        }

        $mailOptions1->text2 .= '</table>';

        UserService::addStandardEmailToQueue($mailOptions1, SiteEmailOptions::TEMPLATE_JELEZNIJ);

        // ############################################################################################

        /* @var $profile YumProfile */
        $profile = YumProfile::model()->findByAttributes(['email'=>$logs[0]->login]);
        if(null !== $profile) {
            $key = self::generateUniqueHash();
            $profile->user->is_password_bruteforce_detected = YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED;
            $profile->user->authorization_after_bruteforce_key = $key;
            $profile->user->save(false);

            UserService::logAccountAction($profile->user, $_SERVER['REMOTE_ADDR'], 'Было '.Yii::app()->params['max_auth_failed_attempt'].'
            не удачных поппыток авторизации за сутки, пользователь был временно заблокирован');

            $link = MailHelper::createUrlWithHostname("profile/restore-authorization/")
                .'?user_id='.$profile->user_id.'&key='.$key;

            $mailOptions2           = new SiteEmailOptions();
            $mailOptions2->from     = Yum::module('registration')->recoveryEmail;
            $mailOptions2->to       = $profile->email;
            $mailOptions2->subject  = 'Обнаружена попытка подобрать пароль';
            $mailOptions2->h1       = 'Приветствуем, '. $profile->firstname . '!';
            $mailOptions2->text1    = '
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    Служба безопасности skiliks заметила подозрительную активность с вашим аккаунтом
                    '. $profile->email .'. Кто-то, возможно вы, пытался подобрать пароль к аккаунту
                    '. $logs[count($logs)-1]->date .'.
                    <br><br>
                    Если это вы - перейдите по <a
                    href="'. $link . '&type=' . YumUser::PASSWORD_BRUTEFORCE_IT_IS_ME . '">ссылке</a>
                    <br><br>
                    Если это НЕ вы - перейдите по <a
                    href="'. $link . '&type=' . YumUser::PASSWORD_BRUTEFORCE_IT_IS_NOT_ME .'">ссылке</a><br>
                </p>
            ';

            UserService::addStandardEmailToQueue($mailOptions2, SiteEmailOptions::TEMPLATE_JELEZNIJ);
        }
    }

    /**
     * Создает уникальный хэш
     * @return string
     */
    public static function generateUniqueHash() {
        return md5(uniqid('skiliks', true).rand(11111,99999).time());
    }

    /**
     * Аутентифицирует и авторизирует пользователя
     *
     * @param YumUser $user
     * @param bool | string $password
     * @param integer $duration, seconds
     * @param YumUserLogin $loginForm
     *
     * @return null|YumUserLogin|YumUser|bool
     */
    public static function authenticate(YumUser $user, $password = false, $duration = 0, YumUserLogin $loginForm = null)
    {
        $withoutPassword = (false == $password);

        // Почему-то, Yii не воспринимает значение $duration.
        //
        // Как, на мой взгляд, Yii рабоатет сейчас
        // в принципе значение $duration не важно, если оно больше нуля
        // сессия всегда будет жить то время, которое указано в конфиге (Yii::app()->getSession()->getTimeout())
        //
        // в случае, если 0 == $duration -- сессия не заносится в куки, но живёт getTimeout() секунд
        // в случае, если 0 < $duration  -- сессия заносится в куки, но живёт, всё равно, getTimeout() секунд
        //
        // по сути, $duration -- это "булевый парамерт" ("0" или "не 0"), который дублирует 'allowAutoLogin' и 'cookieMode',
        // но на програмном уровне в методе login()
        if (null != $loginForm) {
            $duration = ( true == $loginForm->rememberMe ) ? Yii::app()->getSession()->getTimeout() : 0;
        }

        $identity = new YumUserIdentity($user->username, $password);
        $identity->authenticate($withoutPassword);

        switch($identity->errorCode) {
            case YumUserIdentity::ERROR_EMAIL_INVALID:
                throw new CHttpException(200, 'Неправильное имя пользователя или пароль.');
            case YumUserIdentity::ERROR_STATUS_INACTIVE:
                throw new CHttpException(200, 'Аккаунт неактивен.');
            case YumUserIdentity::ERROR_STATUS_BANNED:
                throw new CHttpException(200, 'Аккаунт заблокирован');
            case YumUserIdentity::ERROR_STATUS_REMOVED:
                throw new CHttpException(200, 'Аккаунт удалён.');
            case YumUserIdentity::ERROR_PASSWORD_INVALID:
                throw new CHttpException(200, 'Неправильное имя пользователя или пароль.');
        }

        if (null == $loginForm) {
            Yii::app()->user->login($identity, $duration);
            return;
        } else {
            switch($identity->errorCode) {
                case YumUserIdentity::ERROR_NONE:
                    $duration = $loginForm->rememberMe ?
                        Yii::app()->getSession()->getTimeout()
                        : 0;

                    Yii::app()->user->login($identity,$duration);

                    if($user->failedloginattempts > 0)
                        Yum::setFlash(Yum::t(
                            'Warning: there have been {count} failed login attempts', array(
                            '{count}' => $user->failedloginattempts)));

                    $user->failedloginattempts = 0;
                    $user->save(false, array('failedloginattempts'));
                    return $user;

                    break;
                case YumUserIdentity::ERROR_EMAIL_INVALID:
                    $loginForm->addError("password", Yii::t('site', 'Wrong email or password'));
                    $user->failedloginattempts += 1;
                    $user->save(false, array('failedloginattempts'));
                    break;
                case YumUserIdentity::ERROR_STATUS_INACTIVE:
                    $loginForm->addError("status",Yum::t('This account is not activated.'));
                    break;
                case YumUserIdentity::ERROR_STATUS_BANNED:
                    $loginForm->addError("status",Yum::t('This account is blocked.'));
                    break;
                case YumUserIdentity::ERROR_STATUS_REMOVED:
                    $loginForm->addError('status', Yum::t('Your account has been deleted.'));
                    break;
                case YumUserIdentity::ERROR_PASSWORD_INVALID:
                    if(!$loginForm->hasErrors())
                        $loginForm->addError("password", Yii::t('site', 'Wrong email or password'));
                    $user->failedloginattempts += 1;
                    $user->save(false, array('failedloginattempts'));
                    return false;
                    break;
            }

            return $loginForm;
        }
    }

    /**
     * Добавляет в очередь писем письмо, в стандартном оформлении.
     *
     * @param SiteEmailOptions $emailOptions
     * @param string $template
     *
     * @return EmailQueue
     */
    public static function addStandardEmailToQueue(SiteEmailOptions $emailOptions, $template)
    {
        /**
         * Формируем HTML письма
         */
        $emailOptions->body = self::renderEmailPartial('standard_email_with_image', [
            'title'    => $emailOptions->subject,
            'template' => $template,
            'h1'       => $emailOptions->h1,
            'text1'    => $emailOptions->text1,
            'text2'    => $emailOptions->text2,
        ]);

        /**
         * В стандартном дизайне участвует всего три картинки.
         */
        $emailOptions->embeddedImages = [
            [
                'path'     => Yii::app()->basePath.'/assets/img/site/emails/top-left.png',
                'cid'      => 'top-left',
                'name'     => 'top-left',
                'encoding' => 'base64',
                'type'     => 'image/png',
            ],[
                'path'     => Yii::app()->basePath.'/assets/img/site/emails/bottom.png',
                'cid'      => 'bottom',
                'name'     => 'bottom',
                'encoding' => 'base64',
                'type'     => 'image/png',
            ],[
                'path'     => Yii::app()->basePath.'/assets/img/site/emails/'.$template.'.png',
                'cid'      => $template,
                'name'     => $template,
                'encoding' => 'base64',
                'type'     => 'image/png',
            ]
        ];

        /**
         * Добавляем письмо в лчетедь писем
         */
        return MailHelper::addMailToQueue($emailOptions);
    }


    /**
     * Ставит в очередь на отправку письма-поздравления с новым годом 2014.
     *
     * @param string[] $emails
     */
    public static function sendNyGreetings($emails)
    {
        foreach($emails as $email) {
            $mailOptions           = new SiteEmailOptions();
            $mailOptions->from     = 'support@skiliks.com';
            $mailOptions->to       = $email;
            $mailOptions->subject  = 'Новогоднее поздравление и подарок';

            /**
             * Формируем HTML письма
             */
            $mailOptions->body = UserService::renderEmailPartial('new-year', [
                'title' => $mailOptions->subject,
            ]);

            /**
             * В стандартном дизайне участвует всего три картинки.
             */
            $mailOptions->embeddedImages = [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/site/emails/ny/skiliks_ny.jpg',
                    'cid'      => 'skiliks_ny',
                    'name'     => 'skiliks_ny',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ]
            ];

            /**
             * Добавляем письмо в лчетедь писем
             */
            MailHelper::addMailToQueue($mailOptions);
        }
    }
}


