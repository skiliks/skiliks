<?php

class DashboardController extends SiteBaseController implements AccountPageControllerInterface
{
    public function getBaseViewPath()
    {
        return '/static/dashboard';
    }

    public function actionIndex()
    {
        $this->accountPagesBase();
    }

    public function actionCorporateNew()
    {
        $this->layout = 'site_standard';
        $this->checkUser();

        if (false === $this->user->isCorporate() ||
            empty($this->user->account_corporate->is_corporate_email_verified)
        ) {
            $this->redirect('userAuth/afterRegistrationCorporate');
        }

        $vacancies = [];
        $vacancyList = Vacancy::model()->byUser($this->user->id)->findAll();
        foreach ($vacancyList as $vacancy) {
            $vacancies[$vacancy->id] = Yii::t('site', $vacancy->label);
        }

        $invite = new Invite();
        $validPrevalidate = false;

        if (null !== Yii::app()->request->getParam('prevalidate')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $invite->owner_id = $this->user->id;
            $validPrevalidate = $invite->validate(['firstname', 'lastname', 'email', 'invitations']);
            $profile = YumProfile::model()->findByAttributes(['email' => $invite->email]);

            if ($profile) {
                $invite->receiver_id = $profile->user->id;
            }

            if (null == $invite->vacancy && empty($vacancies)) {
                $invite->clearErrors('vacancy_id');
                $invite->addError('vacancy_id', Yii::t('site', 'Add vacancy in your profile'));
                $validPrevalidate = false;
            }

            if (0 == $this->user->account_corporate->invites_limit) {
                Yii::app()->user->setFlash('error', sprintf(
                    'У вас закончились приглашения'
                ));
                $validPrevalidate = false;
            }

            $invite->message = sprintf(
                'Вопросы относительно вакансии вы можете задать по адресу %s, куратор вакансии - %s.',
                $this->user->account_corporate->corporate_email,
                $this->user->getFormattedName()
            );

            $invite->signature = sprintf(Yii::t('site', 'Best regards, %s'), $invite->ownerUser->getFormattedName());

        }

        // handle send invitation {
        if (null !== Yii::app()->request->getParam('send')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');

            $invite->code = uniqid(md5(mt_rand()));
            $invite->owner_id = $this->user->id;

            // What happens if user is registered, but not activated??
            $profile = YumProfile::model()->findByAttributes([
                'email' => $invite->email
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

            // send invitation
            if ($invite->validate() && 0 < $this->user->getAccount()->invites_limit) {
                $invite->markAsSendToday();
                $invite->message = preg_replace('/(\r\n)/', '<br>', $invite->message);
                $invite->message = preg_replace('/(\n\r)/', '<br>', $invite->message);
                $invite->message = preg_replace('/\\n|\\r/', '<br>', $invite->message);
                $invite->is_display_simulation_results = Yii::app()->params['isDisplaySimulationResults'];
                $invite->save();

                InviteService::logAboutInviteStatus($invite, 'invite : created (new) : standard');
                $this->sendInviteEmail($invite);

                $initValue = $this->user->getAccount()->invites_limit;

                // decline corporate user invites_limit
                $this->user->getAccount()->invites_limit--;
                $this->user->getAccount()->save();
                $this->user->refresh();

                UserService::logCorporateInviteMovementAdd(
                    'send invitation 1',
                    $this->user->getAccount(),
                    $initValue
                );


                $this->redirect('/dashboard');
            } elseif ($this->user->getAccount()->invites_limit < 1 ) {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Беспплатный тарифный план использован. Пожалуйста, <a class="feedback">свяжитесь с нами</a>>, чтобы приобрести пакет симуляций'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Неизвестная ошибка.<br/>Приглашение не отправлено.'));
            }
        }
        // handle send invitation }

        // handle edit invite invitation {
        $inviteToEdit = new Invite();
        if (null !== Yii::app()->request->getParam('edit-invite')) {
            $inviteData = Yii::app()->request->getParam('Invite');

            $inviteToEdit = Invite::model()->findByPk($inviteData['id']);

            if (null === $invite) {
                Yii::app()->user->setFlash('error', sprintf(
                    "Неправильные данные!"
                ));
            } else {
                $inviteToEdit->firstname = $inviteData['firstname'];
                $inviteToEdit->lastname = $inviteData['lastname'];
                $inviteToEdit->vacancy_id = $inviteData['vacancy_id'];
                // send invitation
                if ($inviteToEdit->validate(['firstname', 'lastname', 'vacancy_id'])) {
                    $inviteToEdit->update(['firstname', 'lastname', 'vacancy_id']);
                    $inviteToEdit->refresh();
                    InviteService::logAboutInviteStatus($inviteToEdit, 'invite : update (new) : standard');
                }
            }
        }
        // handle edit invite invitation }

        $simulation = Simulation::model()->getLastSimulation($this->user, Scenario::TYPE_LITE);

        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        $this->render('//new/dashboard_corporate', [
            'invite'              => $invite,
            'inviteToEdit'        => $inviteToEdit,
            'vacancies'           => $vacancies,
            'validPrevalidate'    => $validPrevalidate,
            'simulation'          => $simulation,
            'display_results_for' => $simulationToDisplayResults,
        ]);
    }

