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
        // Тип симуляции
        $type = Yii::app()->request->getParam('type');

        // check invite if it setted {
        $invite_id = Yii::app()->request->getParam('invite_id');
        $invite = null;

        if (null !== $invite_id) {
            $invite = Invite::model()->findByPk($invite_id);
        }
        // check invite if it setted }

        $user = Yii::app()->user->data();
        $simulation = SimulationService::simulationStart($mode, $user, $type);

        if (null === $simulation) {
            $this->sendJSON(
                array(
                    'result' => 0,
                )
            );
        }

        // update invite if it setted
        if (null !== $invite) {
            $invite->simulation_id = $simulation->id;
            $invite->status = Invite::STATUS_STARTED;
            $invite->save(false, ['simulation_id', 'status']);
        }

        $this->sendJSON(
            array(
                'result'      => 1,
                'speedFactor' => Yii::app()->params['public']['skiliksSpeedFactor'],
                'simId'       => $simulation->id
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

}

