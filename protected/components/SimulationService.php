<?php

/**
 * Сервис  по работе с симуляциями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationService
{
    /**
     * Save results of "work with emails"
     *
     * @param integer $simId
     */
    public static function saveEmailsAnalyze($simulation)
    {
        // init emails in analyzer
        $emailAnalyzer = new EmailAnalyzer($simulation);

        // 3322_3324 {
        // 3322 - add to plan right tasks
        // 3324 - add to plan wrong tasks        

        $b_3322_3324 = $emailAnalyzer->check_3322_3324();

        if (isset($b_3322_3324['3322']) &&
            isset($b_3322_3324['3322']['obj']) &&
            isset($b_3322_3324['3322']['positive']) &&
            true === $b_3322_3324['3322']['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3322 = new AssessmentCalculation();
            $emailResultsFor_3322->sim_id = $simulation->id;
            $emailResultsFor_3322->point_id = $b_3322_3324['3322']['obj']->id;
            $emailResultsFor_3322->value = $b_3322_3324['3322']['positive'];
            try {
                $emailResultsFor_3322->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }

        if (isset($b_3322_3324['3324']) &&
            isset($b_3322_3324['3324']['obj']) &&
            isset($b_3322_3324['3324']['negative']) &&
            true === $b_3322_3324['3324']['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3324 = new AssessmentCalculation();
            $emailResultsFor_3324->sim_id = $simulation->id;
            $emailResultsFor_3324->point_id = $b_3322_3324['3324']['obj']->id;
            $emailResultsFor_3324->value = $b_3322_3324['3324']['negative'];
            try {
                $emailResultsFor_3324->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        // 3322_3324 }

        //3325 - read spam {        
        $b_3325 = $emailAnalyzer->check_3325();

        if (isset($b_3325['obj']) &&
            isset($b_3325['negative']) &&
            true === $b_3325['obj'] instanceof HeroBehaviour
        ) {

            $emailResultsFor_3325 = new AssessmentCalculation();
            $emailResultsFor_3325->sim_id = $simulation->id;
            $emailResultsFor_3325->point_id = $b_3325['obj']->id;
            $emailResultsFor_3325->value = $b_3325['negative'];
            try {
                $emailResultsFor_3325->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3325 - read spam }

        //3323 - any action for 2 minutes tasks {        
        $b_3323 = $emailAnalyzer->check_3323();

        if (isset($b_3323['obj']) &&
            isset($b_3323['positive']) &&
            true === $b_3323['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3323 = new AssessmentCalculation();
            $emailResultsFor_3323->sim_id = $simulation->id;
            $emailResultsFor_3323->point_id = $b_3323['obj']->id;
            $emailResultsFor_3323->value = $b_3323['positive'];
            try {
                $emailResultsFor_3323->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3323 - any action for 2 minutes tasks }        

        //3313 - read most of not-spam emails {        
        $b_3313 = $emailAnalyzer->check_3313();

        if (isset($b_3313['obj']) &&
            isset($b_3313['positive']) &&
            true === $b_3313['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3313 = new AssessmentCalculation();
            $emailResultsFor_3313->sim_id = $simulation->id;
            $emailResultsFor_3313->point_id = $b_3313['obj']->id;
            $emailResultsFor_3313->value = $b_3313['positive'];
            try {
                $emailResultsFor_3313->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3313 - read most of not-spam emails }

        $b_3333 = $emailAnalyzer->check_3333();
        if (isset($b_3333['obj']) &&
            isset($b_3333['positive']) &&
            true === $b_3333['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3333 = new AssessmentCalculation();
            $emailResultsFor_3333->sim_id = $simulation->id;
            $emailResultsFor_3333->point_id = $b_3333['obj']->id;
            $emailResultsFor_3333->value = $b_3333['positive'];
            try {
                $emailResultsFor_3333->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }

        //3326 - write not a lot of wrong emails {
        $b_3326 = $emailAnalyzer->check_3326();
        if (isset($b_3326['obj']) &&
            isset($b_3326['positive']) &&
            true === $b_3326['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3326 = new AssessmentCalculation();
            $emailResultsFor_3326->sim_id = $simulation->id;
            $emailResultsFor_3326->point_id = $b_3326['obj']->id;
            $emailResultsFor_3326->value = $b_3326['positive'];
            try {
                $emailResultsFor_3326->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3326 - write not a lot of wrong emails }

        // 3311 {
        $b_3311 = $emailAnalyzer->check_3311();

        if (isset($b_3311['obj']) &&
            isset($b_3311['positive']) &&
            true === $b_3311['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3311 = new AssessmentCalculation();
            $emailResultsFor_3311->sim_id = $simulation->id;
            $emailResultsFor_3311->point_id = $b_3311['obj']->id;
            $emailResultsFor_3311->value = $b_3311['positive'];
            try {
                $emailResultsFor_3311->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        // 3311 }
    }

    /**
     * @param integer $simId
     * @return array of BehaviourCounter
     */
    public static function getAggregatedPoints($simId)
    {
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        // @todo: fix this relation to logHelper
        $data = $simulation->assessment_points;

        $behaviours = array();

        foreach ($data as $line) {
            $pointCode = $line->point->code;

            if (false === isset($behaviours[$pointCode])) {
                $behaviours[$pointCode] = new BehaviourCounter();
            }
            $behaviours[$pointCode]->update($line->value);
        }

        // add Point object
        foreach ($simulation->game_type->getHeroBehavours([]) as $point) {
            if (isset($behaviours[$point->code])) {
                $behaviours[$point->code]->mark = $point;
            }
        }

        return $behaviours;
    }

    /**
     * @param integer $simId
     */
    public static function saveAggregatedPoints($simId)
    {

        foreach (self::getAggregatedPoints($simId) as $agrPoint) {
            // check, is in some fantastic way such value exists in DB {
            $existAssessment = AssessmentAggregated::model()
                ->bySimId($simId)
                ->byPoint($agrPoint->mark->id)
                ->find();
            // check, if in some fantastic way such value exists in DB }

            // init Log record {
            if (null == $existAssessment) {
                $existAssessment = new AssessmentAggregated();
                $existAssessment->sim_id = $simId;
                $existAssessment->point_id = $agrPoint->mark->id;
            } else {
                continue; // assessment has been saved
            }
            // init Log record }

            // set value
            $existAssessment->value = $agrPoint->getValue();
            if ($agrPoint->mark->isNegative() && 0 < $existAssessment->value) {
                // fix for negative points
                $existAssessment->value = -$existAssessment->value;
            }

            $existAssessment->save();
        }
    }

    /**
     * @param integer $simId
     */
    public static function copyMailInboxOutboxScoreToAssessmentAggregated($simId)
    {
        // add mail inbox/outbox points
        foreach (AssessmentCalculation::model()->bySimulation($simId)->findAll() as $emailBehaviour) {

            $assessment = AssessmentAggregated::model()
                ->bySimId($simId)
                ->byPoint($emailBehaviour->point_id)
                ->find();
            // check, if in some fantastic way such value exists in DB }

            // init Log record {
            if (null == $assessment) {
                $assessment = new AssessmentAggregated();
                $assessment->sim_id = $simId;
                $assessment->point_id = $emailBehaviour->point_id;
            }

            $assessment->value = $emailBehaviour->value;
            $assessment->save();
        }
    }

    /**
     * must be called at once, when simulation starts
     * @param Simulation $simulation
     * @internal param int $simulationId
     */
    public static function fillTodo(Simulation $simulation)
    {
        // add P17 - презентация ген. директору
        /** @var $tasks Task[] */
        $tasks = $simulation->game_type->getTasks(['start_type'=> 'start', "time_limit_type" => 'can\'t be moved']);
        foreach ($tasks as $task) {
            $dayplanFixed = new DayPlan();
            $dayplanFixed->date = $task->start_time;
            $dayplanFixed->task_id = $task->getPrimaryKey();
            $dayplanFixed->sim_id = $simulation->getPrimaryKey();
            $dayplanFixed->day = 1; # FIXME hardcode
            $dayplanFixed->save();
        }

        // прочие задачи
        $tasks = $simulation->game_type->getTasks(['start_type' => 'start']);
        if ($tasks) {
            $sql = "INSERT INTO todo (sim_id, adding_date, task_id) VALUES ";

            $add = '';
            foreach ($tasks as $task) {
                if ($task->code === 'P017')
                    continue;
                $sql .= $add . "({$simulation->id}, NOW(), {$task->id})";
                $add = ',';
            }
            $sql .= ";";

            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $command->execute();
        }
    }

    /**
     * Fills executed performance rules according to user actions
     * @param Simulation $simulation
     */
    public static function setFinishedPerformanceRules(Simulation $simulation)
    {
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simulation->getPrimaryKey());
        $allRules = $simulation->game_type->getPerformanceRules([]);
        $done = [];

        /** @var $rule PerformanceRule */
        foreach ($allRules as $rule) {
            if (isset($done[$rule->id])) {
                continue;
            }

            $conditions = $rule->performanceRuleConditions;
            foreach ($conditions as $condition) {
                $satisfies = false;
                if ($condition->replica_id) {
                    /** @var Replica $replica */
                    $replica = Replica::model()->findByPk($condition->replica_id);

                    $satisfies = LogDialog::model()
                        ->bySimulationId($simulation->id)
                        ->byLastReplicaId($replica->excel_id)
                        ->exists();

                } elseif ($condition->mail_id) {
                    /** @var MailBox $mail */
                    $mail = MailBox::model()->findByAttributes([
                        'sim_id' => $simulation->id,
                        'template_id' => $condition->mail_id
                    ]);

                    $satisfies = $mail ?
                        LogMail::model()
                            ->bySimId($simulation->id)
                            ->byMailBoxId($mail->id)
                            ->exists() :
                        false;
                } elseif ($condition->excel_formula_id) {

                    $satisfies = SimulationExcelPoint::model()
                        ->bySimulation($simulation->id)
                        ->byFormula($condition->excel_formula_id)
                        ->byExistsValue()
                        ->exists();

                }

                if ($rule->operation === 'AND' && !$satisfies ||
                    $rule->operation === 'OR' && $satisfies
                ) {
                    break;
                }

            }

            if (!empty($satisfies)) {
                $point = new PerformancePoint();
                $point->sim_id = $simulation->id;
                $point->performance_rule_id = $rule->id;
                $point->save();

                $done[$rule->id] = $rule->value;
            }
        }
    }

    public static function calculatePerformanceRate(Simulation $simulation)
    {
        $maxRates = MaxRate::model()->findAllByAttributes(
            [
                'scenario_id' => $simulation->scenario_id,
                'type' => MaxRate::TYPE_SUCCESS
            ],
            'performance_rule_category_id IS NOT NULL'
        );

        $categoryRates = [];
        foreach ($maxRates as $rate) {
            $categoryRates[$rate->performance_rule_category_id] = $rate->rate;
        }

        $categories = [];
        foreach ($simulation->performance_points as $point) {
            $rule = $point->performanceRule;
            if (empty($categories[$rule->category_id])) {
                $categories[$rule->category_id] = 0;
            }
            $categories[$rule->category_id] += $rule->value;
        }

        foreach ($categories as $cid => $value) {
            $row = new PerformanceAggregated();
            $row->sim_id = $simulation->id;
            $row->category_id = $cid;
            $row->value = $value;
            $row->percent = round($value / $categoryRates[$cid] * 100);

            $row->save();
        }
    }

    /**
     * Fills gained stress rules according to user actions
     * @param Simulation $simulation
     */
    public static function setGainedStressRules($simulation)
    {
        $allRules = $simulation->game_type->getStressRules([]);
        $done = [];

        /** @var $rule StressRule */
        foreach ($allRules as $rule) {
            if (isset($done[$rule->id])) {
                continue;
            }

            $satisfies = false;
            if ($rule->replica_id) {
                $satisfies = !!LogReplica::model()->findByAttributes([
                    'sim_id' => $simulation->id,
                    'replica_id' => $rule->replica_id
                ]);

            } elseif ($rule->mail_id) {
                /** @var MailBox $mail */
                $mail = MailBox::model()->findByAttributes([
                    'sim_id' => $simulation->id,
                    'template_id' => $rule->mail_id
                ]);

                $satisfies = $mail ?
                    LogMail::model()
                        ->bySimId($simulation->id)
                        ->byMailBoxId($mail->id)
                        ->exists() :
                    false;
            }

            if (!empty($satisfies)) {
                $point = new StressPoint();
                $point->sim_id = $simulation->id;
                $point->stress_rule_id = $rule->id;
                $point->save();

                $done[$rule->id] = $rule->value;
            }
        }
    }

    /**
     * @param Simulation $simulation
     *
     * @return array of EventTrigger
     */
    public static function initEventTriggers($simulation)
    {
        $events = EventSample::model()
            ->byNotDocumentCode()
            ->byNotPlanTaskCode()
            ->byNotSentTodayEmailCode()
            ->byNotSentYesterdayEmailCode()
            ->byNotTerminatorCode()
            ->findAllByAttributes(['scenario_id' => $simulation->game_type->getPrimaryKey()]);

        $sql = "INSERT INTO events_triggers (sim_id, event_id, trigger_time) VALUES ";

        $add = '';
        foreach ($events as $event) {
            $eventTime = $event->trigger_time ?: '00:00:00';
            $sql .= $add . "({$simulation->id}, {$event->id}, '$eventTime')";
            $add = ',';
        }
        $sql .= ";";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();

        return EventTrigger::model()->findAllByAttributes(['sim_id' => $simulation->id]);
    }

    /**
     * @param $simulationMode
     * @param string $type
     * @param Invite $invite
     * @throws Exception
     * @internal param $simulationType
     * @return Simulation
     */
    public static function simulationStart($invite, $simulationMode)
    {
        if (Simulation::MODE_DEVELOPER_LABEL == $simulationMode
            && false == $invite->receiverUser->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)
        ) {
            throw new Exception('У вас нет прав для старта этой симуляции');
        }

        // TODO: Change checking logic
        if ($invite->scenario->slug == Scenario::TYPE_FULL
            && false == $invite->receiverUser->can(UserService::CAN_START_FULL_SIMULATION)
        ) {
            throw new Exception('У вас нет прав для старта этой симуляции');
        }

        // Создаем новую симуляцию
        $simulation = new Simulation();
        $simulation->user_id = $invite->receiverUser->id;
        $simulation->start = GameTime::setNowDateTime();
        $simulation->mode = Simulation::MODE_DEVELOPER_LABEL === $simulationMode ? Simulation::MODE_DEVELOPER_ID : Simulation::MODE_PROMO_ID;
        $simulation->scenario_id = Scenario::model()->findByAttributes(['slug' => $invite->scenario->slug])->primaryKey;
        $simulation->save();

        // save simulation ID to user session
        Yii::app()->session['simulation'] = $simulation->id;

        //@todo: increase speed
        SimulationService::initEventTriggers($simulation);

        // предустановка задач в todo!
        SimulationService::fillTodo($simulation);

        // скопируем документы
        MyDocumentsService::init($simulation);

        // @todo: increase speed
        // Установим дефолтовые значения для mail client
        $mailSettings = new MailSettings();
        $mailSettings->sim_id = $simulation->id;
        $mailSettings->insert();

        // Copy email templates
        MailBoxService::initMailBoxEmails($simulation->id);

        ZohoDocuments::copyExcelFiles($simulation->id);
        // проставим дефолтовые значени флагов для симуляции пользователя
        $flags = Flag::model()->findAll();
        foreach ($flags as $flag) {
            FlagsService::setFlag($simulation, $flag->code, 0);
        }

        // update invite if it set
        // in cheat mode invite has no ID
        if (null !== $invite && null != $invite->id) {
            $invite->simulation_id = $simulation->id;
            $invite->status = Invite::STATUS_STARTED;
            $invite->save(false, ['simulation_id', 'status']);
        }

        return $simulation;
    }

    /**
     * @param Simulation $simulation
     * @param array $logs_src
     */
    public static function simulationStop($simulation, $logs_src = array())
    {
        // Remove pause if it was set
        self::resume($simulation);

        // данные для логирования
        EventsManager::processLogs($simulation, $logs_src);

        // Make agregated activity log 
        LogHelper::combineLogActivityAgregated($simulation);

        // make attestation 'work with emails' 
        SimulationService::saveEmailsAnalyze($simulation);

        DayPlanService::copyPlanToLog($simulation, 18 * 60, DayPlanLog::ON_18_00); // 18-00 copy

        $plan = new PlanAnalyzer($simulation);
        $plan->run();

        // Save score for "1. Оценка ALL_DIAL"+"8. Оценка Mail Matrix"
        // see Assessment scheme_v5.pdf



        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $CheckConsolidatedBudget->calcPoints();

        SimulationService::setFinishedPerformanceRules($simulation);
        SimulationService::calculatePerformanceRate($simulation);

        SimulationService::setGainedStressRules($simulation);
        SimulationService::saveAggregatedPoints($simulation->id);

        // @todo: this is trick
        // write all mail outbox/inbox scores to AssessmentAggregate directly
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        $simulation->end = GameTime::setNowDateTime();
        $simulation->save();
        $simulation->checkLogs();

        self::applyReductionFactors($simulation);
        $learning_area = new LearningAreaAnalizer($simulation);
        $learning_area->run();
        $evaluation = new Evaluation($simulation);
        $evaluation->run();

    }

    /**
     * @param Simulation $simulation
     */
    public static function pause($simulation)
    {
        if (empty($simulation->paused)) {
            $simulation->paused = GameTime::setNowDateTime();
            $simulation->save();
        }
    }

    /**
     * @param Simulation $simulation
     */
    public static function resume($simulation)
    {
        if (!empty($simulation->paused)) {
            $skipped = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($simulation->paused);
            $simulation->skipped = $simulation->skipped + $skipped;
            $simulation->paused = null;
            $simulation->save();
        }
    }


    /**
     * WTF! This crazy code not change internal sim time? but change sim start value
     * in real life time coords
     *
     * There are no internal simulation time stored anywhere :)
     *
     * @param Simulation $simulation
     * @param integer $newHours
     * @param integer $newMinutes
     */
    public static function setSimulationClockTime($simulation, $newHours, $newMinutes)
    {
        $speedFactor = Yii::app()->params['public']['skiliksSpeedFactor'];

        $variance = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($simulation->start);
        $variance = $variance * $speedFactor;

        $unixtimeMins = round($variance / 60);
        $start_time = explode(':', $simulation->game_type->start_time);
        $clockH = round($unixtimeMins / 60);
        $clockM = $unixtimeMins - ($clockH * 60);
        $clockH = $clockH + $start_time[0];
        $clockM = $clockM + $start_time[1];

        $simulation->start = GameTime::setUnixDateTime((GameTime::getUnixDateTime($simulation->start) - (($newHours - $clockH) * 60 * 60 / $speedFactor)
            - (($newMinutes - $clockM) * 60 / $speedFactor)));

        $simulation->save();
    }

    /**
     * @wiki: https://maprofi.atlassian.net/wiki/pages/editpage.action?pageId=11174012
     * @param Simulation $simulation
     */
    public static function applyReductionFactors(Simulation $simulation)
    {
        // we will calculate K based om MAX negative value for learning goals
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(['type' => MaxRate::TYPE_FAIL]);
        $criteria->addCondition('learning_goal_id IS NOT NULL');
        $maxRates = $simulation->game_type->getMaxRates($criteria);

        $learningGoalsForUpdate = [];
        $rateValues = [];
        foreach ($maxRates as $rate) {
            $learningGoalsForUpdate[] = $rate->learningGoal;
            $rateValues[$rate->learning_goal_id] = $rate->rate;
        }

        // to access goals by code
        $learningGoals = [];

        // init "0" sum for learning goals, find ids of negative behaviours {
        $learningGoalsForUpdateCodes = [];
        $sum = [];  // learningGoalsForUpdateNegativeScaleSum
        foreach ($learningGoalsForUpdate as $learningGoalForUpdate) {
            $learningGoals[$learningGoalForUpdate->getPrimaryKey()] = $learningGoalForUpdate;

            $learningGoalsForUpdateCodes[] = $learningGoalForUpdate->code;
            $sum[$learningGoalForUpdate->getPrimaryKey()] = 0;
        }

        $negativeLearningGoalCriteria = new CDbCriteria();
        $negativeLearningGoalCriteria->addInCondition('code', $learningGoalsForUpdateCodes);
        $learningGoalsForUpdateIds = $simulation->game_type->getLearningGoals($negativeLearningGoalCriteria);
        $negativeHeroBehavioursCriteria = new CDbCriteria();
        $negativeHeroBehavioursCriteria->addInCondition('learning_goal_id', array_map(function ($i) {return $i->id;}, $learningGoalsForUpdateIds));
        $negativeHeroBehavioursCriteria->compare('type_scale', HeroBehaviour::TYPE_NEGATIVE);
        $negativeHeroBehaviours = HeroBehaviour::model()->findAll($negativeHeroBehavioursCriteria);

        $heroBehavioursForUpdateCodes = [];
        foreach ($negativeHeroBehaviours as $heroBehaviour) {
            $heroBehavioursForUpdateCodes[] = $heroBehaviour->code;
        }
        // init "0" sum for learning goals, find ids of negative behaviours }

        // calculate total negative sum for learning goals {
        foreach ($simulation->assessment_aggregated as $assessment) {
            if (in_array($assessment->point->code, $heroBehavioursForUpdateCodes) &&
                isset($sum[$assessment->point->learning_goal_id])) {

                $sum[$assessment->point->learning_goal_id] += $assessment->value;
            }
        }
        // calculate total negative sum for learning goals }

        // calculate coefficients {
        foreach ($sum as $learningGoalId => $sumValue) {
            $realK = $sumValue/$rateValues[$learningGoalId];

            $k[$learningGoalId] = 1;

            if ($realK <= 0.1) {
                $k[$learningGoalId] = 1;
            } elseif (0.1 < $realK && $realK <= 0.5) {
                $k[$learningGoalId] = 0.5;
            } elseif (0.5 < $realK) {
                $k[$learningGoalId] = 0;
            }
        }
        // calculate coefficients }

        // update assessment on positive scale {
        foreach ($simulation->assessment_aggregated as $assessment) {
            if (isset($k[$assessment->point->learning_goal_id]) &&
                $assessment->point->type_scale == HeroBehaviour::TYPE_POSITIVE
            ) {
                $assessment->coefficient_for_fixed_value = $k[$assessment->point->learning_goal_id];
                $assessment->fixed_value = $assessment->coefficient_for_fixed_value * $assessment->value;
            } else {
                $assessment->fixed_value = $assessment->value;
            }
            $assessment->save();
        }
        // update assessment on positive scale }
    }
}
