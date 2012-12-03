<?php



/**
 * Управление списком todo.
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
        $tasks->category = $task->category;
        $tasks->insert();
        $task->id = $tasks->id;
    }
    
    /**
     * Добавить задачу в список todo
     * @param int $simId
     * @param int $taskId 
     */
    public static function add($simId, $taskId) {
        // проверим есть ли такая задача у нас в туду
        $todo = Todo::model()->bySimulation($simId)->byTask($taskId)->find();
        if (!$todo) {
            $todo = new Todo();
            $todo->sim_id = $simId;
            $todo->task_id = $taskId;
            $todo->adding_date = time();
            $todo->insert();
        }
    }
    
    /**
     * Удалаени задачи из списка todo в рамках заданной симуляции
     * @param int $simId
     * @param int $taskId 
     */
    public static function delete($simId, $taskId) {
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
    public static function getCount($simId) {
        return Todo::model()->bySimulation($simId)->count();
    }
}


