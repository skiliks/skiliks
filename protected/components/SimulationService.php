<?php
use application\components\Logging\LogTableList as LogTableList;
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

        // 3332 {
        $b_3332 = $emailAnalyzer->check_3332();

        if (isset($b_3332['obj']) &&
            isset($b_3332['positive']) &&
            true === $b_3332['obj'] instanceof HeroBehaviour
        ) {
            $emailResultsFor_3332 = new AssessmentCalculation();
            $emailResultsFor_3332->sim_id = $simulation->id;
            $emailResultsFor_3332->point_id = $b_3332['obj']->id;
            $emailResultsFor_3332->value = $b_3332['positive'];
            try {
                $emailResultsFor_3332->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        // 3332 }
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
            $existAssessment = AssessmentAggregated::model()->findByAttributes([
                'sim_id'   => $simId,
                'point_id' => $agrPoint->mark->id
            ]);
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
        foreach (AssessmentCalculation::model()->findAllByAttributes(['sim_id' => $simId]) as $emailBehaviour) {

            $assessment = AssessmentAggregated::model()->findByAttributes([
                'sim_id'   => $simId,
                'point_id' => $emailBehaviour->point_id
            ]);
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
        /** @var Task[] $tasks */
        $tasks = $simulation->game_type->getTasks(['start_type'=> 'start']);
        foreach ($tasks as $task) {
            $dayPlan = new DayPlan();
            $dayPlan->task_id = $task->getPrimaryKey();
            $dayPlan->sim_id = $simulation->getPrimaryKey();

            if ($task->is_cant_be_moved) {
                $dayPlan->day = DayPlan::DAY_1;
                $dayPlan->date = $task->start_time;
            } else {
                $dayPlan->day = DayPlan::DAY_TODO;
            }

            $dayPlan->save();
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

                    $satisfies = SimulationExcelPoint::model()->exists(
                        ' sim_id = :sim_id AND formula_id = :formula_id AND value != 0 ',
                        [
                            'sim_id' => $simulation->id,
                            'formula_id' => $condition->excel_formula_id
                        ]
                    );
                }

                if ($rule->operation === 'AND' && !$satisfies ||
                    $rule->operation === 'OR' && $satisfies ||
                    $rule->operation === '-' && $satisfies
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
        $is40or41ruleUsed = false;

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

            // hack for OR condition in 40/41 rules

            if (40 == $rule->code || 41 == $rule->code) {
                if ($is40or41ruleUsed) {
                    $rule->value = 0;
                } else {
                    $is40or41ruleUsed = true;
                }
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
     * @return EventTrigger[]
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

        if (count($events) > 0) {
            $sql = [];
            foreach ($events as $event) {
                $eventTime = $event->trigger_time ?: '00:00:00';
                $sql[] = "({$simulation->id}, {$event->id}, '$eventTime')";
            }

            $sql = sprintf(
                'INSERT INTO events_triggers (sim_id, event_id, trigger_time) VALUES %s;',
                implode(',', $sql)
            );

            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $command->execute();
        }

        return EventTrigger::model()->findAllByAttributes(['sim_id' => $simulation->id]);
    }

    /**
     * @param Invite $invite
     * @param $simulationMode
     * @throws Exception
     * @return Simulation
     */
    public static function simulationStart($invite, $simulationMode, $simulationType = null)
    {
        if ($simulationMode === Simulation::MODE_DEVELOPER_LABEL) {
            if ($invite->receiverUser->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
                $user = $invite->receiverUser;
                $scenario = Scenario::model()->findByAttributes(['slug' => $invite->scenario->slug]);
                $invite->owner_id = $user->id;
                $invite->receiver_id = $user->id;
                $invite->firstname = $user->profile->firstname;
                $invite->lastname = $user->profile->lastname;
                $invite->scenario_id = $scenario->id;
                $invite->status = Invite::STATUS_ACCEPTED;
                $invite->sent_time = time(); // @fix DB!
                $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
                $invite->save(true, [
                    'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
                ]);

                $invite->email = strtolower($user->profile->email);
                $invite->save(false);
                InviteService::logAboutInviteStatus($invite, 'invite : update sim_id (1) : sim start');
            } else {
                    throw new Exception('У вас нет прав для старта этой симуляции');
            }
        }


        // TODO: Change checking logic
        if ($invite->scenario->isFull() && false == $invite->canUserSimulationStart()
        ) {
            throw new Exception('У вас нет прав для старта этой симуляции');
        }

        if (null === $simulationType) {
            $simulationType = $invite->scenario->slug;
        }

        if ($invite->scenario->isFull() && $simulationType == Scenario::TYPE_TUTORIAL) {
            $scenarioType = Scenario::TYPE_TUTORIAL;
        } else {
            $scenarioType = $invite->scenario->slug;
        }

        // Создаем новую симуляцию
        $simulation = new Simulation();
        $simulation->user_id = $invite->receiverUser->id;
        $simulation->start = GameTime::setNowDateTime();
        $simulation->mode = Simulation::MODE_DEVELOPER_LABEL === $simulationMode ? Simulation::MODE_DEVELOPER_ID : Simulation::MODE_PROMO_ID;
        $simulation->scenario_id = Scenario::model()->findByAttributes(['slug' => $scenarioType])->primaryKey;
        $simulation->status = Simulation::STATUS_IN_PROGRESS;
        $simulation->save();
        $_POST['simId'] = $simulation->id;
        // save simulation ID to user session
        Yii::app()->session['simulation'] = $simulation->id;

        //@todo: increase speed
        SimulationService::initEventTriggers($simulation);

        // предустановка задач в todo!
        SimulationService::fillTodo($simulation);

        // скопируем документы
        MyDocumentsService::init($simulation);

        // Copy email templates
        MailBoxService::initMailBoxEmails($simulation->id);

        // ZohoDocuments::copyExcelFiles($simulation->id);
        // проставим дефолтовые значени флагов для симуляции пользователя
        $flags = Flag::model()->findAll();
        foreach ($flags as $flag) {
            FlagsService::setFlag($simulation, $flag->code, 0);
        }

        // Put time based flags into queue
        FlagsService::copyTimeFlagsToQueue($simulation);

        // update invite if it set
        // in cheat mode invite has no ID
        if (null !== $invite && null != $invite->id) {
            $invite->status = Invite::STATUS_IN_PROGRESS;

            // Списание инвайта с коропративного аккаунта, если он начинает сам свою симуляцию
            // не в dev режиме
            if ($invite->ownerUser->isCorporate()
                && Scenario::TYPE_TUTORIAL == $scenarioType
                && $simulationMode != Simulation::MODE_DEVELOPER_LABEL
                && $invite->ownerUser->id == $invite->receiverUser->id) {
                $invite->ownerUser->getAccount()->invites_limit--;
                $invite->ownerUser->getAccount()->save(false);
            }

            $invite->update();
            if(InviteService::isSimulationOverrideDetected($invite)){
                /* @var $sim Simulation */
                // повторный старт!
                if(null !== $invite->simulation){
                    $invite->simulation->status = Simulation::STATUS_INTERRUPTED;
                    $invite->simulation->save();

                    InviteService::logAboutInviteStatus($invite, 'Set sum id to null from '.$invite->simulation->id);
                    $invite->simulation = null;
                    $invite->simulation_id = null;
                    $invite->save();
                }
            }
            $invite->simulation_id = $simulation->id;
            $scenario = Scenario::model()->findByPk($invite->scenario_id);
            /* @var $scenario Scenario */
            if($scenario->isLite()) {
                $invite->status = Invite::STATUS_IN_PROGRESS;
                $invite->save(false, ['simulation_id', 'status']);
                InviteService::logAboutInviteStatus($invite, 'invite : update sim_id (2) : sim start');
            } else {
                $invite->save(false, ['simulation_id']);
                InviteService::logAboutInviteStatus($invite, 'invite : update sim_id (3) : sim start');
            }

        }

        self::logAboutSim($simulation, 'sim start: done');

        return $simulation;
    }

    /**
     * @param Simulation $simulation
     * @param array $logs_src
     */
    public static function simulationStop($simulation, $logs_src = array(), $manual=false)
    {
        self::logAboutSim($simulation, 'sim stop: begin');

        // Check if simulation was already stopped
        if (null !== $simulation->end && false === $manual) {
            return;
        }

        if (Simulation::STATUS_INTERRUPTED == $simulation->status) {
            return;
        }

        // If simulation was started by invite, mark it as completed
        if (null !== $simulation->invite && $simulation->isTutorial() === false) {
            $simulation->invite->status = Invite::STATUS_COMPLETED;
            $simulation->invite->save(false);
            InviteService::logAboutInviteStatus($simulation->invite, 'invite : updated : sim stop');
        }

        if (null !== $simulation->invite && $simulation->isTutorial()) {
            $simulation->invite->tutorial_finished_at = date('Y-m-d H:i:s');
            $simulation->invite->save(false);
            InviteService::logAboutInviteStatus($simulation->invite, 'invite : updated : tutorial finished');
        }

        // Remove pause if it was set
        self::resume($simulation);

        if($simulation->isCalculateTheAssessment()) {

            if ($simulation->isDevelopMode() ||
                true === Yii::app()->params['public']['isUseStrictAssertsWhenSimStop']
            ) {
                $simulation->checkLogs();
            }

            // @todo: find reason after release
            // we close last Activation log
            if (0 < count($logs_src) && 'activated' == $logs_src[count($logs_src)-1][2]) {
                $extra_log    = $logs_src[count($logs_src)-1];
                $extra_log[2] = 'deactivated';
                $logs_src[] = $extra_log;
            }

            // данные для логирования
            try {
                EventsManager::processLogs($simulation, $logs_src);
            } catch (Exception $e) {
                if ($simulation->isDevelopMode()) {
                    throw $e;
                }
            }
            if(Yii::app()->params['disableOldLogging']){
                LogHelper::updateUniversalLog($simulation);
                $analyzer = new ActivityActionAnalyzer($simulation);
                $analyzer->run();
            }
            // Make aggregated activity log
            LogHelper::combineLogActivityAgregated($simulation);

            // make attestation 'work with emails'
            SimulationService::saveEmailsAnalyze($simulation);

            DayPlanService::copyPlanToLog($simulation, 18 * 60, DayPlanLog::ON_18_00); // 18-00 copy

            $custom = new CalculateCustomAssessmentsService($simulation);
            $custom->run();

            $planAnalyzer = new PlanAnalyzer($simulation);
            $planAnalyzer->run();

            // Calculate and save Time Management assessments
            $TimeManagementAnalyzer = new TimeManagementAnalyzer($simulation);
            $TimeManagementAnalyzer->calculateAndSaveAssessments();

            // Save score for "1. Оценка ALL_DIAL"+"8. Оценка Mail Matrix"
            // see Assessment scheme_v5.pdf
            $CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
            $CheckConsolidatedBudget->calcPoints();

            SimulationService::setFinishedPerformanceRules($simulation);

            // результативность
            SimulationService::calculatePerformanceRate($simulation);

            SimulationService::setGainedStressRules($simulation);
            SimulationService::stressResistance($simulation);
            SimulationService::saveAggregatedPoints($simulation->id);

            // @todo: this is trick
            // write all mail outbox/inbox scores to AssessmentAggregate directly
            SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

            $learningGoalAnalyzer = new LearningGoalAnalyzer($simulation);
            $learningGoalAnalyzer->run();

            $learning_area = new LearningAreaAnalyzer($simulation);
            $learning_area->run();

            $evaluation = new Evaluation($simulation);
            $evaluation->run();

            $simulation->saveLogsAsExcel();

            self::logAboutSim($simulation, 'sim stop: assessment calculated');
        }

        // @ - for PHPUnit
        if (Scenario::TYPE_TUTORIAL !== $simulation->game_type->slug ||
            true == $simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
            @ Yii::app()->request->cookies['display_result_for_simulation_id'] =
                new CHttpCookie('display_result_for_simulation_id', $simulation->id);
        }

        if ($simulation->isFull()) {
            $tmpInvite = $simulation->invite;
            $tmpInvite->can_be_reloaded = false;
            $tmpInvite->save(false);
            InviteService::logAboutInviteStatus($tmpInvite, 'invite : updated : can be reloaded set to false');
            unset($tmpInvite);
            $simulation->invite->refresh();
        }

        // remove all files except D1 - Сводный бюджет 2013 {
        $docs = MyDocument::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);
        foreach ($docs as $document) {
            if ('D1' !== $document->template->code && file_exists($document->getFilePath())) {
                unlink($document->getFilePath());
            }

            // remove all files except D1 }

        }

        $simulation->end = GameTime::setNowDateTime();
        $simulation->status = Simulation::STATUS_COMPLETE;
        $simulation->save(false);
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
    public static function update($simulation, $skipped)
    {
        $simulation->skipped = $simulation->skipped + $skipped;
        $simulation->paused = null;
        $simulation->save();
    }

    /**
     * @param Simulation $simulation
     */
    public static function resume($simulation, $ignoreTimeShift = false)
    {
        if (!empty($simulation->paused)) {
            $skipped = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($simulation->paused);
            self::update($simulation, $skipped, $ignoreTimeShift);
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
        $speedFactor = $simulation->getSpeedFactor();

        $variance = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($simulation->start);
        $variance = $variance * $speedFactor;

        $unixtimeMins = round($variance / 60);
        $start_time = explode(':', $simulation->game_type->start_time);
        $clockH = round($unixtimeMins / 60);
        $clockM = $unixtimeMins - ($clockH * 60);
        $clockH = $clockH + $start_time[0];
        $clockM = $clockM + $start_time[1];

        $startTime = GameTime::setUnixDateTime((GameTime::getUnixDateTime($simulation->start) - (($newHours - $clockH) * 60 * 60 / $speedFactor)
            - (($newMinutes - $clockM) * 60 / $speedFactor)));

        $simulation->refresh();
        $simulation->start = $startTime;
        $simulation->save();
    }

    /**
     * @param $simulation
     */
    public static function stressResistance($simulation) {
        /*
         * AssessmentAggregated 7141
         */
        /* @var $simulation Simulation */
        /* @var $game_type Scenario */
        $game_type = $simulation->game_type;
        $point = $game_type->getHeroBehaviour(['code' => 7141]);

        /* @var $stress StressPoint[] */
        $stress = StressPoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        if(null !== $stress) {
            $value = 0;
            foreach( $stress as $stress_rule ) {
                $value += $stress_rule->stressRule->value;
            }
        } else {
            $value = 0;
        }

        $assessment = new AssessmentCalculation();
        $assessment->point_id = $point->id;
        $assessment->sim_id = $simulation->id;
        $assessment->value = round($value, 2);
        $assessment->save();

    }

    /**
     * @param Simulation $simulation
     * @param Scenario $gameTime
     *
     * @throws InviteException
     */
    /*public static function simulationIsStarted($simulation, $gameTime)
    {

        if($simulation->isTutorial()){ return; }
        if(strtotime('10:00:00') <= strtotime($gameTime) && strtotime($gameTime) <= strtotime('10:30:00') ){

            $invite = Invite::model()->findByAttributes(['simulation_id' => $simulation->id]);

            if (null === $invite) {
                if (false === Yii::app()->user->data()->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE &&
                    true === $simulation->game_type->isAllowOverride())) {
                    throw new InviteException('Симуляция запущена без инвайта');
                }
            } else if ((int)$invite->status === Invite::STATUS_ACCEPTED) {
                $invite->status = Invite::STATUS_IN_PROGRESS;
                $invite->save(false);
                if ($invite->isTrialFull(Yii::app()->user->data())
                    && Yii::app()->user->data()->isCorporate() && (int)$simulation->mode !== Simulation::MODE_DEVELOPER_ID) {

                    $initValue = Yii::app()->user->data()->getAccount()->invites_limit;

                    Yii::app()->user->data()->getAccount()->invites_limit--;
                    Yii::app()->user->data()->getAccount()->save(false);

                    UserService::logCorporateInviteMovementAdd(
                        'simulationIsStarted',
                        Yii::app()->user->data()->getAccount(),
                        $initValue
                    );
                }
            } else if((int)$invite->status === Invite::STATUS_IN_PROGRESS) {
                return;
            } else {
                throw new InviteException("Статус инвайта должен быть STATUS_ACCEPTED или STATUS_IN_PROGRESS. А он ".$invite->status);
            }
        }

    }*/

    /**
     * @param $simId
     * @param $email
     * @throws Exception
     */
    public static function CalculateTheEstimate($simId, $email) {

        /** @var  $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        if(null === $simulation){
            throw new Exception("Simulation by id = {$simId} not found.");
        }
        /* @var $profile YumProfile */
        $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
        if(null === $profile){
            throw new Exception("Profile by email = {$email} not found.");
        }

        if($profile->user_id !== $simulation->user_id){
            throw new Exception("This simulation does not belong to this user.");
        }

        LogActivityActionAgregated::model()->deleteAllByAttributes(['sim_id' => $simId]);

        TimeManagementAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentCalculation::model()->deleteAllByAttributes(['sim_id' => $simId]);
        DayPlanLog::model()->deleteAllByAttributes(['sim_id' => $simId, 'snapshot_time' => DayPlanLog::ON_18_00]);
        LogActivityActionAgregated214d::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentPlaningPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationExcelPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PerformancePoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PerformanceAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        StressPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningGoal::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningArea::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningGoalGroup::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentOverall::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogAssessment214g::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationService::simulationStop($simulation, [], true);
    }

    /**
     * Лог про действия над симуляцией: типа начата, закончена, показано сообщение в 18-00...
     * @param Simulation $simulation
     * @param string $action
     */
    public static function logAboutSim(Simulation $simulation, $action = 'not specified') {

        $comment = '';
        $invite = null;

        $log = new LogSimulation();

        // action
        $log->action = $action;

        // timing
        $log->real_date = date('Y-m-d H:i:s');
        $log->game_time_frontend = Yii::app()->request->getParam('time');

        if (null != $simulation) {
            // add sim_id
            $log->sim_id = $simulation->id;

            // add invite {
            $invites = Invite::model()->findAllByAttributes(['simulation_id' => $simulation->id]);

            if (1 == count($invites)) {
                $invite = reset($invites);

                $log->invite_id = $invite->id;
            } elseif (0 == count($invites)) {
                $comment .= "There is no invites for this simulation!\n";
            } else {
                $comment .= "There are several invites for this simulation!\n";
                $list = [];
                foreach ($invites as $invite) {
                    $list[] = $invite->id;
                }
                $comment .= implode($list);
                unset($list);
                $invite = null;
            }
            // add invite }

            // mode
            $log->mode = $simulation->getModeLabel();

            // game time by backend version
            $log->game_time_backend = $simulation->getGameTime();
        }

        // scenario_name
        if (null !== $invite) {
            $log->scenario_name = $simulation->game_type->slug;
        }

        // add user_id {
        if (null !== Yii::app()->user && null !== Yii::app()->user->data() && Yii::app()->user->data()->id) {
            $log->user_id = Yii::app()->user->data()->id;
        } else {
            $comment .= "Undefined user_id!\n";
        }
        // add user_id }

        $log->comment = $comment;

        $log->save(false);
    }

    /**
     * Удаляет симуляцию и её инвайт
     *
     * @param YumUser $user
     * @param Simulation $simulation
     * @return bool
     */
    public static function removeSimulationData($user, $simulation, $simId = null)
    {
        if (false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            return false;
        }

        if (null === $simulation && null === $simId) {
            return false;
        }

        if (null !== $simulation) {
            $simId = $simulation->id;
        }

        AssessmentAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentCalculation::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentOverall::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentPlaningPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        DayPlan::model()->deleteAllByAttributes(['sim_id' => $simId]);
        DayPlanLog::model()->deleteAllByAttributes(['sim_id' => $simId]);
        EventTrigger::model()->deleteAllByAttributes(['sim_id' => $simId]);

        $mails = MailBox::model()->findAllByAttributes(['sim_id' => $simId]);
        foreach ($mails as $mail) {
            MailAttachment::model()->deleteAllByAttributes(['mail_id' => $mail->id]);
            MailCopy::model()->deleteAllByAttributes(['mail_id' => $mail->id]);
            MailMessage::model()->deleteAllByAttributes(['mail_id' => $mail->id]);
            MailRecipient::model()->deleteAllByAttributes(['mail_id' => $mail->id]);
        }

        MailBox::model()->deleteAllByAttributes(['sim_id' => $simId]);
        MyDocument::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PerformanceAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PerformancePoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        PhoneCall::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationExcelPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationFlag::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationFlagQueue::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningArea::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningGoal::model()->deleteAllByAttributes(['sim_id' => $simId]);
        SimulationLearningGoalGroup::model()->deleteAllByAttributes(['sim_id' => $simId]);
        StressPoint::model()->deleteAllByAttributes(['sim_id' => $simId]);
        TimeManagementAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);

        LogActivityAction::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogActivityActionAgregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogActivityActionAgregated214d::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogAssessment214g::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogCommunicationThemeUsage::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogDialog::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogDocument::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogIncomingCallSoundSwitcher::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogInvite::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogMail::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogMeeting::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogReplica::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogServerRequest::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogSimulation::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogWindow::model()->deleteAllByAttributes(['sim_id' => $simId]);

        UniversalLog::model()->deleteAllByAttributes(['sim_id' => $simId]);

        if (null !== $simulation->invite) {
            $simulation->invite->delete();
        }

        $simulation->delete();
    }

    public static function saveLogsAsExcelCombined($simulations = array()) {
        if(!empty($simulations)) {
            $logTableList = new LogTableList();
            foreach($simulations as $simulation) {
                $logTableList->setSimulationId($simulation);
                $user_fullname = $simulation->user->profile->firstname . " " . $simulation->user->profile->lastname;
                $logTableList->asExcelCombined($user_fullname, $simulation->id);
            }
            $excelWriter = $logTableList->returnXlsFile();
            $excelWriter->save(__DIR__.'/../logs/combined-log.xlsx');
            return true;
        }
    }
}
