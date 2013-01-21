<?php

/**
 * Description of DayPlanService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanService {
    
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
    
    public function addAfterVacation($simId, $taskId, $date) {
        
        // Удалить задачу из дневного плана
        DayPlan::model()->deleteAllByAttributes(array(
            'sim_id' => $simId,
            'task_id' => $taskId
        ));
        
        $dayPlanAfterVacation = new DayPlanAfterVacation();
        $dayPlanAfterVacation->sim_id = $simId;
        $dayPlanAfterVacation->task_id = $taskId;
        $dayPlanAfterVacation->date = $date;
        $dayPlanAfterVacation->insert();
        
        // Убрать задачу из todo
        TodoService::delete($simId, $taskId);
        return true;
    }
    
    /**
     * Добавление задачи на сегодня|завтра
     * @param type $simId
     * @param type $taskId
     * @param type $day
     * @param type $time 
     */
    public function add($simId, $taskId, $day, $time) {
        // Удалить задачу из после отпуска
        $this->removeFromAfterVacation($simId, $taskId);
        
        $dayPlan = DayPlan::model()->bySimulation($simId)->byTask($taskId)->find();
        if (!$dayPlan) {
            $dayPlan = new DayPlan();
            $dayPlan->sim_id = $simId;
            $dayPlan->task_id = $taskId;
        }    
        
        $dayPlan->date = $time;
        $dayPlan->day = $day;
        $dayPlan->save();
        
        // Убрать задачу из todo
        TodoService::delete($simId, $taskId);
    }
    
    public function removeFromAfterVacation($simId, $taskId) {
        DayPlanAfterVacation::model()->deleteAllByAttributes(array(
            'sim_id' => $simId,
            'task_id' => $taskId
        ));
    }
    
    public function get($simId) {
        
        try {

            $data = array();
            $tasks = array();
            $plans = DayPlan::model()->bySimulation($simId)->findAll();  // byDate($fromTime, $toTime)->
            
            foreach($plans as $plan) {
                $tasks[] = $plan->task_id;

                $data[] = array(
                    'date' => GameTime::getTime($plan->date),
                    'task_id' => $plan->task_id,
                    'day' =>  $plan->day  //$date[self::DAY]  // день, на когда идут задачи
                    
                );
            }
            
            if (count($data)==0)  {
                return ['result' => 1, 'data' => array()];
            }
            
            if (count($tasks) == 0) {
                return ['result' => 1, 'data' => array()];
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
                $tasks[] = $item->task_id;
                $vacationTasks[] = array(
                    'date' => GameTime::getTime($item->date),  // дата в формате hh:mm
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

            return ['result' => 1, 'data' => $list];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage()];
        }
        
    }
    
    public function delete($simId, $taskId) {
        
        try {

            DayPlan::model()->deleteAll('sim_id = :simId and task_id = :taskId', 
                    array(':simId' => $simId, ':taskId' => $taskId));
            
            return ['result' => 1];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage()];
            
        }
        
    }
    
    protected function _isAppropriateTime($simId, $time) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) return false;

        $duration = (GameTime::setUnixDateTime(GameTime::setNowDateTime()) - GameTime::setUnixDateTime($simulation->start)) / 4;
        
        // если время задачи меньше времени длительности
        if (GameTime::timeToSeconds($time) < $duration) return false;
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

        $end = GameTime::addMinutesTime($time, $task->duration);
        
        $sql = "select count(*) as count from tasks 
            where 
                (start_time >= '{$time}' and start_time <= '{$end}') or
                (start_time + duration >= '{$time}' and start_time <= '{$time}') or
                (start_time >= '{$time}' and start_time + duration <= '{$end}') or
                (start_time  <= '{$time}' and start_time + duration >= '{$end}')
                ";
                
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);       
        $row = $command->queryRow();
        
        if ($row['count'] == 0) return true;
        return true;
    }
    
    public function addToPlan($simId, $taskId, $time, $day) {
        
        /* на вход : 
         *  string sid, 
         *  int taskId, 
         *  hh:mm time, 
         *  int day (1- сегодня, 2 - завтра 3- отпуск)
        */
    
        try {
            // на всякий случай удалим из вакейшена
            DayPlanAfterVacation::model()->deleteAllByAttributes(array(
                'sim_id' => $simId, 'task_id' => $taskId
            ));
                      
            if ($day == 3) {   // Добавление на после отпуска
                $this->addAfterVacation($simId, $taskId, $time);
                return ['result' => 1];
            }
            
            // проверить не пытаемся ли мы добавить задачу раньше игрового времени
            if (!$this->_isAppropriateTime($simId, $time)) {
                return ['result' => 0, 'code' => 1];
            }
            
            // @todo: проверить подходит ли задача по времени
            if (!$this->_canAddTask($taskId, $time)) {
                return ['result' => 0, 'code' => 2];
            }
            
            $this->add($simId, $taskId, $day, $time);
            
            // Убиваем задачу из todo
            Todo::model()->deleteAll('sim_id = :simId and task_id = :taskId', 
                    array(':simId'=>$simId, ':taskId'=>$taskId));
            
            return ['result' => 1];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage(), 'code' => $exc->getCode()];
        }
        
    }
    
    public function update($simId, $taskId, $time) {
        try {

            $task = DayPlan::model()->findByAttributes(array(
                'sim_id' => $simId, 'task_id' => $taskId
            ));
            if (!$task) throw new Exception("cant find task by id {$taskId}");

            return ['result' => 1];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage()];
        }
    }
}