    public function actionCorporate()
    {
        $this->checkUser();

        if (false === $this->user->isCorporate() ||
            empty($this->user->account_corporate->is_corporate_email_verified)
        ) {
            $this->redirect('userAuth/afterRegistrationCorporate');
        }

        // check and add trial full version {
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $tutorialScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_TUTORIAL]);

        $notUsedFullSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $fullScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedFullSimulations)) {
            $i = 0;
            foreach ($notUsedFullSimulations as $key => $notUsedFullSimulation) {

                if (0 < $i) {
                    $notUsedFullSimulation->delete();
                    unset($notUsedFullSimulations[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedFullSimulations)) {
            $newInviteForFullSimulation = new Invite();
            $newInviteForFullSimulation->owner_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->receiver_id = Yii::app()->user->data()->id;
            $newInviteForFullSimulation->firstname = Yii::app()->user->data()->profile->firstname;
            $newInviteForFullSimulation->lastname = Yii::app()->user->data()->profile->lastname;
            $newInviteForFullSimulation->scenario_id = $fullScenario->id;
            $newInviteForFullSimulation->status = Invite::STATUS_ACCEPTED;
            $newInviteForFullSimulation->sent_time = time(); // @fix DB!
            $newInviteForFullSimulation->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
            $newInviteForFullSimulation->tutorial_scenario_id = $tutorialScenario->id;
            $newInviteForFullSimulation->is_display_simulation_results = 1;
            $newInviteForFullSimulation->save(true, [
                'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status', 'tutorial_scenario_id',
                'updated_at', 'is_display_simulation_results',
            ]);

            $newInviteForFullSimulation->email = strtolower(Yii::app()->user->data()->profile->email);
            $newInviteForFullSimulation->save(false);

            $notUsedFullSimulations[] = $newInviteForFullSimulation;

            InviteService::logAboutInviteStatus($newInviteForFullSimulation, 'invite : created : system-demo (full 1)');

        }
        // check and add trial full version }

        // check and add trial lite version {
        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $liteScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedLiteSimulations)) {
            $i = 0;
            foreach ($notUsedLiteSimulations as $key => $notUsedLiteSimulation) {
                if (0 < $i) {
                    $notUsedLiteSimulation->delete();
                    unset($notUsedLiteSimulations[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedLiteSimulations)) {
            $notUsedLiteSimulations[] = Invite::addFakeInvite(Yii::app()->user->data(), $liteScenario);
        }
        // check and add trial lite version }


        $vacancies = [];
        $vacancyList = Vacancy::model()->byUser($this->user->id)->findAll();
        foreach ($vacancyList as $vacancy) {
            $vacancies[$vacancy->id] = Yii::t('site', $vacancy->label);
        }

        $invite = new Invite();
        $validPrevalidate = false;

        if (null !== Yii::app()->request->getParam('prevalidate')) {
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $invite->owner_id = $this->user->id;

            // show result to user by default have to be false
            $invite->is_display_simulation_results = false;

            $validPrevalidate = $invite->validate(['firstname', 'lastname', 'email', 'invitations']);
            $profile = YumProfile::model()->findByAttributes(['email' => $invite->email]);
            if ($profile) {
                $invite->receiver_id = $profile->user->id;
            }

            if (null == $invite->vacancy && empty($vacancies)) {
                $invite->clearErrors('vacancy_id');
                $invite->addError('vacancy_id', Yii::t('site', 'Add vacancy in your profile'));
                $validPrevalidate = false;
            }

            if (0 == $this->user->account_corporate->invites_limit) {
                Yii::app()->user->setFlash('error', sprintf(
                    'У вас закончились приглашения'
                ));
                $validPrevalidate = false;
            }

            $invite->message = sprintf(
                $this->user->account_corporate->default_invitation_mail_text,
                $this->user->account_corporate->corporate_email,
                $this->user->getFormattedName()
            );

            $invite->signature = sprintf(Yii::t('site', 'Best regards, %s'), $invite->ownerUser->getFormattedName());

        }

        // handle send invitation {
        if (null !== Yii::app()->request->getParam('send')) {
            // beacause of unkown reason is_display_simulation_results lost after $invite->attributes
            $is_display_results = Yii::app()->request->getParam('Invite')['is_display_simulation_results'];
            $invite->attributes = Yii::app()->request->getParam('Invite');
            $invite->code = uniqid(md5(mt_rand()));
            $invite->owner_id = $this->user->id;
            $invite->can_be_reloaded = true;

            // What happens if user is registered, but not activated??
            $profile = YumProfile::model()->findByAttributes([
                'email' => $invite->email
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

            // send invitation
            if ($invite->validate() && 0 < $this->user->getAccount()->invites_limit) {
                $invite->markAsSendToday();
                $this->user->account_corporate->default_invitation_mail_text = $invite->message;
                $this->user->account_corporate->save();
                $invite->message = preg_replace('/(\r\n)/', '<br>', $invite->message);
                $invite->message = preg_replace('/(\n\r)/', '<br>', $invite->message);
                $invite->message = preg_replace('/\\n|\\r/', '<br>', $invite->message);
                $invite->is_display_simulation_results = (int) !$is_display_results;
                $invite->save();
                InviteService::logAboutInviteStatus($invite, 'invite : create : standard');
                $this->sendInviteEmail($invite);

                $initValue = $this->user->getAccount()->invites_limit;

                // decline corporate user invites_limit
                $this->user->getAccount()->invites_limit--;
                $this->user->getAccount()->save();
                $this->user->refresh();

                UserService::logCorporateInviteMovementAdd(
                    'send invitation 2',
                    $this->user->getAccount(),
                    $initValue
                );

                $this->redirect('/dashboard');
            } elseif ($this->user->getAccount()->invites_limit < 1 ) {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Беспплатный тарифный план использован. Пожалуйста, <a class="feedback">свяжитесь с нами</a>>, чтобы приобрести пакет симуляций'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('site', 'Неизвестная ошибка.<br/>Приглашение не отправлено.'));
            }
        }
        // handle send invitation }

        // handle edit invite invitation {
        $inviteToEdit = new Invite();
        if (null !== Yii::app()->request->getParam('edit-invite')) {
            $inviteData = Yii::app()->request->getParam('Invite');

            $inviteToEdit = Invite::model()->findByPk($inviteData['id']);

            if (null === $invite) {
                Yii::app()->user->setFlash('error', sprintf(
                    "Неправильные данные!"
                ));
            } else {
                $inviteToEdit->firstname = $inviteData['firstname'];
                $inviteToEdit->lastname = $inviteData['lastname'];
                $inviteToEdit->vacancy_id = $inviteData['vacancy_id'];
                // send invitation
                if ($inviteToEdit->validate(['firstname', 'lastname', 'vacancy_id'])) {
                    $inviteToEdit->update(['firstname', 'lastname', 'vacancy_id']);
                    $inviteToEdit->refresh();
                    InviteService::logAboutInviteStatus($inviteToEdit, 'invite : update : standard');
                }
            }
        }
        // handle edit invite invitation }

        $simulation = Simulation::model()->getLastSimulation($this->user, Scenario::TYPE_LITE);

        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        $this->render('dashboard_corporate', [
            'invite'              => $invite,
            'inviteToEdit'        => $inviteToEdit,
            'vacancies'           => $vacancies,
            'validPrevalidate'    => $validPrevalidate,
            'simulation'          => $simulation,
            'display_results_for' => $simulationToDisplayResults,
            'notUsedLiteSimulationInvite' => $notUsedLiteSimulations[0],
            'notUsedFullSimulationInvite' => $notUsedFullSimulations[0],
        ]);
    }

    /**
     *
     */
    public function actionPersonal()
    {

        // check and add trial lite version {
        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulations = Invite::model()->findAllByAttributes([
            'receiver_id' => Yii::app()->user->data()->id,
            'scenario_id' => $liteScenario->id,
            'email'       => Yii::app()->user->data()->profile->email,
            'status'      => Invite::STATUS_ACCEPTED
        ]);

        // I remove more than 1 allowed to start lite sim {
        if (1 < count($notUsedLiteSimulations)) {
            $i = 0;
            foreach ($notUsedLiteSimulations as $key => $notUsedFullSimulation) {
                if (0 < $i) {
                    $notUsedFullSimulation->delete();
                    unset($notUsedLiteSimulations[$key]);
                }
                $i++;
            }
        }
        // I remove more than 1 allowed to start lite sim }

        if (0 === count($notUsedLiteSimulations)) {
            $notUsedLiteSimulations[] = Invite::addFakeInvite(Yii::app()->user->data(), $liteScenario);
        }

        // check and add trial lite version }

        $this->checkUser();

        $simulation = Simulation::model()->getLastSimulation(Yii::app()->user->data(), Scenario::TYPE_FULL);

        if (null === $simulation) {
            $simulation = Simulation::model()->getLastSimulation(Yii::app()->user->data(), Scenario::TYPE_LITE);
        }

        $simulationToDisplayResults = null;
        if (isset(Yii::app()->request->cookies['display_result_for_simulation_id'])) {
            $simulationToDisplayResults = Simulation::model()->findByPk(
                Yii::app()->request->cookies['display_result_for_simulation_id']->value
            );
            unset(Yii::app()->request->cookies['display_result_for_simulation_id']);
        }

        $this->render('dashboard_personal', [
            'simulation' => $simulation,
            'display_results_for' => $simulationToDisplayResults,
            'notUsedLiteSimulationInvite' => $notUsedLiteSimulations[0],
        ]);
    }

    /**
     * @param Invite $invite
     * @return bool
     * @throws CException
     */
    private function sendInviteEmail($invite)
    {
        $this->checkUser();
        if (empty($invite->email)) {
            throw new CException(Yum::t('Email is not set when trying to send invite email. Wrong invite object.'));
        }

        $inviteEmailTemplate = Yii::app()->params['emails']['inviteEmailTemplate'];

        $body = $this->renderPartial($inviteEmailTemplate, [
            'invite' => $invite
        ], true);

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

<<<<<<< HEAD
        $invite->is_display_simulation_results = Yii::app()->params['isDisplaySimulationResults'];
=======
        //$invite->is_display_simulation_results = Yii::app()->params['isDisplaySimulationResults'];

>>>>>>> b0265f71264b4418852ea33426128dc18ba93ffc
        $invite->markAsSendToday();
        $invite->save();

        try {
            $sent = YumMailer::send($mail);
        } catch (phpmailerException $e) {
            // happens at my local PC only, Slavka
            $sent = null;
        }

        return $sent;
    }

    /**
     * @param integer $inviteId
     */
    public function actionRemoveInvite($inviteId)
    {
        $this->checkUser();
        $invite = Invite::model()->findByPk($inviteId);
        /* @var $invite Invite */
        $user = Yii::app()->user;
        if (null === $user) {
            Yii::app()->user->setFlash('success', sprintf(
                "Авторизируйтесь"
            ));
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // owner only can delete his invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить чужое приглашение!"
            ));
            $this->redirect('/');
        }

        if ($invite->isPending()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'В ожидании'."
            ));
            $this->redirect('/dashboard');
        }

        if ($invite->isAccepted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Подтверждено'."
            ));
            $this->redirect('/dashboard');
        }

        if ($invite->isStarted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Начато'."
            ));
            $this->redirect('/dashboard');
        }

        if ($invite->isCompleted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Готово'."
            ));
            $this->redirect('/dashboard');
        }

        $invite->deleteInvite();

        $user->getAccount()->increaseLimit($invite);

        $this->redirect('/dashboard');
    }

    /**
     * @param integer $inviteId
     */
    public function actionReSendInvite($inviteId)
    {
        $this->checkUser();
        $invite = Invite::model()->findByPk($inviteId);

        $user = Yii::app()->user;
        if (null === $user) {
            Yii::app()->user->setFlash('success', sprintf(
                "Авторизируйтесь"
            ));
            $this->redirect('/');
        }

        if (null === $invite) {
            //Yii::app()->user->setFlash('success', sprintf(
            //    "Такого приглашения не существует"
            //));
            $this->redirect('/dashboard');
        }

        if (Invite::STATUS_PENDING !== (int)$invite->status) {
            Yii::app()->user->setFlash('success', sprintf(
                nl2br("Только приглашение \n со статусом \"%s\" можно отправить ещё раз."),
                Yii::t('site', Invite::$statusText[Invite::STATUS_PENDING])
            ));
            $this->redirect('/dashboard');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // you can`t delete other (corporate) user invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя продлить чужое приглашение!"
            ));
            $this->redirect('/');
        }

        if (false === $user->isCorporate()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Только корпоративный пользователь пожет продлить приглашение!"
            ));
            $this->redirect('/');
        }

        $this->sendInviteEmail($invite);

        $this->redirect('/dashboard');
    }

    /**
     * @param $code
     */
    public function actionAcceptInvite($id)
    {
        $this->checkUser();
        $invite = Invite::model()->findByPk($id);
        /* @var $invite Invite */
        if (null == $invite) {
            $this->redirect('/dashboard');
        }

        if((int)$invite->status === Invite::STATUS_EXPIRED){
            Yii::app()->user->setFlash('error', 'У симуляции истек срок давности');
            $this->redirect('/');
        }

        if((int)$invite->status !== Invite::STATUS_PENDING){

            $this->redirect('/dashboard');
        }

        $this->checkUser();

        if (strtolower(Yii::app()->user->data()->profile->email) !== strtolower($invite->email)) {
            Yii::app()->user->setFlash('error', 'Вы не можете начать чужую симуляцию.');
            $this->redirect('/profile');
        }

        // for invites to unregistered (when invitation had been send) users, receiver_id is NULL
        // fix (NULL) receiver_id to make sure that simulation can start
        $invite->receiver_id = Yii::app()->user->data()->id;
        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $invite->update(false, ['status', 'receiver_id']);
        InviteService::logAboutInviteStatus($invite, 'invite : updated : accepted');

        /* @flash
        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s принято.',
            $invite->getCompanyOwnershipType(),
            ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
        ));
         */

        $this->redirect('/dashboard'); // promo/full
    }

    /**
     *
     * @param string $code
     */
    public function actionSoftRemoveInvite($id)
    {
        $this->checkUser();

        $invite = Invite::model()->findByPk($id);

        if (null === $invite) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует.');
            $this->redirect('/dashboard');
        }

        if (Yii::app()->user->data()->id !== $invite->receiver_id &&
            Yii::app()->user->data()->id !== $invite->owner_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение.');
            $this->redirect('/dashboard');
        }

        $invite->status = Invite::STATUS_DECLINED;
        $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $invite->update(false, ['status']);

        InviteService::logAboutInviteStatus($invite, 'invite : updated : remove');

        /* @flash
        Yii::app()->user->setFlash('success', sprintf(
            'Приглашение от %s %s отклонено.',
            $invite->getCompanyOwnershipType(),
            ($invite->getCompanyName() === null)?"компании":$invite->getCompanyName()
        ));
         */

        $this->redirect('/dashboard');
    }

    /**
     *
     * @param string $code
     */
    public function actionDeclineInvite($id)
    {
        $declineExplanation = new DeclineExplanation();
        $declineExplanation->attributes = Yii::app()->request->getParam('DeclineExplanation');

        if (null === $declineExplanation->invite) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует.');
            $this->redirect('/dashboard');
        }

        if (Yii::app()->user->data()->id !== $declineExplanation->invite->receiver_id &&
            Yii::app()->user->data()->id !== $declineExplanation->invite->owner_id &&
            Yii::app()->user->data()->profile->email !== $declineExplanation->invite->email &&
            null !== $declineExplanation->invite->receiver_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение.');
            $this->redirect('/dashboard');
        }

        $initValue = $declineExplanation->invite->ownerUser->getAccount()->invites_limit;

        $declineExplanation->invite->ownerUser->getAccount()->invites_limit++;
        $declineExplanation->invite->ownerUser->getAccount()->save(false);

        UserService::logCorporateInviteMovementAdd(
            'actionDeclineInvite',
            $declineExplanation->invite->ownerUser->getAccount(),
            $initValue
        );

        $declineExplanation->invite_recipient_id = $declineExplanation->invite->receiver_id;
        $declineExplanation->invite_owner_id = $declineExplanation->invite->owner_id;
        $declineExplanation->vacancy_label = $declineExplanation->invite->getVacancyLabel();
        $declineExplanation->created_at = date('Y-m-d H:i:s');
        $declineExplanation->save();

        $declineExplanation->invite->status = Invite::STATUS_DECLINED;
        $declineExplanation->invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $declineExplanation->invite->update(false, ['status']);
        InviteService::logAboutInviteStatus($declineExplanation->invite, 'invite : updated : declined');

        $user = Yii::app()->user->data();

        if (!$user->isAuth()) {
            Yii::app()->user->setFlash('success', $this->renderPartial('_thank_you_form', [], true));
            $this->redirect('/');
        } elseif ($user->isPersonal()) {
            $this->redirect('/dashboard');
        }
    }

    /**
     *
     */
    public function actionValidateDeclineExplanation()
    {
        $declineExplanation = new DeclineExplanation();

        $declineExplanation->attributes = Yii::app()->request->getParam('DeclineExplanation');
        $isValid = false;

        $reasonOther = DeclineReason::model()->findByAttributes(['alias' => 'other']);

        // no object - no validation -> this is request to render form at first
        if (null !== Yii::app()->request->getParam('DeclineExplanation')) {
            // fill 'description' from 'reason->label' {
            if (null !== $reasonOther && !empty($declineExplanation->reason_id)) {
                if ($declineExplanation->reason_id != $reasonOther->id) {
                    $declineExplanation->description = $reasonOther->label;
                }
            }
            // fill 'description' from 'reason->label' }

            $isValid = $declineExplanation->validate(['reason_id', 'description']);
        }

        $this->layout = false;

        $html = $this->render(
            'decline_explanation_form',
            [
                'declineExplanation' => $declineExplanation,
                'user' => Yii::app()->user->data(),
                'reasons'            => StaticSiteTools::formatValuesArrayLite(
                    'DeclineReason',
                    'id',
                    'label',
                    Yii::app()->user->isGuest ? '': 'registration_only!=1',
                    false,
                    ' ORDER BY sort_order DESC'
                ),
                'action' => '/dashboard/decline-invite/'.(int)$declineExplanation->invite_id,
                'reasonOtherId' => (null === $reasonOther) ? '' : $reasonOther->id,
            ],
            true
        );

        $this->sendJSON([
            'isValid' => $isValid,
            'html'    => $html,
         ]);
    }

    public function actionSimulationDetails($id)
    {
        $simulation = Simulation::model()->findByPk($id);
        /* @var $user YumUser */
        $user = Yii::app()->user->data();
        if( false === $user->isAdmin() && null !== $simulation->invite){
            if ($user->id !== $simulation->invite->owner_id &&
                $user->id !== $simulation->invite->receiver_id) {
                //echo 'Вы не можете просматривать результаты чужих симуляций.';

                Yii::app()->end(); // кошерное die;
            }
        }

        if (false === $simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
            Yii::app()->end(); // кошерное die;
        }

        $this->layout = false;

        $details = $simulation->getAssessmentDetails();

        // update sim results popup info:
        $simulation->results_popup_partials_path = '//static/dashboard/partials/';
        $simulation->save(false);

        $baseView = str_replace('partials/', 'simulation_details', $simulation->results_popup_partials_path);

        $this->render($baseView, [
            'simulation'     => $simulation,
            'details'        => $details,
            'user'           => $user
        ]);
    }

}