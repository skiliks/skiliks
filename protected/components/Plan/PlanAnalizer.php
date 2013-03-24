<?php

class PlanAnalizer {

    /*
     * Время начала симуляции
     */
    public $start_sim_time;

    /*
     * Время конца симуляции
     */
    public $end_sim_time;

    public $work_time;

    public $tasks;

    public $simId;

    public $tomorrow_day_start = '9:00';

    public $tomorrow_day_end = '16:00';

    public $tomorrow_work_time;

    public function __construct($simId) {
        $this->simId = $simId;
        $this->start_sim_time = $this->toMinutes(Yii::app()->params['simulation']['full']['start']);
        $this->end_sim_time = $this->toMinutes(Yii::app()->params['simulation']['full']['end']);

        $this->work_time = $this->end_sim_time - $this->start_sim_time;

        $this->tomorrow_work_time = $this->toMinutes($this->tomorrow_day_end) - $this->toMinutes($this->tomorrow_day_start);

        $this->tasks = DayPlanLog::model()->model()->findAllByAttributes(
            [
                'sim_id' => $this->simId
            ]);
        $this->tasks = (null === $this->tasks)?[]:$this->tasks;
    }

    public function toMinutes($time){
       return (strtotime($time) - strtotime('today'))/60;
    }

    public function run() {

        $this->check_214a1();
        $this->check_214a3();
        $this->check_214a4();
        $this->check_214a5();
        $this->check_214a8();
    }

    /*
     * "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и
     * сохранил время на незапланированные дела"
     */
    public function check_214a1() {

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);

        $duration = 0;
        foreach($this->tasks as $plan){
            if((int)$plan->task->type === Task::NO_BLOCK AND
                (int)$plan->day === DayPlanLog::TODAY AND
                    (int)$plan->snapshot_time === DayPlanLog::ON_11_00) {
                $duration += (int)$plan->task->duration;
            }
        }

        $plan_real = round($duration/$this->work_time*100, 2);
        if(0 <= $plan_real AND $plan_real <= 50 OR $plan_real > 100) {
            $value = 0;
        }elseif(50 < $plan_real  AND $plan_real <= 60 OR 90 < $plan_real AND $plan_real <= 100) {
            $value = round($behaviour->scale * 33.3 / 100, 2); // 1
        }elseif(60 < $plan_real AND $plan_real <= 70 OR 80 < $plan_real AND $plan_real <= 90 ) {
            $value = round($behaviour->scale * 66.7 / 100, 2); // 2
        }elseif(70 < $plan_real AND $plan_real <= 80){
            $value = $behaviour->scale;
        }else{
            throw new Exception("No case");
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simId;
        $assessment_calculation->save();
    }

    /*
     * 'Составляет полный план на все последующие
     * дни в этой же сессии по планированию - с утра
     * (все задачи из туду листа перенёс в форму планирования,
     * туду лист исчез), сделал это в начале дня (до всех остальных задач)'
     */
    public function check_214a3(){

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a3']);

        $todo_count = 0;
        foreach($this->tasks as $plan){
            if((int)$plan->snapshot_time === DayPlanLog::ON_11_00) {
                $todo_count += (int)$plan->todo_count;
            }
        }

        if(0 === $todo_count){
            $value = $behaviour->scale;
        }else{
            $value = 0;
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simId;
        $assessment_calculation->save();

    }

    /*
     * Составляет полный план на ЗАВТРА в
     * конце рабочего дня (на конец дня все слоты на завтра заполнены)
     */
    public function check_214a4(){

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a4']);

        $duration = 0;
        foreach($this->tasks as $plan){
            if((int)$plan->task->type === Task::NO_BLOCK AND
                (int)$plan->day === DayPlanLog::TOMORROW AND
                    (int)$plan->snapshot_time === DayPlanLog::ON_18_00) {
                $duration += (int)$plan->task->duration;
            }
        }


        $plan_real = round($duration/$this->tomorrow_work_time*100, 2);
        if($plan_real >= 70) {
            $value = $behaviour->scale;
        }elseif($plan_real < 70){
            $value = 0;
        }else{
            throw new Exception("No case");
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simId;
        $assessment_calculation->save();

    }

    /*
     * Разносит ВСЕ задачи из "сделать" в конце рабочего
     * дня (на конец дня сегодня не осталось задач в туду листе)
     */
    public function check_214a5(){

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a5']);

        $todo_count = 0;
        foreach($this->tasks as $plan){
            if((int)$plan->snapshot_time === DayPlanLog::ON_18_00) {
                $todo_count += (int)$plan->todo_count;
            }
        }

        if(0 === $todo_count){
            $value = $behaviour->scale;
        }else{
            $value = 0;
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simId;
        $assessment_calculation->save();

    }

    /*
     * Не планирует вообще. Ни один слот на сегодня
     *  и завтра не заполнен задачами
     */
    public function check_214a8() {

        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a8']);

        $count = 0;
        foreach($this->tasks as $plan){
            if((int)$plan->task->type === Task::NO_BLOCK AND (int)$plan->snapshot_time === DayPlanLog::ON_11_00 AND (int)$plan->day === DayPlanLog::TODAY OR (int)$plan->day === DayPlanLog::TOMORROW) {
                $count++;
            }
        }

        if($count > 0){
            $value = 0;
        }else{
            $value = $behaviour->scale;
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simId;
        $assessment_calculation->save();
    }
}