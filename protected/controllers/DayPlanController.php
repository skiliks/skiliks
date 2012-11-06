<?php



/**
 * Контроллер дневного плана
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanController extends AjaxController{
    
    const YEAR = 0;
    const MONTH = 1;
    const DAY = 2;
    const HOUR = 3;
    const MINUTE = 4;
    const SECOND = 5;
    
    /**
     * Преобразовывает дату в массив
     * @param type $date
     * @return type 
     */
    protected function dateToArr($date) {
        return explode('-', date('Y-m-d-G-i-S', $date));
    }
    
    /**
     * Преобразует время в массив
     * @param string $time 
     * @return array
     */
    protected function _timeToArr($time) {
         return explode(':', $time);
    }
    
    /**
     *
     * @param array $ids
     * @return array
     */
    protected function _loadTasksTitle($ids) {
        if (count($ids)==0) return false;
        
        $tasksCollection = Tasks::model()->byIds($ids)->findAll();
        $tasks = array();
        foreach($tasksCollection as $task) {
            $tasks[$task->id] = array(
                'title' => $task->title,
                'duration' => $task->duration,
                'type' => $task->type
            );
        }
        
        return $tasks;
    }
    
    protected function _shell_numberFormat($digit, $width) {
       while(strlen($digit) < $width)
         $digit = '0' . $digit;
         return $digit;
    }
    
    /** @method void actionGet Получить список для плана дневной */
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("Не передан sid");
            $simId = SessionHelper::getSimIdBySid($sid);
            
        
            $now = time();
            $date = explode('-', date('Y-m-d', $now));


            $fromTime   = mktime(0, 0, 0, $date[self::MONTH], $date[self::DAY], $date[self::YEAR]);
            $toTime = $fromTime + 2*24*60*60;
            //$toTime     = mktime(23, 59, 59, $date[1], $date[2], $date[0]);

            $data = array();
            $tasks = array();
            $plans = DayPlan::model()->bySimulation($simId)->findAll();  // byDate($fromTime, $toTime)->
            
            foreach($plans as $plan) {
                $tasks[] = $plan->task_id;

                /*
                $date = $this->dateToArr($plan->date);
                $showDate = $date[self::HOUR].':'.$date[self::MINUTE];  // дата в формате hh:mm
                 * 
                 */
                
                $showDate = DateHelper::timestampTimeToArr($plan->date);
                //$showDate = $showDate['h'].':'.$showDate['m'];
                $data[] = array(
                    'date' => $this->_shell_numberFormat($showDate['h'], 2).':'.$this->_shell_numberFormat($showDate['m'], 2),
                    'task_id' => $plan->task_id,
                    'day' =>  $plan->day  //$date[self::DAY]  // день, на когда идут задачи
                    
                );
            }
            
            if (count($data)==0)  {
                $data = array('result' => 1, 'data' => array());
                return $this->sendJSON($data);
            }
            
            if (count($tasks) == 0) {
                $data = array('result' => 1, 'data' => array());
                $this->sendJSON($data);
                return;
            }
            
            
            // загрузка названий задач
            $tasks = $this->_loadTasksTitle($tasks);
            
            // Подготовка ответа (сегодня, завтра)
            $list = array();
            foreach($data as $item) {
                if (isset($tasks[$item['task_id']])) {
                    $item['title'] = $tasks[$item['task_id']]['title'];
                    $item['duration'] = $tasks[$item['task_id']]['duration'];
                    $item['type'] = $tasks[$item['task_id']]['type'];
                }    
                $list[] = $item;  //[$item['day']]
            }
            
            ########################################################
            // Загрузка задач после отпуска
            $tasks = array();
            $vacationTasks = array();
            $vacationsCollection = DayPlanAfterVacation::model()->bySimulation($simId)->findAll();
            foreach($vacationsCollection as $item) {
                //$date = $this->dateToArr($item->date);
                $date = DateHelper::timestampTimeToArr($item->date);
                
                $tasks[] = $item->task_id;
                $vacationTasks[] = array(
                    'date' => $this->_shell_numberFormat($date['h'], 2).':'.$this->_shell_numberFormat($date['m'], 2),  // дата в формате hh:mm
                    'task_id' => $item->task_id
                );
            }
            
            // загрузка названий задач
            $tasks = $this->_loadTasksTitle($tasks);
            
            // Добавляем задачи после отпуска
            $vacations = array();
            foreach($vacationTasks as $item) {
                if (isset($tasks[$item['task_id']])) {
                    $item['title'] = $tasks[$item['task_id']]['title'];
                    $item['duration'] = $tasks[$item['task_id']]['duration'];
                }
                $item['day'] = 3;
                
                $list[] = $item;
            }
            //$list[] = $vacations;

            $data = array('result' => 1, 'data' => $list);
            $this->sendJSON($data);
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->sendJSON($data);
        }
    }
    
    /**
     * Удаление задачи из плана дневной
     */
    public function actionDelete() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if ($sid) throw new Exception("Не передан sid");
            $simId = $this->_getSimIdBySid($sid);

            $taskId = (int)Yii::app()->request->getParam('taskId', false);

            DayPlan::model()->deleteAll('sim_id = :simId and task_id = :taskId', 
                    array(':simId' => $simId, ':taskId' => $taskId));
            
            $this->sendJSON(array('result' => 1));
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->sendJSON($data);
        }
        return;
    }
    
    protected function _isAppropriateTime($simId, $time) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) return false;
        
        $start = $simulation->start;
        $duration = (time() - $start) / 4;
        
        // если время задачи меньше времени длительности
        if ($time < $duration) return false;
        return true;
    }
    
    /**
     * Проверяет подходит ли данная задача по времени
     * @param type $taskId 
     * @return boolean
     */
    protected function _canAddTask($taskId, $time) {
        // получить длительность задачи
        $task = Tasks::model()->byId($taskId)->find();
        if (!$task) throw new Exception("cant find task by id {$taskId}");
        
        $start = $time;
        $end = $time + $task->duration;
        
        $sql = "select count(*) as count from tasks 
            where 
                (start_time >= {$start} and start_time <= {$end}) or
                (start_time + duration >= {$start} and start_time <= {$start}) or
                (start_time >= {$start} and start_time + duration <= {$end} ) or
                (start_time  <= {$start} and start_time + duration >= {$end})
                ";
                
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);       
        $row = $command->queryRow();
        
        if ($row['count'] == 0) return true;
        return true;
    }
    
    
    
    /**
     * Добавление задачи в план дневной
     */
    public function actionAdd() {
        /* на вход : 
         *  string sid, 
         *  int taskId, 
         *  hh:mm time, 
         *  int day (1- сегодня, 2 - завтра 3- отпуск)
        */
    
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("Не передан sid");
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $taskId = (int)Yii::app()->request->getParam('taskId', false);
            $time = Yii::app()->request->getParam('time', false);
            $day = (int)Yii::app()->request->getParam('day', false);
            
            
            $date = explode(':', $time);
            $time = $date[0]*60 + $date[1];
            
            Logger::debug("add : $simId, $taskId, $time");
            
            // на всякий случай удалим из вакейшена
            DayPlanAfterVacation::model()->deleteAllByAttributes(array(
                'sim_id' => $simId, 'task_id' => $taskId
            ));
            
            $service = new DayPlanService();
            
            if ($day == 3) {   // Добавление на после отпуска
                $service->addAfterVacation($simId, $taskId, $time);
                $this->sendJSON(array('result' => 1));
                return;
            }
            
            // проверить не пытаемся ли мы добавить задачу раньше игрового времени
            if (!$this->_isAppropriateTime($simId, $time)) {
                $this->sendJSON(array('result' => 0, 'code' => 1));
                return;
            }
            
            // @todo: проверить подходит ли задача по времени
            if (!$this->_canAddTask($taskId, $time)) {
                $this->sendJSON(array('result' => 0, 'code' => 2));
                return;
            }
            
            $service->add($simId, $taskId, $day, $time);
            
            // Убиваем задачу из todo
            Todo::model()->deleteAll('sim_id = :simId and task_id = :taskId', 
                    array(':simId'=>$simId, ':taskId'=>$taskId));
            
            $this->sendJSON(array('result' => 1));
            return;
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage(), 'code' => $exc->getCode());
            $this->sendJSON($data);
        }
    }
    
    public function actionUpdate() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("Не передан sid");
                $simId = SessionHelper::getSimIdBySid($sid);

            $taskId = (int)Yii::app()->request->getParam('taskId', false);
            $time = Yii::app()->request->getParam('time', false);

            $task = DayPlan::model()->findByAttributes(array(
                'sim_id' => $simId, 'task_id' => $taskId
            ));
            if (!$task) throw new Exception("cant find task by id {$taskId}");
 
            // преобразовать время в unixtime
            $time = $this->_timeToArr($time);
            $time = mktime($time[0], $time[1], 0, 0, 0, 0);



            $data = array('result' => 1);
            $this->sendJSON($data);
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->sendJSON($data);
        }
    }
}

?>
