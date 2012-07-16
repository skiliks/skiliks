<?php



/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController{
    
    protected function _fillDayPlan($simId) {
        $tasks = array(14, 15, 19);
        
        $tasks = Tasks::model()->byIds($tasks)->findAll();
        
        foreach($tasks as $task) {
            
            $date = $task->start_time;
            
            $dayPlan = new DayPlan();
            $dayPlan->sim_id = $simId;
            $dayPlan->task_id = $task->id;
            $dayPlan->date = $date;
            $dayPlan->day = rand(1, 2);
            $dayPlan->insert();
                    
            
        }
    }
    
    protected function _fillTodo($simId) {
        $tasks = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 16, 17, 18);
        
        foreach($tasks as $taskId) {
            $todo = new Todo();
            $todo->sim_id = $simId;
            $todo->task_id = $taskId;
            $todo->insert();
        }
    }
    
    /**
     * Старт симуляции
     */
    public function actionStart() {
        $sid = Yii::app()->request->getParam('sid', false);
        
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) {
            $result = array('result' => 0, 'message' => 'cant find user');
            return $this->_sendResponse(200, CJSON::encode($result));
        }
        
        // Удаляем предыдущую симуляцию
        $simulation = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($simulation) $simulation->delete();
        
        // Создаем новую симуляцию
        $simulation = new Simulations();
        $simulation->user_id = $uid;
        $simulation->status = 1;
        $simulation->start = time();
        $simulation->difficulty = 1;
        $simulation->insert();
        
        $simId = $simulation->id;
        
        // Сделать вставки в events triggers
        $events = EventsSamples::model()->limit(1)->findAll();
        foreach($events as $event) {
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id = $simId;
            $eventsTriggers->event_id = $event->id;
            $eventsTriggers->trigger_time = time() + 20; //rand(1*60, 5*60);
            $eventsTriggers->save();
        }
        
        // предустановка задач в todo!
        $this->_fillTodo($simId);
        
        // предустановка задач в план дневной
        $this->_fillDayPlan($simId);
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    /**
     * Остановка симуляции
     */
    public function actionStop() {
        $uid = (int)Yii::app()->request->getParam('uid', false);
        
        $model = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($model) {
            $model->end = time();
            $model->status = 0;
            $model->save();
        }
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetPoint() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("empty sid");
            
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception("cant find user by sid {$sid}");

            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception("cant find simulation for uid {$uid}");
            
            $result = array();
            $result['result'] = 1;
            // определяем duration симуляции
            //$dialogsDuration = SimulationsDialogsDurations::model()->bySimulation($simulation->id)->find();
            $dialogsDuration = SimulationsDialogsDurations::model()->findByAttributes(array('sim_id'=>$simulation->id));
            if ($dialogsDuration) {
                $result['duration'] = $dialogsDuration->duration;    
            }
            else {
                $result['duration'] = 0;
            }
            
            
            // загружаем поинты
            $sql = "select 
                        sdp.count,
                        sdp.value,
                        cpt.code,
                        cpt.title
                    from simulations_dialogs_points as sdp
                    left join characters_points_titles as cpt on (cpt.id = sdp.point_id)
                    where sdp.sim_id = {$simulation->id}";
            
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
        
            $dataReader = $command->query();
        
            foreach($dataReader as $row) { 
                $result['points'][] = array(
                    'code' => $row['code'],
                    'title' => $row['title'],
                    'count' => $row['count'],
                    'value' => $row['value'],
                    'avg' => $row['value'] / $row['count']
                );
            }        
                    
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode($result));
        }

        
    }
}

?>
