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
    public function actionSimulation($mode, $type = Simulation::TYPE_LITE, $invite_id = null)
    {
        header('X-Frame-Options: GOFORIT');

        $user = Yii::app()->user->data();

        if (null === $user) {
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
                Yii:app()->user->setFlash('error', 'Выбирите приглашение по которому Вы хотите начать симуляцию. <br/>Похоже, что вы пытаетесь начать симуляцию не указав приглашение -  это запрещено.');
                $this->redirect('/simulations');
            }

            if (null !== $invite->simulation_id) {
                Yii::app()->user->setFlash('error', sprintf(
                    'Вы уже прошли (начали) симуляцию по приглашению от %s %s.',
                    $invite->ownerUser->getAccount()->ownership_type,
                    $invite->ownerUser->getAccount()->company_name
                ));
                $this->redirect('/simulations');
            }
        }
        // check invite if it setted }

        $assetsUrl = $this->getAssetsUrl();
        $config = array_merge(
            Yii::app()->params['public'],
            Yii::app()->params['simulation'][Simulation::$typeLabel[$type]],
            [
                'assetsUrl' => $assetsUrl,
                'mode' => $mode,
                'type' => $type,
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
}


