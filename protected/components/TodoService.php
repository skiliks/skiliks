<?php

/**
 * Управление списком todo.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TodoService
{

    /**
     * Создание задачи
     * @param Task $task 
     * 
     * WTF?
     */
    public static function createTask(Task $task)
    {
        $tasks = new Tasks();
        $tasks->title = $task->title;
        $tasks->start_time = $task->startTime;
        $tasks->duration = $task->duration;
        $tasks->type = $task->type;
        $tasks->sim_id = $task->simulation;
        $tasks->category = $task->category;
        $tasks->insert();
        $task->id = $tasks->id;
    }

    /**
     * Добавить задачу в список todo
     * @param int $simId
     * @param int $taskId 
     */
    public static function add($simId, $taskId)
    {
        // проверим есть ли такая задача у нас в туду
        $todo = Todo::model()->bySimulation($simId)->byTask($taskId)->find();
        if (!$todo) {
            $todo = new Todo();
            $todo->sim_id = $simId;
            $todo->task_id = $taskId;
            $todo->adding_date = GameTime::setNowDateTime();
            $todo->insert();
        }
    }

    /**
     * Удалаени задачи из списка todo в рамках заданной симуляции
     * @param int $simId
     * @param int $taskId 
     */
    public static function delete($simId, $taskId)
    {
        //Todo::model()->bySimulation($simId)->byTask($taskId)->delete();
        Todo::model()->deleteAllByAttributes(array(
            'sim_id' => $simId,
            'task_id' => $taskId
        ));
    }

    /**
     * Определить колличество задач в todo в рамках симуляции
     * @param int $sim 
     * @return int
     */
    public static function getCount($simId)
    {
        return Todo::model()->bySimulation($simId)->count();
    }

    /**
     * @param integer $simulationId
     * @return mixed array
     */
    public static function getTodoTasksList($simulationId)
    {
        try {
            $todoCollection = Todo::model()
                ->bySimulation($simulationId)
                ->byLatestAddingDate()
                ->findAll();
        } catch (Exception $e) {
            StringTools::logException($e);
            $todoCollection = array();
        }
            
        $tasks = array();
        $taskOrder = array();
        $order = 0;
        
        foreach($todoCollection as $item) {
            $tasks[] = $item->task_id;
            $taskOrder[$item->task_id] = $order;
            $order++;
        }
        
        if (count($tasks) == 0) {
            return array();
        }
        
        try {
            $tasksEntities = Tasks::model()
                ->byIds($tasks)
                ->findAll();
        } catch (Exception $e) {
            StringTools::logException($e);
            $tasksEntities = array();
        }
        
        $results = array();
        foreach($tasksEntities as $taskEntity) {
            $results[$taskOrder[$taskEntity->id]] = array(
                'id'       => $taskEntity->id,
                'title'    => $taskEntity->title,
                'duration' => TimeTools::roundTime($taskEntity->duration)
            );
        }
        
        return $results;
    }
    
    /**
     * @param int $taskId
     * @param int $simulationId, must be already verified!
     * @return boolean || Todo
     */
    public static function addTask($taskId, $simulationId)
    {
        $taskId = (int)$taskId;
        
        if ($taskId === 0) {
            return false;
        }

        $condition = array(
            'sim_id'  => $simulationId, 
            'task_id' => $taskId
        );

        // Удалить из дневного плана и отпуска
        DayPlan::model()->deleteAllByAttributes($condition);
        DayPlanAfterVacation::model()->deleteAllByAttributes($condition);


        $todoEntity = Todo::model()->findByAttributes($condition); // can exist only one for such $simId and $taskId
        if (null !== $todoEntity) {
            return $todoEntity;
        }

        $newTodoEntity = new Todo();
        $newTodoEntity->sim_id = $simulationId;
        $newTodoEntity->task_id = $taskId;
        $newTodoEntity->insert();
        
        return true;
    }
}

