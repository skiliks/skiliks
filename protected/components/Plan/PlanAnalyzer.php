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

    public $logActivityActionsAggregatedGroupByParent = [];

    public $logAggregated214d = [];

    public $parents_keep_last_category = [];

    public $parents_ending_time = [];

    /**
     * @param Simulation $simulation
     */
    public function __construct($simulation)
    {
        $this->simulation = $simulation;
        $this->start_sim_time = $this->toMinutes($simulation->game_type->start_time);
        $this->end_sim_time   = $this->toMinutes($simulation->game_type->end_time);

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



        /*
         * @var $groupedLog:
         * array [
         * 'parent'
         * 'grandparent'
         * 'category'
         * 'start'
         * 'end'
         * 'available'
         * ]
         *
         * */
        $groupedLog = [];
        $log_214d = [];

        $currentParentCode = null;
        $i = 0;

        foreach ($this->simulation->log_activity_actions_aggregated as $logItem) {
            $logItem->activityAction;
            $code = (null === $logItem->activityAction) ? null : $logItem->activityAction->activity->code;

            // @for: SKILIKS-2754
            if ('plan' == $logItem->leg_action
                || 'A_wait' == $code
                || 'A_wrong_call' == $code
                || 'A_already_used' == $code
                || '2_min' == $logItem->category
                || 'D24' === $logItem->leg_action
                || 'D27' === $logItem->leg_action
                || 'D8' === $logItem->leg_action
                || 'D20' === $logItem->leg_action
                || 'D13' === $logItem->leg_action
                || 'MSY10' === $logItem->leg_action
                || 'MSY1' === $logItem->leg_action
                || 'D9' === $logItem->leg_action
                || 'D2' === $logItem->leg_action
                || 'D25' === $logItem->leg_action
            ) {
                // 0
                $logItem->keep_last_category_after_60_sec = 0;
                $logItem->save();
                continue;
            }

            if ($logItem->activityAction->activity->parent != $currentParentCode &&
                $this->simulation->isFull()) {
                $currentParentCode = $logItem->activityAction->activity->parent;
                $parentAvailability = $simulation->game_type->getActivityParentAvailability([
                    'code' => $logItem->activityAction->activity->parent
                ]);

                $groupedLog[] = [
                    'parent'      => $logItem->activityAction->activity->parent,
                    'grandparent' => $logItem->activityAction->activity->grandparent,
                    'category'    => $logItem->category,
                    'start'       => $logItem->start_time,
                    'end'         => $logItem->end_time,
                    'available'   => $this->calculateParentAvailability($parentAvailability, $groupedLog),
                    'keepLastCategoryAfter60sec' => LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES ===
                        $this->calcKeepLastCategoryAfter(
                            $logItem->start_time,
                            $logItem->end_time,
                            $parentAvailability->is_keep_last_category
                        )
                ];
                $log_214d[] = [
                    'sim_id' => $logItem->sim_id,
                    'leg_type' => $logItem->leg_type,
                    'leg_action' => $logItem->leg_action,
                    'activity_action_id' => $logItem->activity_action_id,
                    'parent' => $logItem->activityAction->activity->parent,
                    'category' => $logItem->category,
                    'start_time' => $logItem->start_time,
                    'end_time' => $logItem->end_time,
                ];
                $i++;
            } elseif ($logItem->activityAction->activity->parent == $currentParentCode) {
                $groupedLog[($i - 1)]['end'] = $logItem->end_time;
                $log_214d[($i - 1)]['end_time'] = $logItem->end_time;
                $currentParentCode = $logItem->activityAction->activity->parent;
            }

            $logItem->keep_last_category_after_60_sec = $groupedLog[$i - 1]['keepLastCategoryAfter60sec'];
            $logItem->save();

        }

        $this->logActivityActionsAggregatedGroupByParent = $groupedLog;
        $parents = $simulation->game_type->getActivityParentsAvailability();

        /* @var $parent ActivityParentAvailability */
        foreach($parents as $parent){
            $this->parents_keep_last_category[$parent->code] = ((int)$parent->is_keep_last_category === 1)?LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES:LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_NO;
        }

        /* Log ActivityActionsAggregated214d */
        foreach($log_214d as $log) {
            $var_214d = new LogActivityActionAgregated214d();
            $var_214d->sim_id = $log['sim_id'];
            $var_214d->leg_type = $log['leg_type'];
            $var_214d->leg_action = $log['leg_action'];
            $var_214d->activity_action_id = $log['activity_action_id'];
            $var_214d->category = $log['category'];
            $var_214d->start_time = $log['start_time'];
            $var_214d->end_time = $log['end_time'];
            $var_214d->duration = gmdate('H:i:s', strtotime($log['end_time'])-strtotime($log['start_time']));
            $var_214d->keep_last_category_initial = $this->parents_keep_last_category[$log['parent']];
            $var_214d->keep_last_category_after = $this->calcKeepLastCategoryAfter($log['start_time'], $log['end_time'], $var_214d->keep_last_category_initial);
            $var_214d->parent = $log['parent'];
            $var_214d->save();
        }

        $this->logAggregated214d = LogActivityActionAgregated214d::model()->findAllByAttributes(['sim_id'=>$simulation->id]);

//        $parents_ending = SimulationCompletedParent::model()->findAllByAttributes(['sim_id'=>$simulation->id]);

        /* @var $sim_log SimulationCompletedParent */
        //foreach($parents_ending as $sim_log) {
        //    $this->parents_ending_time[$sim_log->parent_code] = $sim_log->end_time;
        //}
    }

    public static function calcKeepLastCategoryAfter($start_time, $end_time, $keep_last_category_initial) {
        if($keep_last_category_initial === LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES) {
            if((Yii::app()->params['keep_last_category_time_214g']/60*Yii::app()->params['public']['skiliksSpeedFactor']) <=
                ((strtotime($end_time) - strtotime($start_time))/60)){
                return LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_NO;
            }else{
                return LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES;
            }
        }else{
            return LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_NO;
        }
    }

    public function isLastParent($parent, $current) {

        foreach($this->logAggregated214d as $find => $log) {
            /* @var $log LogActivityActionAgregated214d */
            if($current < $find){
                if($parent === $log->parent){
                    return false;
                }
            }
        }
        return true;

    }

    public function calculateParentAvailability($parentAvailability, $groupedLog)
    {
        if (null === $parentAvailability) {
            return null;
        }

        if($parentAvailability->code === 'T7b') {
            $max_end_time = 0;

            foreach($groupedLog as $log){
                if($log['parent'] === "T7a" && !empty($log['end'])){
                    $max_end_time = ($max_end_time < strtotime($log['end'])) ? strtotime($log['end']) : $max_end_time;
                }
            }
            if(0 !== $max_end_time){
                return (new DateTime())->setTimestamp($max_end_time)->add(new DateInterval("PT2H"))->format("H:i:s");
            }
        }

        if($parentAvailability->code === 'TM8') {
            $startTimes = [];

            // when parent logged at first {
            $parentTM8activityIds = [];

            $activities = Activity::model()->findAllByAttributes([
                'parent'      => 'TM8',
                'scenario_id' => $this->simulation->game_type->id
            ]);
            foreach ($activities as $activity) {
                $parentTM8activityIds[] = $activity->id;
            }

            $parentTM8activityLogsIds = [];

            $activityActions = ActivityAction::model()->findAll(
                ' activity_id IN ('.implode(',', $parentTM8activityIds).')',[]
            );

            foreach ($activityActions as $activityAction) {
                $parentTM8activityActionIds[] = $activityAction->id;
            }

            $parentTM8firstLog = LogActivityAction::model()->find([
                'condition' => ' `t`.`sim_id` = :sim_id AND `t`.`activity_action_id` IN ('.implode(', ', $parentTM8activityActionIds).')',
                'params' => [
                    'sim_id' => $this->simulation->id,
                ],
                'order' => 'start_time ASC'
            ]);

            if (null !== $parentTM8firstLog) {
                $startTimes[] = strtotime($parentTM8firstLog->start_time);
            }

            // when parent logged at first }

            // when M8 read {
            $mail_template = $this->simulation->game_type->getMailTemplate(['code'=>"M8"]);

            if(null !== $mail_template){
                $m8 = MailBox::model()->findByAttributes([
                    'template_id' => $mail_template->id,
                    'sim_id'      => $this->simulation->id
                ]);

                if(null !== $m8){
                    $log_mail = LogMail::model()->findByAttributes([
                        'mail_id' => $m8->id,
                        'sim_id'  =>  $this->simulation->id
                    ]);

                    if(null !== $log_mail){
                        $startTimes[] = strtotime($log_mail->start_time);
                    }
                }
            }
            // when M8 read }

            // get ET8 start time {
            $dialog = $this->simulation->game_type->getDialog(['code'=>'ET8']);
            $startTimes[] = strtotime($dialog->start_time);
            // get ET8 start time }

            // return minimum of [TM8 first log, M8 read time, ET8 start time]
            return (new DateTime())->setTimestamp(min($startTimes))->format("H:i:s");

        }

        return $parentAvailability ? $parentAvailability->available_at : null;
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
        // 214a
        $this->check_214a1();
        $this->check_214a3();
        $this->check_214a4();
        $this->check_214a5();
        $this->check_214a8();

        // 214b
        $this->check_214b0_214b4('214b0', 0);
        $this->check_214b0_214b4('214b1', 1);
        $this->check_214b0_214b4('214b2', 2);
        $this->check_214b0_214b4('214b3', 3);
        $this->check_214b0_214b4('214b4', 4);

        $this->check_214b5_6_8('214b5', 0);
        $this->check_214b5_6_8('214b6', 1);
        $this->check_214b5_6_8('214b8', 2);

        $this->check_214b9();

        $this->check_214d0_214d4('214d0', 0);
        $this->check_214d0_214d4('214d1', 1);
        $this->check_214d0_214d4('214d2', 2);
        $this->check_214d0_214d4('214d3', 3);
        $this->check_214d0_214d4('214d4', 4);

        $this->check_214d5_6_8('214d5', 0, [4,5]);
        $this->check_214d5_6_8('214d6', 1, [4,5]);
        $this->check_214d5_6_8('214d8', 2, [4,5]);

        $this->check_214g('214g0', '0', []);
        $this->check_214g('214g1', '1', ['0']);
    }

    /*
     * "Составляет план на сегодня до 11 утра.
     * Заполнил задачами все слоты на сегодня и
     * сохранил время на незапланированные дела"
     */
    public function check_214a1()
    {
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'214a1']);

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
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'214a3']);

        $todo_count = 0;
        foreach ($this->tasksOn11 as $plan) {
            if (DayPlanLog::TODO == $plan->day) {
                $todo_count++;
            }
        }

        $total = count($this->tasksOn11);
        $total = (0 < $total) ? $total : 1;
        if ($todo_count/$total <= 0.15 ) {
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
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'214a4']);

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
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'214a5']);

        $todo_count = 0;
        foreach ($this->tasksOn18 as $plan) {
            if (DayPlanLog::TODO == $plan->day) {
                $todo_count++;
            }
        }

        $total = count($this->tasksOn18);
        $total = (0 < $total) ? $total : 1;
        if ($todo_count/$total <= 0.15) {
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
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>'214a8']);

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

    /**
     * Assessment according 11:00 planned tasks log only
     *
     * @param $code
     * @param $category
     */
    public function check_214b0_214b4($code, $category)
    {
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code' => $code]);

        $wrongActions = [];
        $rightActions = [];

        $usedTaskCodes = [];

        foreach ($this->tasksOn11 as $taskLogItem) {
            $data = [];

            if ($this->canBeAssessedBy214b($taskLogItem, $category)) {
                $data = $this->findLessImportantTaskLogsBefore($this->tasksOn11, $taskLogItem, $usedTaskCodes);
                if (0 < count($data)) {
                    $wrongActions[] = $taskLogItem;

                    $usedTaskCodes[] = $data[0]->task->code;
                } elseif (0 == count($data)) {
                    if ($this->canAddPlusOneBy214b($taskLogItem)) {
                        $rightActions[] = $taskLogItem;
                    }
                }
            }
        }

        foreach ($rightActions as $rightAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $rightAction->task->id;
            $assessment->value             = 1;
            $assessment->type_scale        = 1;
            $assessment->save();
        }

        foreach ($wrongActions as $wrongAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $wrongAction->task->id;
            $assessment->type_scale        = 2;
            $assessment->value             = 0;
            $assessment->save();
        }

        if (0 == (count($rightActions) +  count($wrongActions))) {
            $rate = 0;
        } else {
            $rate = count($rightActions) / (count($rightActions) +  count($wrongActions));
        }

        if ($behaviour === null) {
            return;
        }

        $value = $behaviour->scale * $rate;

        $assessmentCalculation           = new AssessmentCalculation();
        $assessmentCalculation->sim_id   = $this->simulation->id;
        $assessmentCalculation->point_id = $behaviour->id;
        $assessmentCalculation->value    = round($value, 2);
        $assessmentCalculation->save();
    }

    /**
     * Assessment according 11:00 planned tasks log only
     *
     * @param string $code
     * @param int $category
     * @param array $categories
     */
    public function check_214b5_6_8($code = '214b5', $category = 0, $wrongCategoryIds = [4,5])
    {
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code' => $code]);

        $wrongActions = [];

        $isStartAssessment = false;

        // from less time to day-1 9:00
        foreach (array_reverse($this->tasksOn11) as $taskLogItem) {

            if (false == $isStartAssessment && $category == $taskLogItem->task->category
            ) {
                $isStartAssessment = true;
            }

            if (in_array($taskLogItem->day, [DayPlanLog::AFTER_VACATION, DayPlanLog::TODO])) {
                continue;
            }

            if ($isStartAssessment &&
                in_array($taskLogItem->task->category, $wrongCategoryIds)
            ) {
                $wrongActions[] = $taskLogItem;
            }
        }

        foreach ($wrongActions as $wrongAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $wrongAction->task->id;
            $assessment->value             = 1;
            $assessment->type_scale        = 2;
            $assessment->save();
        }

        $assessmentCalculation           = new AssessmentCalculation();
        $assessmentCalculation->sim_id   = $this->simulation->id;
        $assessmentCalculation->point_id = $behaviour->id;
        $assessmentCalculation->value    = $behaviour->scale * count($wrongActions);
        $assessmentCalculation->save();
    }

    /**
     * Assessment according 11:00 planned tasks log only
     *
     * @param string $code
     * @param int $category
     * @param array $categories
     */
    public function check_214b9()
    {
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code' => '214b9']);

        $wrongActions = [];
        $rightActions = [];

        // from less time to day-1 9:00
        foreach (array_reverse($this->tasksOn11) as $taskLogItem) {

            if ('yes' !== $taskLogItem->task->time_limit_type
            ) {
                continue;
            }

            if (DayPlanLog::AFTER_VACATION == $taskLogItem->day ||
                DayPlanLog::TODO == $taskLogItem->day
            ) {
                continue;
            }

            if (2 < $taskLogItem->task->category ) {
                continue;
            }

            $dayId = null;
            if ('today' == $taskLogItem->task->fixed_day) {
                $dayId = 1;
            } elseif ('tomorrow' == $taskLogItem->task->fixed_day) {
                $dayId = 2;
            } else {
                continue;
            }

            if (
                $taskLogItem->day == $dayId &&
                $taskLogItem->date == $taskLogItem->task->start_time
            ) {
                $rightActions[] = $taskLogItem;
            } else {
                $wrongActions[] = $taskLogItem;
            }
        }

        foreach ($rightActions as $rightAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $rightAction->task->id;
            $assessment->value             = 1;
            $assessment->type_scale        = 1;
            $assessment->save();
        }

        foreach ($wrongActions as $wrongAction) {
            $assessment                    = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id = $behaviour->id;
            $assessment->sim_id            = $this->simulation->id;
            $assessment->task_id           = $wrongAction->task->id;
            $assessment->value             = 0;
            $assessment->type_scale        = 2;
            $assessment->save();
        }

        if (0 == (count($rightActions) +  count($wrongActions))) {
            $rate = 0;
        } else {
            $rate = count($rightActions) / (count($rightActions) +  count($wrongActions));
        }
        $value = $behaviour->scale * $rate;

        $assessmentCalculation           = new AssessmentCalculation();
        $assessmentCalculation->sim_id   = $this->simulation->id;
        $assessmentCalculation->point_id = $behaviour->id;
        $assessmentCalculation->value    = $value;
        $assessmentCalculation->save();
    }

    /**
     * Можем ли мы сравтить 2 задачи
     *
     * @param DayPlanLog $task
     * @param DayPlanLog $taskToCompare
     * @return bool
     */
    public function isComparable($taskLogItem, $taskLogItemToCompare)
    {
        return (
            null !== $taskLogItemToCompare->date &&
            '00:00:00' !== $taskLogItemToCompare->date
        );
    }

    /**
     * @param array of DayPlanLog $tasks
     * @param DayPlanLog $task
     */
    public function findLessImportantTaskLogsBefore($taskLogs, $mainTaskLogItem, $usedTaskCodes)
    {
        $result = [];

        // $usedTaskCodes = [];

        foreach ($taskLogs as $taskLogItem) {
            if ('can\'t be moved' == $taskLogItem->task->time_limit_type) {
                continue;
            }

            if ($taskLogItem->task->code == $mainTaskLogItem->task->code) {
                break;
            }

            if ($mainTaskLogItem->task->category < $taskLogItem->task->category &&
                $this->isComparable($mainTaskLogItem, $taskLogItem) &&
                false == in_array($taskLogItem->task->code, $usedTaskCodes)) {

                //$usedTaskCodes[] = $taskLogItem->task->code;
                $result[] = $taskLogItem;
            }
        }

        return $result;
    }

    public function canAddPlusOneBy214b($mainTaskLogItem)
    {
        $result = true;

        if (DayPlanLog::AFTER_VACATION == $mainTaskLogItem->day || DayPlanLog::TODO == $mainTaskLogItem->day ) {
            $result = false;
        }

        return $result;
    }

    public function canBeAssessedBy214b($mainTaskLogItem, $category)
    {
        $isLowPriorityFixedDateTask = (
            'yes' == $mainTaskLogItem->task->time_limit_type &&
            2 < $mainTaskLogItem->task->category
        );

        $isNotFixedOrUrgentTask = in_array($mainTaskLogItem->task->time_limit_type, ['no', 'urgent']);

        return (
            $category == $mainTaskLogItem->task->category &&
            (
                $isNotFixedOrUrgentTask ||
                $isLowPriorityFixedDateTask
            )
        );
    }

    /**
     * @param $code
     * @param $category
     */
    public function check_214d0_214d4($code, $category)
    {
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code' => $code]);

        $wrongActions = [];
        $rightActions = [];

        /*
         * @var $groupedLog:
         * array [
         * 'parent'
         * 'grandparent'
         * 'category'
         * 'start'
         * 'end'
         * ]
         * */
        $logs = $this->logActivityActionsAggregatedGroupByParent;
        $alreadyAssessedParentCode = [];

        foreach ($logs as $taskLogItemToCheck) {
            if ($taskLogItemToCheck['category'] != $category) {
                continue;
            }
            if (in_array($taskLogItemToCheck['parent'], $alreadyAssessedParentCode)) {
                continue;
            }

            $data = [];
                foreach ($logs  as $taskLogItem) {
                    if ($taskLogItemToCheck['available'] <= $taskLogItemToCheck['start']) {
                        if (false == in_array($taskLogItem['parent'], $alreadyAssessedParentCode)
                            && $taskLogItem['category'] != '2_min'
                            && $taskLogItemToCheck['category'] < $taskLogItem['category']
                            && $taskLogItem['start'] < $taskLogItemToCheck['start']
                            && $taskLogItemToCheck['available'] < $taskLogItem['start']
                            && false == in_array($taskLogItem['parent'], $data)
                            && false == $taskLogItem['keepLastCategoryAfter60sec']
                            ) {
                            $data[] = $taskLogItem['parent'];
                            break;
                        }
                    } else {
                        break;
                    }
                }

                // findLessImportantTaskLogsBefore }
                if (0 < count($data)) {
                    if (false == in_array($taskLogItemToCheck['parent'], $alreadyAssessedParentCode)) {
                        $wrongActions[] = $taskLogItemToCheck;

                        $alreadyAssessedParentCode[] = $taskLogItemToCheck['parent'];
                    }
                } else {
                    if (false == in_array($taskLogItemToCheck['parent'], $alreadyAssessedParentCode)) {
                        $rightActions[] = $taskLogItemToCheck;

                        $alreadyAssessedParentCode[] = $taskLogItemToCheck['parent'];
                    }
                }
        }

        foreach ($rightActions as $rightAction) {
            $assessment                       = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id    = $behaviour->id;
            $assessment->sim_id               = $this->simulation->id;
            $assessment->activity_parent_code = $rightAction['parent'];
            $assessment->type_scale           = HeroBehaviour::TYPE_ID_POSITIVE;
            $assessment->value                = 1;
            $assessment->save();
        }

        foreach ($wrongActions as $wrongAction) {
            $assessment                       = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id    = $behaviour->id;
            $assessment->sim_id               = $this->simulation->id;
            $assessment->activity_parent_code = $wrongAction['parent'];
            $assessment->type_scale           = HeroBehaviour::TYPE_ID_NEGATIVE;
            $assessment->value                = 0;
            $assessment->save();
        }

        if (0 == (count($rightActions) +  count($wrongActions))) {
            $rate = 0;
        } else {
            $rate = count($rightActions) / (count($rightActions) +  count($wrongActions));
        }


        $value = $behaviour->scale * $rate;

        $assessmentCalculation           = new AssessmentCalculation();
        $assessmentCalculation->sim_id   = $this->simulation->id;
        $assessmentCalculation->point_id = $behaviour->id;
        $assessmentCalculation->value    = round($value, 2);
        $assessmentCalculation->save();
    }

    /**
     * @param string $code
     * @param int $category
     * @param array $categories
     */
    public function check_214d5_6_8($code, $category = 0, $wrongCategoryIds = [4,5])
    {
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code' => $code]);

        $wrongActions = [];

        /*
         * @var $groupedLog:
         * array [
         * 'parent'
         * 'grandparent'
         * 'category'
         * 'start'
         * 'end'
         * ]
         * */
        $logs = $this->logActivityActionsAggregatedGroupByParent;

        foreach ($logs as $taskLogItemToCheck) {
            if (false == in_array($taskLogItemToCheck['category'], $wrongCategoryIds )) {
                continue;
            }

            $data = [];
            $wellPlannedParents= [];
            foreach ($logs  as $taskLogItem) {
                if ($taskLogItemToCheck['start'] < $taskLogItem['available']) {
                    continue;
                }
                if ($taskLogItem['category'] != $category) {
                    continue;
                }

                if($taskLogItem['keepLastCategoryAfter60sec'] === LogActivityActionAgregated::KEEP_LAST_CATEGORY_YES){
                    continue;
                }

                if ($taskLogItemToCheck['start'] < $taskLogItem['start']
                    && false == in_array($taskLogItem['parent'], $wellPlannedParents)
                ) {
                    $data[] = $taskLogItem['parent'];

                    break;
                } else {
                    $wellPlannedParents[] = $taskLogItem['parent'];
                }
            }

            // findLessImportantTaskLogsBefore }
            if (0 < count($data)) {
                $wrongActions[] = $taskLogItemToCheck;
            }
        }

        foreach ($wrongActions as $wrongAction) {
            $assessment                       = new AssessmentPlaningPoint();
            $assessment->hero_behaviour_id    = $behaviour->id;
            $assessment->sim_id               = $this->simulation->id;
            $assessment->activity_parent_code = $wrongAction['parent'];
            $assessment->value                = 1;
            $assessment->type_scale           = 2;
            $assessment->save();
        }

        $assessmentCalculation           = new AssessmentCalculation();
        $assessmentCalculation->sim_id   = $this->simulation->id;
        $assessmentCalculation->point_id = $behaviour->id;
        $assessmentCalculation->value    = $behaviour->scale * count($wrongActions);
        $assessmentCalculation->save();
    }

    public function check_214g($code, $category, $enable_categories) {

        /* @var $behaviour HeroBehaviour */
        $behaviour = $this->simulation->game_type->getHeroBehaviour(['code'=>$code]);
        $value = 0;

        $in_work = [];
        /* @var $log LogActivityActionAgregated214d */
        foreach($this->logAggregated214d as $key => $log) {
            $parent = $log->parent;
            if(in_array($log->category, $enable_categories)){
                continue;
            }

            if($category === $log->category) {

                if(false === in_array($parent, $in_work)){
                    if(false === $this->isLastParent($parent, $key)){
                        $in_work[] = $parent;
                    }else{
                        continue;
                    }
                }else{
                    if($this->isLastParent($parent, $key)){
                        $key_in_work = array_search($parent, $in_work);
                        unset($in_work[$key_in_work]);
                    }else{
                        continue;
                    }
                }
            }else{
                if(!empty($in_work)) {
                    if($log->keep_last_category_initial === LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_YES) {
                        if($log->keep_last_category_after === LogActivityActionAgregated214d::KEEP_LAST_CATEGORY_NO) {
                            $value += (float)$behaviour->scale;
                            $log_214g = new LogAssessment214g();
                            $log_214g->sim_id = $this->simulation->id;
                            $log_214g->code = $code;
                            $log_214g->parent = $parent;
                            $log_214g->start_time = $log->start_time;
                            $log_214g->save();
                        }else{
                            continue;
                        }
                    }else{
                        $value += (float)$behaviour->scale;
                        $log_214g = new LogAssessment214g();
                        $log_214g->sim_id = $this->simulation->id;
                        $log_214g->code = $code;
                        $log_214g->parent = $parent;
                        $log_214g->start_time = $log->start_time;
                        $log_214g->save();                    }
                }else{
                    continue;
                }
            }
        }

        $assessment_calculation = AssessmentCalculation::model()->findByAttributes(['sim_id' => $this->simulation->id, 'point_id' => $behaviour->id]);
        if(null === $assessment_calculation){
            $assessment_calculation = new AssessmentCalculation();
            $assessment_calculation->point_id = $behaviour->id;
            $assessment_calculation->sim_id = $this->simulation->id;
        }
        $assessment_calculation->value = $value;
        $assessment_calculation->save();
    }
}