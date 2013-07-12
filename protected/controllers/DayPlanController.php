<?php

class DayPlanController extends SimulationBaseController{
        
    /** 
     * @method actionGet Получить список для плана дневной 
     */
    public function actionGet()
    {
        $result = DayPlanService::get($this->getSimulationEntity());
        $result['result'] = self::STATUS_SUCCESS;
        $this->sendJSON($result);
    }
    
    /** 
     * Copy current plan state to day_plan_log
     */
    public function actionCopyPlan() {
        $minutes = (int)Yii::app()->request->getParam('minutes', false);
        
        DayPlanService::copyPlanToLog($this->getSimulationEntity(), $minutes);

        $result = [ 'result' => self::STATUS_SUCCESS ];
        $this->sendJSON($result);
    }
    
    /**
     * Удаление задачи из плана дневной
     */
    public function actionDelete()
    {
        $result = DayPlanService::delete($this->getSimulationEntity(), Yii::app()->request->getParam('id'));
        $result['result'] = self::STATUS_SUCCESS;
        $this->sendJSON($result);
    }

    /**
     * Добавление задачи в план дневной
     */
    public function actionAdd()
    {
        $result = DayPlanService::addToPlan(
                    $this->getSimulationEntity(),
                    Yii::app()->request->getParam('task_id'),
                    Yii::app()->request->getParam('date'),
                    Yii::app()->request->getParam('day')
                );
        $this->sendJSON($result);
            
    }

    public function actionSave()
    {
        $result = DayPlanService::saveToXLS($this->getSimulationEntity(), 2);
        $this->sendJSON($result);
    }
}


