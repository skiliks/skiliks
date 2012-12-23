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
            SimulationService::simulationStart();

            $this->sendJSON(array(
                'result' => 1, 
                'speedFactor' => Yii::app()->params['skiliksSpeedFactor']
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
            SimulationService::setSimulationClockTime(
                $this->getSimulationEntity(), 
                (int)Yii::app()->request->getParam('hour', 0),
                (int)Yii::app()->request->getParam('min', 0)
            );

            $this->sendJSON(array('result' => 1));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

}

