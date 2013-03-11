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
        // тип симуляции 1 - promo, 2 - dev
        $simulationType = (int) Yii::app()->request->getParam('stype', 1);
        $simulation = SimulationService::simulationStart($simulationType);

        if (null === $simulation) {
            $this->sendJSON(
                array(
                    'result' => 0,
                )
            );
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
        $user = SessionHelper::getUserBySid();
        if($user->isAnonymous()){
            $this->sendJSON(['result' => 1, 'redirect'=>'registration/choose-account-type']);
        }else{
            $this->sendJSON(['result' => 1, 'redirect'=>'registration/results']);
        }

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

            $simulation->deleteOldTriggers($newHours, $newMinutes);

            $this->sendJSON(array('result' => 1));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

}

