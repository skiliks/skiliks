<?php

class TodoController extends SimulationBaseController
{
    /**
     * @return HttpResponce
     */
    public function actionGet()
    {
        $simulationId = $this->getSimulationId(); // external for make code clean

        return $this->sendJSON(array(
                'result' => self::STATUS_SUCCESS,
                'data'   => TodoService::getTodoTasksList($simulationId),
        ));
    }

    /**
     * Adds task in todo
     *
     * @internal param taskId
     *
     * @return HttpResponse
     */
    public function actionAdd()
    {

        $simulationId = $this->getSimulationId(); // external for make code clean
        
        $taskId = Yii::app()->request->getParam('taskId', 0);
        
        // addTask() returns Todo instance or false
        $result = TodoService::addTask($taskId, $simulationId);
        
        // responce :
        if (true === $result) {
            $this->sendJSON(
                array('result' => self::STATUS_SUCCESS)
            );
        } else {
            $this->returnErrorMessage('Wrong task id '.$taskId);
        }
    }
}

