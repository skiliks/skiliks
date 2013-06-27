<?php
class SiteController extends AjaxController
{
    public $user;
    public $signInErrors;

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
        $user = Yii::app()->user->data();
        /* @var $user YumUser  */
        if (!$user->isAuth()) {
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
                $this->redirect('/simulations');
            }

            $invite->refresh(); // Important! Prevent caching
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

        if (isset($invite) && null === $invite->tutorial_displayed_at && null !== $invite->tutorial) {
            $type = $invite->tutorial->slug;
            $tutorial = true;
            $invite->tutorial_displayed_at = date('Y-m-d H:i:s');
            $invite->save(false);
        }

        /** @var Scenario $scenario */
        $scenario = Scenario::model()->findByAttributes([
            'slug' => $type
        ]);

        if (null === $scenario) {
            $this->redirect('/dashboard');
        }

        if (isset($invite) && $invite->isTrialFull($user) &&
            $user->isCorporate() && $user->account_corporate->invites_limit == 0
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
                'start' => $scenario->start_time,
                'end' => $scenario->end_time,
                'finish' => $scenario->finish_time,
                'badBrowserUrl' => '/old-browser',
                'oldBrowserUrl' => '/old-browser',
                'dummyFilePath' => $assetsUrl . '/img/kotik.jpg',
                'invite_id'     => $invite_id,
            ]
        );

        if (!empty($tutorial)) {
            $config['result-url'] = $this->createUrl('static/site/simulation', [
                'mode' => $mode,
                'type' => isset($invite) ? Scenario::model()->findByPk($invite->scenario_id)->slug : Scenario::TYPE_FULL,
                'invite_id' => $invite_id
            ]);
        }

        $this->layout = false;
        $this->render('site', [
            'config'    => CJSON::encode($config),
            'assetsUrl' => $assetsUrl,
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
}


