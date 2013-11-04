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
        $this->checkUser();

        if (false === $this->user->isCorporate() ||  false === $this->user->isActive()){
            $this->redirect('userAuth/afterRegistration');
        }

        // creating session for page

        $page = Yii::app()->request->getParam("page", null);

        $request_uri = Yii::app()->request->url;
        $cookie = (Yii::app()->request->cookies['dashboard_page'] !== null) ? Yii::app()->request->cookies['dashboard_page']->value : null;

        if($request_uri == "/dashboard" && $cookie != null && $cookie != $request_uri) {
            $this->redirect($cookie);
        }

        if($page != null) {
            Yii::app()->request->cookies['dashboard_page'] = new CHttpCookie('dashboard_page', $request_uri);
        }

        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $notUsedFullSimulations = UserService::getInviteHimSelf($this->user, $fullScenario);

        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $notUsedLiteSimulations = UserService::getInviteHimSelf($this->user, $liteScenario);
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
            $invite->expired_at = date("Y-m-d H:i:s", time() + 86400*Yii::app()->params['inviteExpired']);

            // show result to user by default have to be false
            $invite->is_display_simulation_results = false;
            $invite->email = strtolower(trim($invite->email));

            $profile = YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)]);

            $validPrevalidate = $invite->validate(['firstname', 'lastname', 'email', 'invitations']);

            if ($profile) {
                $invite->receiver_id = $profile->user->id;
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
        if (null !== Yii::app()->request->getParam('send')) {

            $profile = YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)]);
            $invite->setAttributes($this->getParam('Invite'));
            $is_send = UserService::sendInvite($this->user, $profile, $invite, $this->getParam('Invite')['is_display_simulation_results']);
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

        // check is display pop up about referral`s model {
        $userInvitesCount = Invite::model()->countByAttributes([
            'owner_id'    => $this->user->id,
            'scenario_id' => $fullScenario->id,
        ]);

        // Starting show
        $countOfInvitesToShowPopup = Yii::app()->params['countOfInvitesToShowReferralPopup'];
        if($userInvitesCount == $countOfInvitesToShowPopup) {
            $this->user->getAccount()->is_display_referrals_popup = 1;
            $this->user->getAccount()->save();
        }
        // check is display pop up about referral`s model }

        // Getting popup properties

        $is_display_tariff_expire_pop_up = $this->user->getAccount()->is_display_tariff_expire_pop_up;
        $is_display_user_referral_popup  = $this->user->getAccount()->is_display_referrals_popup;

        $this->render('dashboard_corporate', [
            'invite'              => $invite,
            'inviteToEdit'        => $inviteToEdit,
            'vacancies'           => $vacancies,
            'validPrevalidate'    => $validPrevalidate,
            'simulation'          => $simulation,
            'display_results_for' => $simulationToDisplayResults,
            'notUsedLiteSimulationInvite' => $notUsedLiteSimulations[0],
            'notUsedFullSimulationInvite' => $notUsedFullSimulations[0],
            'show_user_referral_popup' =>  $is_display_user_referral_popup,
            'is_display_tariff_expire_pop_up' => $is_display_tariff_expire_pop_up,
            'user'                => $this->user
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

        $notUsedLiteSimulations = UserService::getInviteHimSelf($this->user, $liteScenario);
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

        // owner only can delete his invite
        if ($user->id !== $invite->owner_id) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить чужое приглашение!"
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if ($invite->isPending()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'В ожидании'."
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if ($invite->isAccepted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Подтверждено'."
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if ($invite->isStarted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Начато'."
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        if ($invite->isCompleted()) {
            Yii::app()->user->setFlash('success', sprintf(
                "Нельзя удалить приглашение которое находится в статусе 'Готово'."
            ));
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        $invite->deleteInvite();

        $user->getAccount()->increaseLimit($invite);

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
            $this->redirect(Yii::app()->request->urlReferrer);
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

        if (null === $declineExplanation->invite) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует.');
            $this->redirect('/dashboard');
        }

        if (Yii::app()->user->data()->id !== $declineExplanation->invite->receiver_id &&
            Yii::app()->user->data()->id !== $declineExplanation->invite->owner_id &&
            strtolower(Yii::app()->user->data()->profile->email) !== strtolower($declineExplanation->invite->email) &&
            null !== $declineExplanation->invite->receiver_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение.');
            $this->redirect('/dashboard');
        }

        $initValue = $declineExplanation->invite->ownerUser->getAccount()->getTotalAvailableInvitesLimit();

        $declineExplanation->invite->ownerUser->getAccount()->invites_limit++;
        $declineExplanation->invite->ownerUser->getAccount()->save(false);

        UserService::logCorporateInviteMovementAdd(sprintf("Пользователь %s отклонил приглашение номер %s. В аккаунт возвращена одна симуляция.",
            $declineExplanation->invite->email, $declineExplanation->invite->id),  $declineExplanation->invite->ownerUser->getAccount(), $initValue);


        $declineExplanation->invite_recipient_id = $declineExplanation->invite->receiver_id;
        $declineExplanation->invite_owner_id = $declineExplanation->invite->owner_id;
        $declineExplanation->vacancy_label = $declineExplanation->invite->getVacancyLabel();
        $declineExplanation->created_at = date('Y-m-d H:i:s');
        $declineExplanation->save();

        $invite_status = $declineExplanation->invite->status;
        $declineExplanation->invite->status = Invite::STATUS_DECLINED;
        $declineExplanation->invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $declineExplanation->invite->update(false, ['status']);
        InviteService::logAboutInviteStatus($declineExplanation->invite, 'Пользователь сменил статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($declineExplanation->invite->status));

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

    public function actionInviteReferrals() {
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if(!$user->isAuth() || !$user->isCorporate()) {
            $this->redirect("dashboard");
        }


        if(!Yii::app()->request->getIsAjaxRequest()) {
            $referralInviteModel = new ReferralsInviteForm();
            $this->render("invite_referrals", ['user'=>$user, 'referralInviteModel' => $referralInviteModel]);
        }
        else {
            $referralForm = new ReferralsInviteForm();

            $referralInviteText   = Yii::app()->request->getParam('ReferralsInviteForm')['text'];

            $referralForm->emails = strtolower(Yii::app()->request->getParam('emails')) ;
            $referralForm->text   = Yii::app()->request->getParam('text');

            $errors = CActiveForm::validate($referralForm);

            if ($errors && $errors != "[]") {
                echo $errors;
            }
            else {

                foreach($referralForm->validatedEmailsArray as $referAddress) {
                    $refer = new UserReferral();
                    $refer->referral_email = strtolower($referAddress);
                    $refer->referrer_id    = $user->id;
                    $refer->invited_at     = date("Y-m-d H:i:s");
                    $refer->status         = "pending";
                    $refer->save();

                    $refer->uniqueid    = md5($refer->id . time());
                    $refer->save();


                    $refer->sendInviteReferralEmail($referralInviteText);
                }

                $message = (count($referralForm->validatedEmailsArray) > 1) ?  "Приглашения для " : "Приглашение для ";
                $emails = implode($referralForm->validatedEmailsArray, ", ");
                $message .= $emails;
                $message .= (count($referralForm->validatedEmailsArray) > 1) ?  " успешно отправлены." : " успешно отправлено.";
                Yii::app()->user->setFlash('success', $message);
            }
        }
    }


    function actionSendReferralEmail() {
        $user = Yii::app()->user->data();
        if (!$user->isAuth()) {
            Yii::app()->user->setFlash('success', $this->renderPartial('_thank_you_form', [], true));
            $this->redirect('/');
        } elseif ($user->isPersonal()) {
            $this->redirect('/dashboard');
        }

        else {

            $this->redirect('/dashboard');
        }
    }

    function actionDontShowPopup() {

        $user = Yii::app()->user->data();

        if (!$user->isAuth()) {
            Yii::app()->end();
        } elseif ($user->isPersonal()) {
            Yii::app()->end();
        }

        $dontShowPopup = Yii::app()->request->getParam("dontShowPopup", null);
        if($dontShowPopup !== null && $dontShowPopup == 1) {
            $user->getAccount()->is_display_referrals_popup = 0;
            $user->getAccount()->save();
        }
    }

    function actionDontShowTariffEndPopup() {

        $user = Yii::app()->user->data();

        if (!$user->isAuth()) {
            Yii::app()->end();
        } elseif ($user->isPersonal()) {
            Yii::app()->end();
        }

        $is_display_tariff_expire_pop_up = Yii::app()->request->getParam("is_display_tariff_expire_pop_up", null);
        if($is_display_tariff_expire_pop_up !== null && $is_display_tariff_expire_pop_up == 1) {
            $user->getAccount()->is_display_tariff_expire_pop_up = 0;
            $user->getAccount()->save();
        }
    }

    public function actionRemakeRenderType() {
        $user = Yii::app()->user->data()->profile;

        if (null !== Yii::app()->request->getParam('remakeRender')) {
            if($user->assessment_results_render_type == "percentil") {
                $user->assessment_results_render_type = "standard";
            } else {
                $user->assessment_results_render_type = "percentil";
            }
            $user->save();
            Yii::app()->end();
        }

    }

}