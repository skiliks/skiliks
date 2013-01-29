<?php

/**
 * Контроллер дневного плана
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanController extends AjaxController{
        
    /** 
     * @method actionGet Получить список для плана дневной 
     */
    public function actionGet() {
        $plan = new DayPlanService();
        $json = $plan->get($this->getSimulationId());
        $this->sendJSON($json);
    }
    
    /** 
     * Copy current plan state to day_plan_log
     */
    public function actionCopyPlan() {
        $minutes = (int)Yii::app()->request->getParam('minutes', false);
        
        DayPlanService::copyPlanToLog($this->getSimulationEntity(), $minutes);
        
        $this->sendJSON([ 'result' => 1 ]);
    }
    
    /**
     * Удаление задачи из плана дневной
     */
    public function actionDelete() {        
        $plan = new DayPlanService();
        $json = $plan->delete($this->getSimulationId(), (int)Yii::app()->request->getParam('id', false));
        $this->sendJSON($json);
    }
       
    /**
     * Добавление задачи в план дневной
     */
    public function actionAdd() {
        
            $plan = new DayPlanService();
            $json = $plan->addToPlan(
                    $this->getSimulationId(), 
                    (int)Yii::app()->request->getParam('task_id', false),
                    Yii::app()->request->getParam('date', false),
                    (int)Yii::app()->request->getParam('day', false));
            $this->sendJSON($json);
            
    }
    
    public function actionUpdate() {

            $plan = new DayPlanService();
            $json = $plan->update(
                    $this->getSimulationId(), 
                    (int)Yii::app()->request->getParam('taskId', false), 
                    Yii::app()->request->getParam('time', false));
            $this->sendJSON($json);
    }
}


