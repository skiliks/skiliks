<?php

class TodoController extends SimulationBaseController
{
    /**
     *
     */
    public function actionGet()
    {
        $this->sendJSON([
            'result' => self::STATUS_SUCCESS,
            'data'   => DayPlanService::getTodoList($this->getSimulationEntity()),
        ]);
    }

    /**
     *
     */
    public function actionAdd()
    {
        $result = DayPlanService::addTask(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('taskId'),
            DayPlan::DAY_TODO
        );
        
        $this->sendJSON([
            'result' => $result ? self::STATUS_SUCCESS : self::STATUS_ERROR
        ]);
    }
}

