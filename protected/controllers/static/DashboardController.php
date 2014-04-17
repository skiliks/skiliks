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

    public function actionCorporate()
    {
        $isDisplayStandardInvitationMailTopText = true;
        $this->checkUser();

        if (false === $this->user->isCorporate() ||  false === $this->user->isActive()){
            $this->redirect('userAuth/afterRegistration');
        }

        // creating session for page

        $page = Yii::app()->request->getParam("page", null);

        $request_uri = Yii::app()->request->url;
        $cookie = (Yii::app()->request->cookies['dashboard_page'] !== null) ? Yii::app()->request->cookies['dashboard_page']->value : null;

        if($request_uri == "/dashboard" && $cookie != null && $cookie != $request_uri) {
            if(Yii::app()->user->hasFlash('error')){
                Yii::app()->user->setFlash('error', Yii::app()->user->getFlash('error'));
            }
            $this->redirect($cookie);
        }

        if($page != null) {
            Yii::app()->request->cookies['dashboard_page'] = new CHttpCookie('dashboard_page', $request_uri);
        }

        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $notUsedFullSimulations = UserService::getSelfToSelfInvite($this->user, $fullScenario);

        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulations = UserService::getSelfToSelfInvite($this->user, $liteScenario);
            // check and add trial lite version }

        $vacancies = [];
        $vacancyList = Vacancy::model()->findAllByAttributes(['user_id' => $this->user->id]);
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
            $invite->email = strtolower(trim($invite->email));

            $profile = YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)]);

            $validPrevalidate = $invite->validate(['firstname', 'lastname', 'email', 'invitations']);

            if ($profile) {
                $isDisplayStandardInvitationMailTopText = false;
                $invite->receiver_id = $profile->user->id;
            }else{
                $isDisplayStandardInvitationMailTopText = true;
            }

            if (null == $invite->vacancy && empty($vacancies)) {
                $invite->clearErrors('vacancy_id');
                $invite->addError('vacancy_id', Yii::t('site', 'Add vacancy in your profile'));
                $validPrevalidate = false;
            }

            if (0 == $this->user->account_corporate->getTotalAvailableInvitesLimit()) {
                Yii::app()->user->setFlash('error', sprintf(
                    'У вас закончились приглашения'
                ));
                $validPrevalidate = false;
            }


            if($profile !== null && $profile->user->isCorporate()) {
                Yii::app()->user->setFlash('error', sprintf(
                    'Данный пользователь с e-mail: '.$invite->email.' является корпоративным. Вы можете отправлять
                     приглашения только персональным и незарегистрированным пользователям'
                ));
                $validPrevalidate = false;
            }

            $invite->message = sprintf(
                $this->user->account_corporate->default_invitation_mail_text,
                $this->user->profile->email,
                $this->user->getFormattedName()
            );

            $invite->signature = sprintf(Yii::t('site', 'Best regards, %s'), $invite->ownerUser->getFormattedName());

        }

        // handle send invitation {
        //Отправка Инвайта
        if (null !== Yii::app()->request->getParam('send')) {

            $profile = YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)]);
            if($profile === null){
                $isDisplayStandardInvitationMailTopText = true;
            }
            $invite->setAttributes($this->getParam('Invite'));
            $is_send = false;
            try {
                $is_send = UserService::sendInvite($this->user, $invite, $this->getParam('Invite')['is_display_simulation_results']);
                if ($is_send) {
                    $invite->refresh();
                    UserService::sendEmailInvite($invite);
                }
            } catch (RedirectException $e) {
                Yii::app()->user->setFlash('error', Yii::t('site', 'У вас закончились приглашения'));
                $this->redirect("static/tariffs");
            }
            if(true === $is_send){
                $this->redirect('/dashboard');
            }elseif(false === $is_send) {
                $validPrevalidate = false;
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
                    InviteService::logAboutInviteStatus($inviteToEdit, 'Обновлене данных инвайта в рабочем кабинете');
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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/dashboard-1280.css');
        $this->addSiteCss('pages/dashboard-1024.css');
        $this->addSiteCss('partials/simulation-details-1280.css');
        $this->addSiteCss('partials/simulation-details-1024.css');

        $this->addSiteJs('_page-dashboard.js');
        $this->addSiteJs('_start_demo.js');
        $this->addSiteJs('libs/d3.v3.js');
        $this->addSiteJs('libs/charts.js');

        $this->render('dashboard_corporate', [
            'invite'              => $invite,
            'inviteToEdit'        => $inviteToEdit,
            'vacancies'           => $vacancies,
            'validPrevalidate'    => $validPrevalidate,
            'simulation'          => $simulation,
            'display_results_for' => $simulationToDisplayResults,
            'notUsedLiteSimulationInvite' => $notUsedLiteSimulations[0],
            'notUsedFullSimulationInvite' => $notUsedFullSimulations[0],
            'user'                => $this->user,
            'isDisplayStandardInvitationMailTopText'=>$isDisplayStandardInvitationMailTopText
        ]);
    }

    /**
     *
     */
    public function actionPersonal()
    {
        $this->checkUser();
        // check and add trial lite version {
        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulations = UserService::getSelfToSelfInvite($this->user, $liteScenario);
        // check and add trial lite version }

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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/dashboard-1280.css');
        $this->addSiteCss('pages/dashboard-1024.css');
        $this->addSiteCss('partials/simulation-details-1280.css');
        $this->addSiteCss('partials/simulation-details-1024.css');

        $this->addSiteJs('_page-dashboard.js');
        $this->addSiteJs('_start_demo.js');
        $this->addSiteJs('_decline-invite.js');

        $this->addSiteJs('libs/d3.v3.js');
        $this->addSiteJs('libs/charts.js');

        $this->render('dashboard_personal', [
            'simulation' => $simulation,
            'display_results_for' => $simulationToDisplayResults,
            'notUsedLiteSimulationInvite' => $notUsedLiteSimulations[0],
            'user' => $this->user
        ]);
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
        /* @var YumUser $user */

        // owner only can delete his invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить чужое приглашение"
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if ($invite->isStarted() && $invite->isUserInGame()) {
            Yii::app()->user->setFlash('success', sprintf(
                "В данный момент получатель приглашения проходит симуляцию"
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if ($invite->isCompleted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение, которое находится в статусе \"Готово\""
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if($invite->status == Invite::STATUS_PENDING
            || $invite->status == Invite::STATUS_ACCEPTED
            || $invite->isStarted() && false === $invite->isUserInGame()) {

                $status = $invite->status;
                $initValue = $user->account_corporate->getTotalAvailableInvitesLimit();

                $user->account_corporate->increaseLimit($invite);

                $invite->refresh();

                // надо прервать начатую симуляцию, от неё 2 часа нет вестей
                if ($invite->isStarted() && null !== $invite->simulation) {
                    $invite->simulation->refresh();
                    $invite->simulation->status = Simulation::STATUS_INTERRUPTED;
                    $invite->simulation->save(false);
                }

                UserService::logCorporateInviteMovementAdd(
                    'Ивайт удален пользователем в статусе "'.Invite::getStatusNameByCode($status).'"',
                    $this->user->getAccount(),
                    $initValue
                );
        }

        $invite->deleteInvite();
        $this->redirect(Yii::app()->request->urlReferrer);
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
              $this->redirect('/dashboard');
        }

        if (Invite::STATUS_PENDING !== (int)$invite->status) {
            Yii::app()->user->setFlash('success', sprintf(
                nl2br("Только приглашение \n со статусом \"%s\" можно отправить ещё раз"),
                Yii::t('site', Invite::$statusText[Invite::STATUS_PENDING])
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        $user = $user->data();  //YumWebUser -> YumUser

        // you can`t delete other (corporate) user invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя продлить чужое приглашение"
            ));
            $this->redirect('/');
        }

        if (false === $user->isCorporate()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Только корпоративный пользователь пожет продлить приглашение!"
            ));
            $this->redirect('/');
        }

        UserService::sendEmailInvite($invite);

        $this->redirect(Yii::app()->request->urlReferrer);
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
        $invite_status = $invite->status;
        $invite->status = Invite::STATUS_ACCEPTED;
        $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $invite->update(false, ['status', 'receiver_id']);
        InviteService::logAboutInviteStatus($invite, 'Пользователь сменил статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($invite->status));

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
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует');
            $this->redirect('/dashboard');
        }

        if (Yii::app()->user->data()->id !== $invite->receiver_id &&
            Yii::app()->user->data()->id !== $invite->owner_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение');
            $this->redirect('/dashboard');
        }

        $invite->status = Invite::STATUS_DECLINED;
        $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $invite->update(false, ['status']);

        InviteService::logAboutInviteStatus($invite, 'Клиент удалил инвайт');

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

        $result = InviteService::declineInvite(Yii::app()->user->data(), $declineExplanation);

        if( null !== $result ) {
            $this->redirect( $result );
        } else {
            throw new Exception( "Must be not null" );
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
            'errors'  => json_encode($declineExplanation->getErrors()),
         ]);
    }

    public function actionSimulationDetails($id)
    {
        /* @var $simulation Simulation */

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

        $baseView = $simulation->results_popup_partials_path.'/simulation_details';

        $this->render($baseView, [
            'simulation'     => $simulation,
            'details'        => $details,
            'user'           => $user
        ]);
    }

    public function actionSwitchAssessmentResultsRenderType() {
        $profile = Yii::app()->user->data()->profile;

        $profile->switchAssessmentResultsRenderType();
        $profile->save(false);

        Yii::app()->end();
    }
}