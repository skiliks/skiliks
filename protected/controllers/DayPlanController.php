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
    public function actionGet()
    {
        $this->sendJSON(DayPlanService::get($this->getSimulationEntity()));
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
    public function actionDelete()
    {
        $this->sendJSON
            (DayPlanService::delete(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('id')
            )
        );
    }
       
    /**
     * Добавление задачи в план дневной
     */
    public function actionAdd()
    {
        $this->sendJSON(
            DayPlanService::addToPlan(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('task_id'),
                Yii::app()->request->getParam('date'),
                Yii::app()->request->getParam('day')
            )
        );
            
    }

    /**
     *
     */
    public function actionUpdate()
    {

        $this->sendJSON(
            DayPlanService::update(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('taskId'),
                Yii::app()->request->getParam('time')
            )
        );
    }
}


