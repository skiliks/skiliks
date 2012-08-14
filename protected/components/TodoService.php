<?php



/**
 * Description of TodoService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TodoService {
    
    /**
     * Создание задачи
     * @param Task $task 
     */
    public static function createTask(Task $task) {
        $tasks = new Tasks();
        $tasks->title = $task->title;
        $tasks->start_time = $task->startTime;
        $tasks->duration = $task->duration;
        $tasks->type = $task->type;
        $tasks->sim_id = $task->simulation;
        $tasks->insert();
        $task->id = $tasks->id;
    }
    
    /**
     * Добавить задачу в список todo
     * @param int $simId
     * @param int $taskId 
     */
    public static function add($simId, $taskId) {
        $todo = new Todo();
        $todo->sim_id = $simId;
        $todo->task_id = $taskId;
        $todo->insert();
    }
}

?>
