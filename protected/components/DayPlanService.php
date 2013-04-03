<?php

/**
 * Description of DayPlanService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanService
{
    /**
     * @param array $ids
     * @return array
     */
    protected static function _loadTasksTitle($ids)
     {
        if (0 === count($ids)) {
            return false;
        }
        
        $tasksCollection = Task::model()->byIds($ids)->findAll();

        $tasks = [];
        foreach($tasksCollection as $task) {
            $tasks[$task->id] = array(
                'title'    => $task->title,
                'duration' => $task->duration,
                'type'     => $task->is_cant_be_moved
            );
        }
        
        return $tasks;
    }

    /**
     * @param Simulation $simulation
     * @param $taskId
     * @param $date
     * @return DayPlanAfterVacation
     */
    public static function addAfterVacation($simulation, $taskId, $date)
    {
        // Удалить задачу из дневного плана
        DayPlan::model()->deleteAllByAttributes(array(
            'sim_id'  => $simulation->id,
            'task_id' => $taskId
        ));
        
        $taskAfterVacation = new DayPlanAfterVacation();
        $taskAfterVacation->sim_id  = $simulation->id;
        $taskAfterVacation->task_id = $taskId;
        $taskAfterVacation->date    = $date;
        $taskAfterVacation->insert();
        
        // Убрать задачу из todo
        TodoService::delete($simulation->id, $taskId);

        return $taskAfterVacation;
    }
    
    /**
     * Добавление задачи на сегодня|завтра
     * @param type $simId
     * @param type $taskId
     * @param type $day
     * @param type $time 
     */
    public static function add($simulation, $taskId, $day, $time) {
        // Удалить задачу из после отпуска
        self::removeFromAfterVacation($simulation, $taskId);
        
        $dayPlan = DayPlan::model()->bySimulation($simulation->id)->byTask($taskId)->find();
        if (!$dayPlan) {
            $dayPlan          = new DayPlan();
            $dayPlan->sim_id  = $simulation->id;
            $dayPlan->task_id = $taskId;
        }    
        
        $dayPlan->date = $time;
        $dayPlan->day = $day;
        $dayPlan->save();
        
        // Убрать задачу из todo
        TodoService::delete($simulation->id, $taskId);
    }

    /**
     * @param Simulation $simulation
     * @param $taskId
     */
    public static function removeFromAfterVacation($simulation, $taskId) {
        DayPlanAfterVacation::model()->deleteAllByAttributes(array(
            'sim_id'  => $simulation->id,
            'task_id' => $taskId
        ));
    }

    /**
     * @param Simulation $simulation
     * @return array
     */
    public static function get($simulation)
    {
        try {
            $data = array();
            $tasks = array();
            $plans = DayPlan::model()->bySimulation($simulation->id)->findAll();  // byDate($fromTime, $toTime)->
            
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
            $tasks = self::_loadTasksTitle($tasks);
            
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
            $vacationsCollection = DayPlanAfterVacation::model()->bySimulation($simulation->id)->findAll();
            foreach($vacationsCollection as $item) {
                $tasks[] = $item->task_id;
                $vacationTasks[] = array(
                    'date' => GameTime::getTime($item->date),  // дата в формате hh:mm
                    'task_id' => $item->task_id
                );
            }
            
            // загрузка названий задач
            $tasks = self::_loadTasksTitle($tasks);
            
            // Добавляем задачи после отпуска
            foreach($vacationTasks as $item) {
                if (isset($tasks[$item['task_id']])) {
                    $item['title'] = $tasks[$item['task_id']]['title'];
                    $item['duration'] = $tasks[$item['task_id']]['duration'];
                }
                $item['day'] = 3;
                
                $list[] = $item;
            }

            return ['result' => 1, 'data' => $list];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage()];
        }
        
    }

    /**
     * @param Simulation $simulation
     * @param $taskId
     * @return array
     */
    public static function delete($simulation, $taskId)
    {
        try {
            DayPlan::model()->deleteAll(
                'sim_id = :simId and task_id = :taskId',
                    array(
                        ':simId'  => $simulation->id,
                        ':taskId' => (int)$taskId)
            );
            
            return ['result' => 1];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage()];
        }
    }

    /**
     * @param Simulation $simulation
     * @param $time
     * @return bool
     */
    protected static function _isAppropriateTime($simulation, $time)
    {
        if (!$simulation) {
            return false;
        }

        $duration = (GameTime::getUnixDateTime(GameTime::setNowDateTime())
            - GameTime::getUnixDateTime($simulation->start)) / 4;
        
        // если время задачи меньше времени длительности
        if (GameTime::timeToSeconds($time) < $duration) {
            return false;
        }

        return true;
    }
    
    /**
     * Проверяет подходит ли данная задача по времени
     * @param type $taskId 
     * @return boolean
     */
    protected static function _canAddTask($taskId, $time) {
        // получить длительность задачи
        $task = Task::model()->byId($taskId)->find();
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

    /**
     * @param Simulation $simulation
     * @param $taskId
     * @param $time
     * @param $day
     * @return array
     */
    public static function addToPlan($simulation, $taskId, $time, $day)
    {
        $taskId = (int)$taskId;
        $day = (int)$day;
        try {
            // на всякий случай удалим из вакейшена
            DayPlanAfterVacation::model()->deleteAllByAttributes(array(
                'sim_id' => $simulation->id, 'task_id' => $taskId
            ));
                      
            if ($day == 3) {   // Добавление на после отпуска
                self::addAfterVacation($simulation, $taskId, $time);
                return ['result' => 1];
            }
            
            // проверить не пытаемся ли мы добавить задачу раньше игрового времени
            if (!self::_isAppropriateTime($simulation, $time)) {
                return ['result' => 0, 'code' => 1];
            }
            
            // @todo: проверить подходит ли задача по времени
            if (!self::_canAddTask($taskId, $time)) {
                return ['result' => 0, 'code' => 2];
            }

            self::add($simulation, $taskId, $day, $time);
            
            // Убиваем задачу из todo
            Todo::model()->deleteAll('sim_id = :simId and task_id = :taskId',
                    array(':simId'=>$simulation->id, ':taskId'=>$taskId));
            
            return ['result' => 1];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage(), 'code' => $exc->getCode()];
        }
        
    }

    /**
     * @param Simulation $simulation
     * @param $taskId
     * @param $time
     * @return array
     * @throws Exception
     */
    public static function update($simulation, $taskId, $time)
    {
        $taskId = (int)$taskId;
        try {
            $task = DayPlan::model()->findByAttributes(array(
                'sim_id' => $simulation->id, 'task_id' => $taskId
            ));
            if (null === $task) {
                throw new Exception("C`ant find task by id {$taskId}");
            }

            return ['result' => 1];
        } catch (Exception $exc) {
            return ['result' => 0, 'message' => $exc->getMessage()];
        }
    }
    
    /**
     * @param Simulation $simulation
     * @param integer $minutes
     * @param timestamp $snapShotTime
     */
    public static function copyPlanToLog($simulation, $minutes, $snapShotTime = 1)
    {
        $todoCount = Todo::model()->bySimulation($simulation->id)->count();
        
        // copy first 2 days to DayPlanLog
        foreach (DayPlan::model()->bySimulation($simulation->id)->findAll() as $dayPlanItem) {
            $log = new DayPlanLog();
            $log->uid           = $simulation->user_id;
            $log->date          = $dayPlanItem->date;
            $log->day           = $dayPlanItem->day;
            $log->task_id       = $dayPlanItem->task_id;
            $log->sim_id        = $simulation->id;
            $log->todo_count    = $todoCount;
            $log->snapshot_time = $snapShotTime;
            $log->save();
        }
        
        // copy after vacation list to DayPlanLog
        foreach (DayPlanAfterVacation::model()->bySimulation($simulation->id)->findAll() as $dayPlanItem) {
            $log = new DayPlanLog();
            $log->uid           = $simulation->user_id;
            $log->day           = DayPlanLog::AFTER_VACATION;
            $log->task_id       = $dayPlanItem->task_id;
            $log->sim_id        = $simulation->id;
            $log->todo_count    = $todoCount;
            $log->snapshot_time = $snapShotTime;
            $log->save();
        }

        // copy 'Сделать' list to DayPlanLog
        foreach (Todo::model()->bySimulation($simulation->id)->findAll() as $dayPlanItem) {
            $log = new DayPlanLog();
            $log->uid           = $simulation->user_id;
            $log->day           = DayPlanLog::TODO;
            $log->task_id       = $dayPlanItem->task_id;
            $log->sim_id        = $simulation->id;
            $log->todo_count    = $todoCount;
            $log->snapshot_time = $snapShotTime;
            $log->save();
        }
    }
}


