<?php

class PlanAnalyzer {
    /*
     * Время начала симуляции
     */
    public $start_sim_time;

    /*
     * Время конца симуляции
     */
    public $end_sim_time;

    public $work_time;

    public $tasksOn11 = [];

    public $tasksOn18 = [];

    public $simulation;

    public $tomorrow_day_start = '9:00';

    public $tomorrow_day_end = '16:00';

    public $tomorrow_work_time;

    /**
     * @param Simulation $simulation
     */
    public function __construct($simulation)
    {
        $this->simulation = $simulation;
        $this->start_sim_time = $this->toMinutes(Yii::app()->params['simulation']['full']['start']);
        $this->end_sim_time   = $this->toMinutes(Yii::app()->params['simulation']['full']['end']);

        $this->work_time = $this->end_sim_time - $this->start_sim_time;

        $this->tomorrow_work_time = $this->toMinutes($this->tomorrow_day_end) - $this->toMinutes($this->tomorrow_day_start);

        $this->tasksOn11 = DayPlanLog::model()->model()->findAllByAttributes([
            'sim_id'        => $this->simulation->id,
            'snapshot_time' => DayPlanLog::ON_11_00,
        ],
        [
            'order' => ' day, date ',
        ]);

        $this->tasksOn18 = DayPlanLog::model()->model()->findAllByAttributes([
            'sim_id'        => $this->simulation->id,
            'snapshot_time' => DayPlanLog::ON_18_00,
        ],
        [
            'order' => ' day, date ',
        ]);


        $this->tasksOn11 = (null === $this->tasksOn11) ? []  : $this->tasksOn11;
        $this->tasksOn18 = (null === $this->tasksOn18) ? []  : $this->tasksOn18;
    }

    /**
     * @param $time
     * @return float
     */
    public function toMinutes($time)
    {
       return (strtotime($time) - strtotime('today'))/60;
    }

