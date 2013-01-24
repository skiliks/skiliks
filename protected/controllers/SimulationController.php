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
        try {
            // тип симуляции 1 - promo, 2 - dev
            $simulationType = (int) Yii::app()->request->getParam('stype', 1);
            SimulationService::simulationStart($simulationType);

            $this->sendJSON(
                array(
                    'result' => 1,
                    'speedFactor' =>
                        Yii::app()->params['public']['skiliksSpeedFactor']
                )
            );
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

    /**
     * Остановка симуляции
     */
    public function actionStop()
    {
        SimulationService::simulationStop($this->getSimulationId());

        $this->sendJSON(array('result' => 1));
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
        try {
            $newHours = (int)Yii::app()->request->getParam('hour', 0);
            $newMinutes = (int)Yii::app()->request->getParam('min', 0);
            $simulation = $this->getSimulationEntity();
            SimulationService::setSimulationClockTime(
                $simulation,
                $newHours,
                $newMinutes
            );

            //$simulation->deleteOldTriggers($newHours, $newMinutes);

            $this->sendJSON(array('result' => 1));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

}

