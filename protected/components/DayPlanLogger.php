<?php



/**
 * Логгер дневного плана
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanLogger {
    
    public static function log($simId, $type=1) {
        $uid = SimulationService::getUid($simId);
        $today = DateHelper::getCurrentTimestampDate();
        
        /*$models = DayPlan::model()->bySimulation($simId)->findAll();
        foreach($models as $model) {
            
        }*/
        
        // логируем сегодня и завтра
        $sql = "insert into day_plan_log (uid, snapshot_date, date, day, task_id, snapshot_time)
            select $uid, $today, date, day, task_id, $type 
            from day_plan as p where p.sim_id = $simId";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();
        
        // логируем после отпуска
        $sql = "insert into day_plan_log (uid, snapshot_date, date, day, task_id, snapshot_time)
            select $uid, $today, date, 3, task_id, $type  
            from day_plan_after_vacation as p where p.sim_id = $simId";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();
    }
}

?>
