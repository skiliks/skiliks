<?php



/**
 * Description of DayPlanService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanService {
    
    public function addAfterVacation($simId, $taskId, $date) {
        Logger::debug("_addDayPlanAfterVacation : $simId $taskId $date");
        
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
    }
    
    public function removeFromAfterVacation($simId, $taskId) {
        DayPlanAfterVacation::model()->deleteAllByAttributes(array(
            'sim_id' => $simId,
            'task_id' => $taskId
        ));
    }
}

?>
