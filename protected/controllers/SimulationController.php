<?php

/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController
{
    /**
     * Старт симуляции
     */
    public function actionStart()
    {
        try {
            // тип симуляции 1 - promo, 2 - dev
            $simulationType = (int) Yii::app()->request->getParam('stype', 1);
            SimulationService::simulationStart($simulationType);

            $this->sendJSON(array(
                'result' => 1, 
                'speedFactor' => Yii::app()->params['public']['skiliksSpeedFactor']
            ));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

    /**
     * Остановка симуляции
     */
    public function actionStop()
    {
        SimulationService::simulationStop($this->getSimulationEntity());

        $this->sendJSON(array('result' => 1));
    }

    /**
     *  
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

            $simulation->deleteOldTriggers($newHours, $newMinutes);

            $this->sendJSON(array('result' => 1));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

}

