<?php

class DayPlanController extends SimulationBaseController{
        
    /** 
     * @method actionGet Получить список для плана дневной 
     */
    public function actionGet()
    {
        $result = DayPlanService::getPlanList($this->getSimulationEntity());
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
        $result = DayPlanService::deleteTask(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('id')
        );

        $result['result'] = self::STATUS_SUCCESS;
        $this->sendJSON($result);
    }

    /**
     * Добавление задачи в план дневной
     */
    public function actionAdd()
    {
        $result = DayPlanService::addTask(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('task_id'),
            Yii::app()->request->getParam('day'),
            Yii::app()->request->getParam('date')
        );

        $this->sendJSON([
            'result' => $result ? self::STATUS_SUCCESS : self::STATUS_ERROR
        ]);
    }

    public function actionSave()
    {
        $result = DayPlanService::saveToXLS($this->getSimulationEntity());
        $this->sendJSON($result);
    }
}


