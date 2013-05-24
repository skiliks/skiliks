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
        if(!$user->isAuth()){
            $this->redirect('/user/auth');
        }

        /** @var Scenario $scenario */
        $scenario = Scenario::model()->findByAttributes([
            'slug' => $type
        ]);

        if (null === $user || null === $scenario) {

            $this->redirect('/');
        }

        if (Simulation::MODE_DEVELOPER_LABEL == $mode
            && false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            // redirect such cheater with no message!
            $this->redirect('/');
        }

        if (false == in_array($mode, [Simulation::MODE_PROMO_LABEL, Simulation::MODE_DEVELOPER_LABEL])) {
            // wrong mode name mode
            $this->redirect('/');
        }

        // check invite if it setted {
        if (null !== $invite_id) {
            $invite = Invite::model()->findByPk($invite_id);

            if (null == $invite) {
                Yii::app()->user->setFlash(
                    'error',
                    'Выберите приглашение по которому Вы хотите начать симуляцию'
                );

                $this->redirect('/simulations');
            }

            if ($invite->scenario->slug !== Scenario::TYPE_LITE) {
                if (null !== $invite->simulation_id && ($invite->isStarted() || $invite->isCompleted())) {
                    Yii::app()->user->setFlash('error', sprintf(
                        'Вы уже прошли (начали) симуляцию по приглашению от %s %s.',
                        $invite->getCompanyOwnershipType(),
                        $invite->getCompanyName()
                    ));
                    $this->redirect('/simulations');
                }
            }

            if ($invite->scenario->slug == Scenario::TYPE_FULL
                && false == $invite->canUserSimulationStart()
            ) {
                Yii::app()->user->setFlash('error', sprintf(
                    'У вас нет прав для старта этой симуляции'
                ));
                $this->redirect("/dashboard");//throw new Exception('У вас нет прав для старта этой симуляции');
                return;
            }

            if ($invite->isTrialFull(Yii::app()->user->data())
                && Yii::app()->user->data()->isCorporate()
                && Yii::app()->user->data()->getAccount()->invites_limit == 0) {
                Yii::app()->user->setFlash('error', sprintf(
                    'У вас закончились приглашения'
                ));
                $this->redirect("/profile/corporate/tariff");
            }
        } else {
            if (false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
                $this->redirect("/dashboard");
            }
        }
        // check invite if it setted }

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
                'badBrowserUrl' => '/bad-browser',
                'oldBrowserUrl' => '/old-browser',
                'dummyFilePath' => $assetsUrl . '/img/kotik.jpg',
                'invite_id'     => $invite_id,
            ]
        );

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


