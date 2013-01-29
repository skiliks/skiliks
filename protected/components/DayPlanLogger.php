<?php



/**
 * Логгер дневного плана
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanLogger {
    
    const ELEVEN = 1;
    
    const STOP = 2;
    
    /**
     * Логировать дневной план
     * @param int $simId
     * @param int $type 
     */
    public static function log($simId, $type=1) {
        $uid = SimulationService::getUid($simId);
        $today = DateHelper::getCurrentTimestampDate();
        
        // определить колличество задач в todo
        $todoCount = TodoService::getCount($simId);
        
        // логируем сегодня и завтра
        $sql = "insert into day_plan_log (uid, snapshot_date, date, day, task_id, snapshot_time, todo_count, sim_id)
            select $uid, $today, date, day, task_id, $type, $todoCount, $simId 
            from day_plan as p where p.sim_id = $simId";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();
        
        // логируем после отпуска
        $sql = "insert into day_plan_log (uid, snapshot_date, date, day, task_id, snapshot_time, todo_count, sim_id)
            select $uid, $today, date, 3, task_id, $type, $todoCount, $simId  
            from day_plan_after_vacation as p where p.sim_id = $simId";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();
    }
}


