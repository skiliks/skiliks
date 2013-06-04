<?php
/**
 * Контроллер симуляции
 *
 * PHP Version 5.4
 *
 * @package  None
 * @link     skiliks.com
 * @author   Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 * @license  proprietary http://skiliks.com/
 */
class SimulationController extends AjaxController
{
    /**
     * Старт симуляции
     *
     * @return string
     */
    public function actionStart()
    {
        // Режим симуляции: promo, dev
        $mode = Yii::app()->request->getParam('mode');
        $type = Yii::app()->request->getParam('type');
        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        // check invite if it setted {
        $invite_id = Yii::app()->request->getParam('invite_id');
        $invite = Invite::model()->findByPk($invite_id);

        $scenarioName = null;

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

        if (null === $simulation) {
            $this->sendJSON(
                array(
                    'result' => 0,
                )
            );
        }

        $this->sendJSON(
            array(
                'result'       => 1,
                'speedFactor'  => $simulation->getSpeedFactor(),
                'simId'        => $simulation->id,
                'scenarioName' => $scenarioName
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
        $this->sendJSON(['result' => 1]);

    }

    /**
     * Установка симуляции на паузу
     */
    public function actionStartPause()
    {
        SimulationService::pause(
            $this->getSimulationEntity()
        );
        $this->sendJSON(['result' => 1]);
    }

    /**
     * Возобновление симуляции
     */
    public function actionStopPause()
    {
        SimulationService::resume(
            $this->getSimulationEntity()
        );
        $this->sendJSON(['result' => 1]);
    }

    /**
     * Get user's score
     */
    public function actionGetPoint()
    {
        $simulation = $this->getSimulationEntity();
        
        try {
            $this->sendJSON(SimulationService::getPointsForDebug($simulation));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
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
        if (null !== $invite && $invite->isAccepted() && false === $invite->scenario->isLite()) {
            $invite->status = Invite::STATUS_STARTED;
            $invite->save(false);
            if (Yii::app()->user->data()->isCorporate()) {
                Yii::app()->user->data()->getAccount()->invites_limit--;
                Yii::app()->user->data()->getAccount()->save(false);
            }
        }
    }
}

