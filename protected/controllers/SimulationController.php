<?php

class SimulationController extends SimulationBaseController
{
    /**
     * Старт симуляции
     *
     * @return string
     */
    public function actionStart()
    {
        sleep(8);
        // Режим симуляции: promo, dev
        $mode = Yii::app()->request->getParam('mode');
        $type = Yii::app()->request->getParam('type');
        $screen_resolution = Yii::app()->request->getParam('screen_resolution');
        $window_resolution = Yii::app()->request->getParam('window_resolution');
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        // check invite if it setted {
        $invite_id = Yii::app()->request->getParam('invite_id');
        $invite = Invite::model()->findByPk($invite_id);

        $scenarioName = null;

        // clean up info bout tutorial to start tutorial
        if (null !== $invite && Scenario::TYPE_FULL === $type) {
            $invite->tutorial_finished_at = null;
            $invite->save(false);
        }

        if (null == $invite) {
            if (false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE) &&
                $type !== Scenario::TYPE_LITE
            ) {
                throw new LogicException('You must have invite.');
            }

            $invite = new Invite();
            $invite->scenario = new Scenario();
            $invite->receiverUser = Yii::app()->user->data();
            $invite->scenario->slug = Yii::app()->request->getParam('type');

            $scenarioName = $invite->scenario->slug;
        } else {
            $scenarioName = $invite->scenario->slug;
        }
        // check invite if it setted }

        $simulation = SimulationService::simulationStart($invite, $mode, $type);
        $simulation->screen_resolution = $screen_resolution;
        $simulation->window_resolution = $window_resolution;
        $simulation->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $simulation->ipv4 = $_SERVER["REMOTE_ADDR"];
        $simulation->update();
        /* @var $log LogServerRequest */
        $log = LogServerRequest::model()->findByPk($this->request_id);
        $log->sim_id = $simulation->id;
        $log->backend_game_time = $simulation->getGameTime();
        $log->update(['sim_id', 'backend_game_time']);

        if (null === $simulation) {
            $this->sendJSON(
                array(
                    'result' => 0,
                )
            );
        }

//        $scenarioConfigLabelText = $invite->scenario->scenario_config->scenario_label_text;

        $scenario = Scenario::model()->findByAttributes(['slug'=>$type]);
        $scenarioConfigLabelText = $scenario->scenario_config->scenario_label_text;

        $this->sendJSON(
            array(
                'result'        => 1,
                'speedFactor'   => $simulation->getSpeedFactor(),
                'simId'         => $simulation->id,
                'inviteId'      => $simulation->invite->id,
                'scenarioName'  => $scenarioName,
                'scenarioLabel' => $scenarioConfigLabelText
            )
        );
    }

    /**
     * Остановка симуляции
     */
    public function actionStop()
    {
        SimulationService::simulationStop(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('logs', array())
        );
        $this->sendJSON([
            'result' => self::STATUS_SUCCESS
        ]);

    }

    /**
     * Установка симуляции на паузу
     */
    public function actionStartPause()
    {
        SimulationService::pause(
            $this->getSimulationEntity()
        );
        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    /**
     * Возобновление симуляции
     */
    public function actionStopPause()
    {
        SimulationService::resume(
            $this->getSimulationEntity()
        );
        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    /**
     * Возобновление симуляции
     */
    public function actionUpdatePause()
    {
        $skipped = Yii::app()->request->getParam("skipped");

        // log to site simulation actions
        SimulationService::logAboutSim($this->getSimulationEntity(), sprintf('Pause prolonged on %s min', $skipped));

        if(null === $skipped) {
            throw new Exception("skipped not found");
        }
        SimulationService::update(
            $this->getSimulationEntity(),
            $skipped
        );
        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    /**
     * Изменение времени симуляции
     *
     * @return string
     */
    public function actionChangeTime()
    {
        $user = $this->getSimulationEntity()->user;

        // protect against real user-cheater
        if (false == $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            return [
                'result' => 0
            ];
        }

        try {
            $newHours = (int)Yii::app()->request->getParam('hour', 0);
            $newMinutes = (int)Yii::app()->request->getParam('min', 0);
            $simulation = $this->getSimulationEntity();
            SimulationService::setSimulationClockTime(
                $simulation,
                $newHours,
                $newMinutes
            );

            $simulation->deleteOldTriggers($newHours, $newMinutes);

            $this->sendJSON(array('result' => 1));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

    /**
     *
     */
    public function actionMarkInviteStarted()
    {
        $simulation = $this->getSimulationEntity();
        $invite = Invite::model()->findByAttributes(['simulation_id' => $simulation->id]);

        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        // IF - to prevent cheating
        if (null !== $invite /*&& $invite->isAccepted()*/ && false === $invite->scenario->isLite()) {
            $invite_status = Invite::STATUS_IN_PROGRESS;
            $invite->status = Invite::STATUS_IN_PROGRESS;
            $invite->save(false);
            InviteService::logAboutInviteStatus($invite, "Инвайт меняет статус с ".Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($invite->status));
        }
        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    public function actionMarkTutorialNotStarted()
    {
        $invite_id = Yii::app()->request->getParam('invite_id', null);
        $invite = Invite::model()->findByPk($invite_id);
        $invite->tutorial_displayed_at = null;
        $invite->save(false);
        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    public function actionConnect()
    {
        SimulationService::logAboutSim($this->getSimulationEntity(), 'internet connection break');
        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    public function actionLogCrashAction()
    {
        $action = Yii::app()->request->getParam('action');

        SimulationService::logAboutSim($this->getSimulationEntity(), 'crash action: '.$action);

        $this->sendJSON(['result' => self::STATUS_SUCCESS]);
    }

    public function actionIsEmergencyAllowed()
    {
        $simulation = $this->getSimulationEntity();
        SimulationService::logAboutSim($this->getSimulationEntity(), 'Check emergency allowed state: ' . $simulation->is_emergency_panel_allowed);

        $this->sendJSON([
            'result' => (int)$simulation->is_emergency_panel_allowed
        ]);
    }

    public function actionEmergencyClosed()
    {
        $simulation = $this->getSimulationEntity();
        $simulation->is_emergency_panel_allowed = false;
        $simulation->save();

        SimulationService::logAboutSim($this->getSimulationEntity(), 'Emergency allowed set to false');

        $this->sendJSON([
            'result' => self::STATUS_SUCCESS
        ]);
    }
}

