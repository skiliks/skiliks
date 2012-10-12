<?php



/**
 * Description of TodoController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TodoController extends AjaxController{
    
    /**
     * Округляет время до 30 минут
     * @param type $time
     * @return type 
     */
    protected function _roundTime($time) {
        return  ceil($time / (30)) * 30;
    }
    
    public function actionGetCount() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("Не передан sid");
            $simId = SessionHelper::getSimIdBySid($sid);
            

            $data = array('result' => 1, 'data' => Todo::model()->bySimulation($simId)->count());
            return $this->_sendResponse(200, CJSON::encode($data));
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->_sendResponse(200, CJSON::encode($data));
        }       
    }
    
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("Не передан sid");
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $todoCollection = Todo::model()->bySimulation($simId)->byLatestAddingDate()->findAll();
            
            $tasks = array();
            $taskOrder = array();
            $order = 0;
            foreach($todoCollection as $item) {
                $tasks[] = $item->task_id;
                $taskOrder[$item->task_id] = $order;
                $order++;
            }
            if (count($tasks) == 0) {
                $data = array('result' => 1, 'data' => array());
                return $this->_sendResponse(200, CJSON::encode($data));
            }
            
            $tasks = Tasks::model()->byIds($tasks)->findAll();
            $list = array();
            foreach($tasks as $task) {
                $list[$taskOrder[$task->id]] = array(
                    'id' => $task->id,
                    'title' => $task->title,
                    'duration' => $this->_roundTime($task->duration)
                );
            }
            
            $data = array('result' => 1, 'data' => $list);
            return $this->_sendResponse(200, CJSON::encode($data));
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->_sendResponse(200, CJSON::encode($data));
        }    
    }
    
    public function actionAdd() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception ('wrong sid');
            $taskId = (int)Yii::app()->request->getParam('taskId', false);
            if ($taskId == 0) throw new Exception ('wrong task');

            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId) throw new Exception ('cant find simulation');

            $condition = array('sim_id' => $simId, 'task_id' => $taskId);
            
            // Удалить из дневного плана и отпуска
            DayPlan::model()->deleteAllByAttributes($condition);
            DayPlanAfterVacation::model()->deleteAllByAttributes($condition);
            
            
            $todo = Todo::model()->findByAttributes($condition);
            if ($todo) {
                return $this->_sendResponse(200, CJSON::encode(array('result' => 1)));
            }
            
            $todo = new Todo();
            $todo->sim_id = $simId;
            $todo->task_id = $taskId;
            $todo->insert();

            $data = array('result' => 1);
            $this->_sendResponse(200, CJSON::encode($data));
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->_sendResponse(200, CJSON::encode($data));
        }    
    }
}

?>
