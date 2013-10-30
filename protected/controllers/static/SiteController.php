<?php
class SiteController extends SiteBaseController
{
    /**
     * This is defaut Yii action.
     * It never useded in API or frontend static pages.
     * So, we display error message for user if aout script call this action.
     */
    public function actionIndex()
    {
        $this->forward('static/page/index');
    }

    /**
     *
     */
    public function actionSimulation($mode, $type = Scenario::TYPE_LITE, $invite_id = null)
    {
        $start = Yii::app()->request->getParam('start');
        $user = Yii::app()->user->data();
        /* @var $user YumUser  */
        if (!$user->isAuth() && $type != Scenario::TYPE_LITE) {
            $this->redirect('/user/auth');
        }

        if (Simulation::MODE_DEVELOPER_LABEL == $mode
            && false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/dashboard');
        }

        if ($mode !== Simulation::MODE_PROMO_LABEL && $mode !== Simulation::MODE_DEVELOPER_LABEL) {
            $this->redirect('/dashboard');
        }

        if (null === $invite_id && false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/dashboard');
        }

        if (null !== $invite_id) {
            /** @var Invite $invite */
            $invite = Invite::model()->findByAttributes(['id' => $invite_id]);
            if (null === $invite) {
                Yii::app()->user->setFlash('error', 'Выберите приглашение по которому вы хотите начать симуляцию');
                $this->redirect('/dashboard');
            }

            $invite->refresh(); // Important! Prevent caching
            MailHelper::sendEmailIfSuspiciousActivity($invite);
        }

        if (isset($invite) &&
            $invite->scenario->slug == Scenario::TYPE_FULL &&
            false == $invite->canUserSimulationStart()
        ) {
            Yii::app()->user->setFlash('error', 'У вас нет прав для старта этой симуляции');
            $this->redirect('/dashboard');
        }

        if (isset($invite) && $invite->receiver_id !== $user->id) {
            $this->redirect('/dashboard');
        }

        if (isset($invite) && false == $invite->can_be_reloaded) {
            Yii::app()->user->setFlash('error',
                'Прохождение симуляции было прервано. <br/> Свяжитесь с работодателем ' .
                'чтобы он выслал вам новое приглашение или со службой тех.поддержки ' .
                'чтобы восстановить доступ к прохождению симуляции.'
            );
            $this->redirect('/dashboard');
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
            $this->redirect('/dashboard');
        }

        if (isset($invite) && Scenario::TYPE_TUTORIAL == $type
            && $user->isCorporate() && (int)$user->account_corporate->getTotalAvailableInvitesLimit() == 0
        ) {
            Yii::app()->user->setFlash('error', 'У вас закончились приглашения');
            $this->redirect('/profile/corporate/tariff');
        }

        $assetsUrl = $this->getAssetsUrl();
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
            $config['result-url'] = $this->createUrl('static/site/simulation', [
                'mode' => $mode,
                'type' => isset($invite) ? Scenario::model()->findByPk($invite->scenario_id)->slug : Scenario::TYPE_FULL,
                'invite_id' => $invite_id,
            ]);
        }

        $this->layout = false;
        $this->render('site', [
            'config'        => CJSON::encode($config),
            'assetsUrl'     => $assetsUrl,
            'inviteId'      => (null === $invite_id) ? 'null' : $invite_id,
            'httpUserAgent' => str_replace(['(',')'], '', $_SERVER['HTTP_USER_AGENT']),
            'scenarioLabel' => $scenarioConfigLabelText
        ]);
    }

    /**
     * We handle Yii errors and save them to Yii.log.
     * User see just standard notice
     */
    public function actionError()
    {
        $this->returnErrorMessage(Yii::app()->errorHandler->error);
    }

    /**
     *
     */
    public function actionRunSimulationOrChooseAccount()
    {
        $this->render('runSimulationOrChooseAccount');
    }

    /**
     *
     */
    public function actionError404()
    {
        $error = Yii::app()->errorHandler->error;

        if( $error )
        {
            $this->render('error404');
        }
    }

    public function actionWatchVideo() {
        $this->render('watchVideo');
    }

    public function actionIsStarted()
    {
        $invite_id = Yii::app()->request->getParam('invite_id');
        /* @var */
        $invite = Invite::model()->findByPk($invite_id);
        if(InviteService::isSimulationOverrideDetected($invite)){
            InviteService::logAboutInviteStatus($invite, 'Проверка на запущеную симуляцию');
            $result['user_try_start_simulation_twice'] = true;
        }else{
            $result['user_try_start_simulation_twice'] = false;
        }

        $user = Yii::app()->user->data();
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $countInvites = Invite::model()->countByAttributes([
            'owner_id'    => $user->id,
            'receiver_id' => $user->id,
            'status'      => Invite::STATUS_IN_PROGRESS,
            'scenario_id' => $scenario->id,
        ]);

        $result['count_self_to_self_invites_in_progress'] = $countInvites;

        $this->sendJSON($result);
    }

    public function actionBreakSimulationsForSelfToSelfInvites()
    {
        $user = Yii::app()->user->data();
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $invites = Invite::model()->findAllByAttributes([
            'owner_id'    => $user->id,
            'receiver_id' => $user->id,
            'status'      => Invite::STATUS_IN_PROGRESS,
            'scenario_id' => $scenario->id,
        ]);

        foreach ($invites as $invite) {
            if (null !== $invite->simulation) {
                $invite->simulation->status = Simulation::STATUS_INTERRUPTED;
                $invite->simulation->save(false);
                $initValue = $user->getAccount()->getTotalAvailableInvitesLimit();
                $user->getAccount()->invites_limit++;

                UserService::logCorporateInviteMovementAdd(sprintf("Симуляция номер %s прервана ( приглашение номер %s). В аккаунт возвращена одна симуляция.",
                    $invite->simulation->id,$invite->id), $user->getAccount(), $initValue);

            }
            $invite->status = Invite::STATUS_DELETED;
            $invite->save(false);
        }

        $user->getAccount()->save();
    }

    public function actionUserStartSecondSimulation() {

        $invite_id = Yii::app()->request->getParam('invite_id');
        if(null!==$invite_id){
            $invite = Invite::model()->findByPk($invite_id);
            InviteService::logAboutInviteStatus($invite, 'Пользователь запустил вторую симуляцию');
        }
    }

    public function actionUserRejectStartSecondSimulation() {
        $invite_id = Yii::app()->request->getParam('invite_id');
        if(null!==$invite_id){
            $invite = Invite::model()->findByPk($invite_id);
            InviteService::logAboutInviteStatus($invite, 'Пользователь запустил вторую симуляцию');
        }
    }


    public function actionExit(){

        Yii::app()->user->setFlash('error','Текущая симуляция была прервана, так как вы начали новую симуляцию');

        $this->redirect('/dashboard');
    }

    public function actionDemo(){
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $invite = new Invite();
        $invite->scenario = $scenario;
        $invite->scenario_id = $scenario->id;
        $invite->is_display_simulation_results = 1;
        $invite->setExpiredAt();
        $invite->save(false);

        $this->redirect('/simulation/promo/lite/'.$invite->id);
    }
}


