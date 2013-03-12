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
    public static function saveEmailsAnalyze($simId)
    {
        // init emails in analyzer
        $emailAnalyzer = new EmailAnalizer($simId);

        // 3322_3324 {
        // 3322 - add to plan right tasks
        // 3324 - add to plan wrong tasks        

        $b_3322_3324 = $emailAnalyzer->check_3322_3324();

        if (isset($b_3322_3324['3322']) &&
            isset($b_3322_3324['3322']['obj']) &&
            isset($b_3322_3324['3322']['positive']) &&
            true === $b_3322_3324['3322']['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3322 = new SimulationMailPoint();
            $emailResultsFor_3322->sim_id = $simId;
            $emailResultsFor_3322->point_id = $b_3322_3324['3322']['obj']->id;
            $emailResultsFor_3322->scale_type_id = $b_3322_3324['3322']['obj']->type_scale;
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
            $emailResultsFor_3324 = new SimulationMailPoint();
            $emailResultsFor_3324->sim_id = $simId;
            $emailResultsFor_3324->point_id = $b_3322_3324['3324']['obj']->id;
            $emailResultsFor_3324->scale_type_id = $b_3322_3324['3324']['obj']->type_scale;
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

            $emailResultsFor_3325 = new SimulationMailPoint();
            $emailResultsFor_3325->sim_id = $simId;
            $emailResultsFor_3325->point_id = $b_3325['obj']->id;
            $emailResultsFor_3325->scale_type_id = $b_3325['obj']->type_scale;
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
            $emailResultsFor_3323 = new SimulationMailPoint();
            $emailResultsFor_3323->sim_id = $simId;
            $emailResultsFor_3323->point_id = $b_3323['obj']->id;
            $emailResultsFor_3323->scale_type_id = $b_3323['obj']->type_scale;
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
            $emailResultsFor_3313 = new SimulationMailPoint();
            $emailResultsFor_3313->sim_id = $simId;
            $emailResultsFor_3313->point_id = $b_3313['obj']->id;
            $emailResultsFor_3313->scale_type_id = $b_3313['obj']->type_scale;
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
            $emailResultsFor_3333 = new SimulationMailPoint();
            $emailResultsFor_3333->sim_id = $simId;
            $emailResultsFor_3333->point_id = $b_3333['obj']->id;
            $emailResultsFor_3333->scale_type_id = $b_3333['obj']->type_scale;
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
            $emailResultsFor_3326 = new SimulationMailPoint();
            $emailResultsFor_3326->sim_id = $simId;
            $emailResultsFor_3326->point_id = $b_3326['obj']->id;
            $emailResultsFor_3326->scale_type_id = $b_3326['obj']->type_scale;
            $emailResultsFor_3326->value = $b_3326['positive'];
            try {
                $emailResultsFor_3326->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3326 - write not a lot of wrong emails }
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
        $data = $simulation->getAssessmentPointDetails();

        $behaviours = array();

        foreach ($data as $line) {
            if (is_array($line)) {

                $pointCode = $line['code'];
                $add_value = $line['add_value'];
            } else if ($line instanceof AssessmentDetail) {
                $pointCode = $line->point->code;
                $add_value = $line->getReplicaPoint()->add_value;
            } else {
                $pointCode = $line->point->code;
                $add_value = $line->value;
            }
            if (false === isset($behaviours[$pointCode])) {
                $behaviours[$pointCode] = new BehaviourCounter();
            }

            $behaviours[$pointCode]->update($add_value);

        }

        // add Point object
        foreach (HeroBehaviour::model()->findAll() as $point) {
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
        foreach (SimulationMailPoint::model()->bySimulation($simId)->findAll() as $emailBehaviour) {

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
     * @param integer $simulationId
     */
    public static function fillTodo($simulation)
    {
        // add P17 - презентация ген. директору
        $task = Task::model()->byStartType('start')->find(" code = 'P017' ");
        $sql = "INSERT INTO day_plan (sim_id, date, day, task_id) VALUES
        ({$simulation->id}, '16:00:00',1, {$task->id});
        ";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();

        // прочие задачи
        $tasks = Task::model()->byStartType('start')->findAll(" code != 'P017' ");
        $sql = "INSERT INTO todo (sim_id, adding_date, task_id) VALUES ";

        $add = '';
        foreach ($tasks as $task) {
            $sql .= $add . "({$simulation->id}, '00:00:00', {$task->id})";
            $add = ',';
        }
        $sql .= ";";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();
    }

    /**
     * Fills executed assessment rules according to user actions
     * @param int $simId
     */
    public static function setFinishedAssessmentRules($simId)
    {
        $allRules = AssessmentRule::model()->findAll();
        $done = [];

        /** @var $rule AssessmentRule */
        foreach ($allRules as $rule) {
            if (isset($done[$rule->id])) {
                continue;
            }

            $conditions = $rule->assessmentRuleConditions;
            foreach ($conditions as $condition) {
                $satisfies = false;
                if ($condition->dialog_id) {
                    /** @var Replica $dialog */
                    $dialog = Replica::model()->findByPk($condition->dialog_id);

                    $satisfies = LogDialog::model()
                        ->bySimulationId($simId)
                        ->byLastReplicaId($dialog->excel_id)
                        ->exists();

                } elseif ($condition->mail_id) {
                    /** @var MailBox $mail */
                    $mail = MailBox::model()->findByAttributes([
                        'sim_id' => $simId,
                        'template_id' => $condition->mail_id
                    ]);

                    $satisfies = $mail ?
                        LogMail::model()
                            ->bySimId($simId)
                            ->byMailBoxId($mail->id)
                            ->exists() :
                        false;
                }

                if ($rule->operation === 'AND' && $satisfies ||
                    ($rule->operation === 'OR') && !$satisfies
                ) {
                    continue;
                }
            }

            if (!empty($satisfies)) {
                $sar = new SimulationAssessmentRule();
                $sar->sim_id = $simId;
                $sar->assessment_rule_id = $rule->id;
                $sar->save();

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
            ->byTriggerTimeGreaterThanZero()
            ->findAll();

        $sql = "INSERT INTO events_triggers (sim_id, event_id, trigger_time) VALUES ";

        $add = '';
        foreach ($events as $event) {
            $sql .= $add . "({$simulation->id}, {$event->id}, '{$event->trigger_time}')";
            $add = ',';
        }
        $sql .= ";";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();

        return EventTrigger::model()->findAllByAttributes(['sim_id' => $simulation->id]);
    }

    /**
     * @param $simulationType
     * @param Users $user
     * @return Simulation
     * @throws Exception
     */
    public static function simulationStart($simulationMode, $user = null)
    {
        $profiler = new SimpleProfiler(false);
        $profiler->startTimer();

        if ($user === null) {
            $userId = SessionHelper::getUidBySid();
        } else {
            $userId = $user->primaryKey;
        }

        if (null === $userId) {
            return null;
        }

        $profiler->render('1: ');

        if (Simulation::MODE_DEVELOPER_LABEL == $simulationMode
            && false == $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)
        ) {
            throw new Exception('У вас нет прав для старта этой симуляции');
        }
        $profiler->render('2: ');

        // Создаем новую симуляцию
        $simulation = new Simulation();
        $simulation->user_id = $userId;
        $simulation->status = 1;
        $simulation->start = GameTime::setNowDateTime();
        $simulation->difficulty = 1;
        $simulation->type = $simulationMode;
        $simulation->insert();
        $profiler->render('3: ');

        // save simulation ID to user session
        Yii::app()->session['simulation'] = $simulation->id;
        $profiler->render('4: ');

        //@todo: increase speed
        SimulationService::initEventTriggers($simulation);
        $profiler->render('5: '); // 3.10 ~ 3.17

        // предустановка задач в todo!
        SimulationService::fillTodo($simulation);
        $profiler->render('6: ');

        // скопируем документы
        MyDocumentsService::init($simulation);
        $profiler->render('7: ');

        // @todo: increase speed
        // Установим дефолтовые значения для mail client
        $mailSettings = new MailSettings();
        $mailSettings->sim_id = $simulation->id;
        $mailSettings->insert();
        $profiler->render('8: ');

        // Copy email templates
        MailBoxService::initMailBoxEmails($simulation->id);
        $profiler->render('9: '); // 3.51 ~ 4.14

        // проставим дефолтовые значени флагов для симуляции пользователя
        $flags = Flag::model()->findAll();
        foreach ($flags as $flag) {
            FlagsService::setFlag($simulation, $flag->code, 0);
        }

        return $simulation;
    }

    /**
     * @param Simulation $simulation
     */
    public static function simulationStop($simulation, $logs_src = array())
    {
        // данные для логирования
        EventsManager::processLogs($simulation, $logs_src);

        // Make agregated activity log 
        LogHelper::combineLogActivityAgregated($simulation);

        // make attestation 'work with emails' 
        SimulationService::saveEmailsAnalyze($simulation->id);

        // Save score for "1. Оценка ALL_DIAL"+"8. Оценка Mail Matrix"
        // see Assessment scheme_v5.pdf
        SimulationService::saveAggregatedPoints($simulation->id);

        SimulationService::setFinishedAssessmentRules($simulation->id);

        DayPlanService::copyPlanToLog($simulation, 18 * 60); // 18-00 copy

        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        $CheckConsolidatedBudget->calcPoints();

        // @todo: this is trick
        // write all mail outbox/inbox scores to AssessmentAggregate directly
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        $simulation->end = GameTime::setNowDateTime();
        $simulation->save();
        $simulation->checkLogs();
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
        $start_time = explode(':', Yii::app()->params['public']['simulationStartTime']);
        $clockH = round($unixtimeMins / 60);
        $clockM = $unixtimeMins - ($clockH * 60);
        $clockH = $clockH + $start_time[0];
        $clockM = $clockM + $start_time[1];

        $simulation->start = GameTime::setUnixDateTime((GameTime::getUnixDateTime($simulation->start) - (($newHours - $clockH) * 60 * 60 / $speedFactor)
            - (($newMinutes - $clockM) * 60 / $speedFactor)));

        $simulation->save();
    }
}
