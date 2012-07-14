<?php



/**
 * Description of TodoController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TodoController extends AjaxController{
    
    protected function _roundTime($time) {
        return  ceil($time / (30)) * 30;
    }
    
    public function actionGet() {
        
        
        $tasks = Tasks::model()->findAll();
            $list = array();
            foreach($tasks as $task) {
                $list[] = array(
                    'id' => $task->id,
                    'title' => $task->title,
                    'duration' => $this->_roundTime($task->duration),
                    'type' => $task->type
                );
            }
            
        $data = array('result' => 1, 'data' => $list);
        return $this->_sendResponse(200, CJSON::encode($data));
        //////////////////////////////////////////
        
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("Не передан sid");
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $todoCollection = Todo::model()->bySimulation($simId)->findAll();
            
            $tasks = array();
            foreach($todoCollection as $item) {
                $tasks[] = $item->task_id;
            }
            if (count($tasks) == 0) {
                $data = array('result' => 1, 'data' => array());
                return $this->_sendResponse(200, CJSON::encode($data));
            }
            
            $tasks = Tasks::model()->byIds($tasks)->findAll();
            $list = array();
            foreach($tasks as $task) {
                $list[] = array(
                    'id' => $task->id,
                    'title' => $task->title,
                    'duration' => $task->duration / 60
                );
            }
            
            $data = array('result' => 1, 'data' => $list);
            return $this->_sendResponse(200, CJSON::encode($data));
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->_sendResponse(200, CJSON::encode($data));
        }    
    }
}

?>