    public function run()
    {
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
    public function check_214a1()
    {
        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a1']);

        $duration = 0;
        foreach ($this->tasksOn11 as $plan){
            if ((int)$plan->task->is_cant_be_moved == Task::NO_BLOCK &&
                (int)$plan->day === DayPlanLog::TODAY) {
                $duration += (int)$plan->task->duration;
            }
        }

        $plan_real = round($duration/$this->work_time*100, 2);
        if (0 <= $plan_real && $plan_real <= 50 || $plan_real > 100) {
            $value = 0;
        } elseif (50 < $plan_real  && $plan_real <= 60 || 90 < $plan_real && $plan_real <= 100) {
            $value = round($behaviour->scale * 33.3 / 100, 2); // 1
        } elseif (60 < $plan_real && $plan_real <= 70 || 80 < $plan_real && $plan_real <= 90 ) {
            $value = round($behaviour->scale * 66.7 / 100, 2); // 2
        } elseif (70 < $plan_real && $plan_real <= 80) {
            $value = $behaviour->scale;
        } else {
            throw new Exception("No case");
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simulation->id;
        $assessment_calculation->save();
    }

    /*
     * 'Составляет полный план на все последующие
     * дни в этой же сессии по планированию - с утра
     * (все задачи из туду листа перенёс в форму планирования,
     * туду лист исчез), сделал это в начале дня (до всех остальных задач)'
     */
    public function check_214a3()
    {
        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a3']);

        $todo_count = 0;
        foreach ($this->tasksOn11 as $plan) {
            if (DayPlanLog::TODO == $plan->day) {
                $todo_count++;
            }
        }

        if (0 === $todo_count) {
            $value = $behaviour->scale;
        } else {
            $value = 0;
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simulation->id;
        $assessment_calculation->save();

    }

    /*
     * Составляет полный план на ЗАВТРА в
     * конце рабочего дня (на конец дня все слоты на завтра заполнены)
     */
    public function check_214a4()
    {
        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a4']);

        $duration = 0;
        foreach ($this->tasksOn18 as $plan) {
            if ((int)$plan->task->is_cant_be_moved == Task::NO_BLOCK &&
                (int)$plan->day === DayPlanLog::TOMORROW) {
                $duration += (int)$plan->task->duration;
            }
        }

        $plan_real = round($duration/$this->tomorrow_work_time*100, 2);
        if ($plan_real >= 70) {
            $value = $behaviour->scale;
        } elseif ($plan_real < 70){
            $value = 0;
        } else {
            throw new Exception("No case");
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simulation->id;
        $assessment_calculation->save();

    }

    /*
     * Разносит ВСЕ задачи из "сделать" в конце рабочего
     * дня (на конец дня сегодня не осталось задач в туду листе)
     */
    public function check_214a5()
    {
        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a5']);

        $todo_count = 0;
        foreach ($this->tasksOn18 as $plan) {
            if (DayPlanLog::TODO == $plan->day) {
                $todo_count++;
            }
        }

        if (0 === $todo_count) {
            $value = $behaviour->scale;
        } else {
            $value = 0;
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simulation->id;
        $assessment_calculation->save();
    }

    /*
     * Не планирует вообще. Ни один слот на сегодня
     *  и завтра не заполнен задачами
     */
    public function check_214a8()
    {
        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214a8']);

        $count = 0;
        foreach($this->tasksOn11 as $plan){
            if((int)$plan->task->is_cant_be_moved == Task::NO_BLOCK && (int)$plan->day === DayPlanLog::TODAY || (int)$plan->day === DayPlanLog::TOMORROW) {
                $count++;
            }
        }

        foreach($this->tasksOn18 as $plan){
            if((int)$plan->task->is_cant_be_moved == Task::NO_BLOCK && (int)$plan->day === DayPlanLog::TODAY || (int)$plan->day === DayPlanLog::TOMORROW) {
                $count++;
            }
        }

        if ($count > 0){
            $value = 0;
        } else {
            $value = $behaviour->scale;
        }

        $assessment_calculation = new AssessmentCalculation();
        $assessment_calculation->point_id = $behaviour->id;
        $assessment_calculation->value = $value;
        $assessment_calculation->sim_id = $this->simulation->id;
        $assessment_calculation->save();
    }

    public function check_214b1()
    {
        $behaviour = HeroBehaviour::model()->findByAttributes(['code'=>'214b1']);

        $wrongActions = [];
        $rightActions = [];

        foreach ($this->tasksOn11 as $taskLogItem) {
            $data = [];

            if (0 == $taskLogItem->task->category) {
                $data = $this->findLessImportantTaskLogsBefore($this->tasksOn11, $taskLogItem);
                if (0 < count($data)) {
                    $wrongActions[] = $taskLogItem;
                } elseif (0 == count($data)) {
                    $rightActions[] = $taskLogItem;
                }
            }
        }

        foreach ($rightActions as $rightAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $rightAction->task->id;
            $assessment->value             = 1;
            $assessment->save();
        }

        foreach ($wrongActions as $wrongAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $wrongAction->task->id;
            $assessment->value             = 0;
            $assessment->save();
        }

        $rate = count($rightActions) / (count($rightActions) +  count($wrongActions));
        var_dump($rate);
        $value = $behaviour->scale * $rate;

        $assessmentAggregated           = new AssessmentCalculation();
        $assessmentAggregated->sim_id   = $this->simulation->id;
        $assessmentAggregated->point_id = $behaviour->id;
        $assessmentAggregated->value    = $value;
        $assessmentAggregated->save();
    }

    /**
     * Можем ли мы сравтить 2 задачи
     *
     * @param DayPlanLog $task
     * @param DayPlanLog $taskToCompare
     * @return bool
     */
    public function isComparable($task, $taskToCompare)
    {
        return (
            null !== $task->date &&
            '00:00:00' !== $task->date &&
            null !== $taskToCompare->date &&
            '00:00:00' !== $taskToCompare->date
        );
    }

    /**
     * @param array of DayPlanLog $tasks
     * @param DayPlanLog $task
     */
    public function findLessImportantTaskLogsBefore($taskLogs, $mainTaskLogItem)
    {
        $result = [];

        foreach ($taskLogs as $taskLogItem) {
            if ($taskLogItem->task->code == $mainTaskLogItem->task->code) {
                break;
            }

            if ($mainTaskLogItem->task->category < $taskLogItem->task->category) {
                $result[] = $mainTaskLogItem;
            }
        }

        return $result;
    }
}