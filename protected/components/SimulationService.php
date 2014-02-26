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
            $row->percent = round($value / $categoryRates[$cid] * 100, 6);

            $row->save(false);
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
                $invite_status = $invite->status;
                $invite->status = Invite::STATUS_ACCEPTED;
                $invite->sent_time = date("Y-m-d H:i:s"); // @fix DB!
                $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
                $invite->save(true, [
                    'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
                ]);

                $invite->email = strtolower($user->profile->email);
                $invite->save(false);
                InviteService::logAboutInviteStatus($invite, "При старте симуляции статус инвайта изменен с ".Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($invite->status));
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

        // for Demo
        if (null !== $invite->receiverUser) {
            $simulation->user_id = $invite->receiverUser->id;
        }
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
            if (null !== $invite->ownerUser && $invite->ownerUser->isCorporate()
                && Scenario::TYPE_TUTORIAL == $scenarioType
                && $simulationMode != Simulation::MODE_DEVELOPER_LABEL
                && $invite->ownerUser->id == $invite->receiverUser->id) {
                if(InviteService::isSimulationOverrideDetected($invite)){
                    $init_value = $invite->ownerUser->getAccount()->getTotalAvailableInvitesLimit();
                    UserService::logCorporateInviteMovementAdd('Инвайт не был снят потому что предыдущая симуляция по инвайту '.$invite->id.' не была завершена', $invite->ownerUser->getAccount(), $init_value);
                }else{
                    $init_value = $invite->ownerUser->getAccount()->getTotalAvailableInvitesLimit();
                    $invite->ownerUser->getAccount()->decreaseLimit();
                    UserService::logCorporateInviteMovementAdd(sprintf("Симуляция списана за начало симуляции по собственной инициативе, номер приглашения %s",
                        $invite->id), $invite->ownerUser->getAccount(), $init_value);
                    $invite->ownerUser->getAccount()->save(false);
                }
            }

            $invite->update();
            if(InviteService::isSimulationOverrideDetected($invite)){
                /* @var $sim Simulation */
                // повторный старт!
                if(null !== $invite->simulation){
                    $invite->simulation->status = Simulation::STATUS_INTERRUPTED;
                    $invite->simulation->save();

                    InviteService::logAboutInviteStatus($invite, "Игрок прервал симуляцию - ".$invite->simulation_id." по этому инвайту");
                    $invite->simulation = null;
                    $invite->simulation_id = null;
                    $invite->save();
                }
            }
            $invite->simulation_id = $simulation->id;
            $scenario = Scenario::model()->findByPk($invite->scenario_id);
            /* @var $scenario Scenario */
            if($scenario->isLite()) {
                $invite_status = $invite->status;
                $invite->status = Invite::STATUS_IN_PROGRESS;
                $invite->save(false, ['simulation_id', 'status']);
                InviteService::logAboutInviteStatus($invite, "При старте симуляции статус инвайта изменен с ".Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($invite->status));
            } else {
                $invite->save(false, ['simulation_id']);
                //InviteService::logAboutInviteStatus($invite, 'invite : update sim_id (3) : sim start');
            }

        }

        self::logAboutSim($simulation, 'sim start: done');

        return $simulation;
    }

    /**
     * @param Simulation $simulation
     * @param array $logs_src
     * @param bool $manualRecalculation
     */
    public static function simulationStop(Simulation $simulation, $logs_src = array(), $manualRecalculation = false)
    {

            // Check if simulation was already stopped
            if (null !== $simulation->end && false === $manualRecalculation) {
                return;
            }

            if (Simulation::STATUS_INTERRUPTED == $simulation->status) {
                return;
            }

            // If simulation was started by invite, mark it as completed
            if (null !== $simulation->invite && $simulation->isTutorial() === false) {
                $invite_status = $simulation->invite->status;
                $simulation->invite->status = Invite::STATUS_COMPLETED;
                $simulation->invite->save(false);
                InviteService::logAboutInviteStatus($simulation->invite, "При завершении симуляции статус инвайта изменился с ".Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($simulation->invite->status));
            }

            if (null !== $simulation->invite && $simulation->isTutorial()) {
                $simulation->invite->tutorial_finished_at = date('Y-m-d H:i:s');
                $simulation->invite->save(false);
                InviteService::logAboutInviteStatus($simulation->invite, 'При завершении симуляции статус инвайта '.Invite::getStatusNameByCode($simulation->invite->status));
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

                LogHelper::updateUniversalLog($simulation);
                $analyzer = new ActivityActionAnalyzer($simulation);
                $analyzer->run();

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

                $simulation->calculatePercentile();

                self::logAboutSim($simulation, sprintf(
                    'sim stop: assessment calculated. Overall: %s, Percentile  %s.',
                    (float)$simulation->getCategoryAssessmentWithoutRound(),
                    (float)$simulation->getCategoryAssessmentWithoutRound(AssessmentCategory::PERCENTILE)
                ));
            }

            // @ - for PHPUnit
            if (Scenario::TYPE_TUTORIAL !== $simulation->game_type->slug ||
                true == $simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
                @ Yii::app()->request->cookies['display_result_for_simulation_id'] =
                    new CHttpCookie('display_result_for_simulation_id', $simulation->id);
            }

            if ($simulation->isFull()) {
                // просто $simulation->invite->can_be_reloaded = false; не сохраняет!!!
                $tmpInvite = $simulation->invite;
                $tmpInvite->can_be_reloaded = false;
                $tmpInvite->save(false);
                InviteService::logAboutInviteStatus($tmpInvite, 'Запрещаеться возможность начать симуляцию по этому инвайту повторно can_be_reloaded = false');
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

            EventTrigger::model()->deleteAllByAttributes(['sim_id' => $simulation->id]);

            $simulation->end = GameTime::setNowDateTime();
            $simulation->status = Simulation::STATUS_COMPLETE;
            $simulation->save(false);
            $simulation->refresh();
            $simulation->getAssessmentDetails();
            $assessment_engine_version = Yii::app()->params['assessment_engine_version'];
            $simulation->results_popup_partials_path = '//simulation_details_popup/'.$assessment_engine_version;
            $simulation->assessment_version = $assessment_engine_version;
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

        LogActivityAction::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogActivityActionAgregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        LogActivityActionAgregated214d::model()->deleteAllByAttributes(['sim_id' => $simId]);
        TimeManagementAggregated::model()->deleteAllByAttributes(['sim_id' => $simId]);
        AssessmentCalculation::model()->deleteAllByAttributes(['sim_id' => $simId]);
        DayPlanLog::model()->deleteAllByAttributes(['sim_id' => $simId, 'snapshot_time' => DayPlanLog::ON_18_00]);
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
        if (false == Yii::app() instanceof CConsoleApplication
            && null !== Yii::app()->user
            && null !== Yii::app()->user->data() && Yii::app()->user->data()->id) {
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
        if (false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE) &&
            false == Yii::app() instanceof CConsoleApplication) {
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
        //LogWindow::model()->deleteAllByAttributes(['sim_id' => $simId]);

        UniversalLog::model()->deleteAllByAttributes(['sim_id' => $simId]);

        if (null !== $simulation->invite) {
            $simulation->invite->delete();
        }

        $simulation->delete();
    }

    public static function saveLogsAsExcelReport1($simulations = array()) {
        if(!empty($simulations)) {
            $logTableList = new LogTableList();
            foreach($simulations as $simulation) {
                $logTableList->setSimulation($simulation);
                $user_fullname = (empty($simulation->user))?'Аноним':$simulation->user->profile->firstname . " " . $simulation->user->profile->lastname;
                $logTableList->saveLogsAsExcelReport1($user_fullname, $simulation->id);
            }
            $excelWriter = $logTableList->returnXlsFile();
            $excelWriter->save(__DIR__.'/../logs/combined-log_report-1.xlsx');
            return true;
        }
    }

    public static function saveLogsAsExcelReport2($simulations = array(), $account = null) {
        if(!empty($simulations)) {
            $logTableList = new LogTableList();
            foreach($simulations as $simulation) {
                $logTableList->setSimulation($simulation);
                $logTableList->saveLogsAsExcelReport2();
            }
            $excelWriter = $logTableList->returnXlsFile();
            if($account === null){
                $user_id = 'custom';
            }else{
                $user_id = $account->user_id;
            }

            $path = self::createPathForAnalyticsFile($user_id, $simulation->assessment_version);

            $excelWriter->save($path);
            return $path;
        }

        // симуляций нет
        return null;
    }

    public static function createPathForAnalyticsFile($user_id, $assessment_version) {
        return __DIR__.'/../system_data/analytic_files_2/'.$user_id.'_'.$assessment_version.'.xlsx';
    }

    public static function saveLogsAsExcelReport2ForCorporateUser(UserAccountCorporate $account, $assessment_version) {
        $invites = Invite::model()->findAllByAttributes(['owner_id'=>$account->user_id]);
        $simulations = [];
        foreach($invites as $invite) {
            /* @var Invite $invite */
            if(null === $invite->simulation) {
                continue;
            }
            $isCompleted = $invite->simulation->end !== null;
            $isFull = $invite->simulation->isFull();
            $isValidAssessmentVersion = $invite->simulation->assessment_version === $assessment_version;
            if($isCompleted && $isFull && $isValidAssessmentVersion) {
                $simulations[] = $invite->simulation;
            }
        }

        return self::saveLogsAsExcelReport2($simulations, $account);
    }

    public static function saveAssessmentPDFFilesOnDisk(Simulation $simulation){

        //$simulation = Simulation::model()->findByPk('10264');
        $path = __DIR__."/../system_data/prb_bank/pdf_slices/".$simulation->id;
        $data = json_decode($simulation->getAssessmentDetails(), true);
        if(!is_dir($path)){
            mkdir($path);
        }
        $path.= '/';
        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        // 1. Спидометры и прочее
        $pdf->addSinglePage('bank_1', 0, 0, 190, 92);
        $pdf->addRatingOverall(75.2, 0, $data['overall']);
        $pdf->addSpeedometer(9.5, 58.5, $data['time']['total']);
        $pdf->addSpeedometer(78.4, 58.5, $data['performance']['total']);
        $pdf->addSpeedometer(147.3, 58.5, $data['management']['total']);

        $pdf->saveOnDisk($path.'bank_1');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->addSinglePage('bank_2', 0, 0, 185, 136);
        $pdf->addPercentSmallInfo($data['time']['total'], 117.3, 1.5);

        $pdf->addTimeDistribution(
            43,
            63.2,
            $data['time']['time_spend_for_1st_priority_activities'],
            $data['time']['time_spend_for_non_priority_activities'],
            $data['time']['time_spend_for_inactivity']
        );
        $pdf->addOvertime(145.2, 63.8, $data['time']['workday_overhead_duration']);

        $pdf->saveOnDisk($path.'bank_2');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(204, 110);
        $pdf->addSinglePage('bank_3', 0, 0, 204, 110);

        $pdf->addPercentSmallInfo($data['time']['total'], 174, 1.8);

        $pdf->addPercentMiddleInfo(
            $data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES],
            79.1,
            23.5
        ); //Продуктивное время


        $pdf->addPercentMiddleInfo($data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES], 183.5, 23.5);//Не продуктивное время

        //Positive
        $x_positive = 31;
        $max_positive = $pdf->getMaxTimePositive($data['time']);

        //Документы
        $pdf->addTimeBarProductive($x_positive, 44, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS], $max_positive);

        //Встречи
        $pdf->addTimeBarProductive($x_positive, 55, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS], $max_positive);

        //Звонки
        $pdf->addTimeBarProductive($x_positive, 65.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS], $max_positive);

        //Почта
        $pdf->addTimeBarProductive($x_positive, 76.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL], $max_positive);

        //План
        $pdf->addTimeBarProductive($x_positive, 87, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING], $max_positive);

        //Negative
        $y_positive = 134.5;
        $max_negative = $pdf->getMaxTimeNegative($data['time']);

        //Документы
        $pdf->addTimeBarUnproductive($y_positive, 44, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS], $max_negative);

        //Встречи
        $pdf->addTimeBarUnproductive($y_positive, 55, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS], $max_negative);

        //Звонки
        $pdf->addTimeBarUnproductive($y_positive, 65.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS], $max_negative);

        //Почта
        $pdf->addTimeBarUnproductive($y_positive, 76.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL], $max_negative);

        //План
        $pdf->addTimeBarUnproductive($y_positive, 87, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING], $max_negative);


        $pdf->saveOnDisk($path.'bank_3');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(164, 202);
        $pdf->addSinglePage('bank_4', 0, 0, 177, 206);
        $pdf->addPercentSmallInfo($data['performance']['total'], 46.6, 11);

        //Срочно
        $pdf->addUniversalBar(47, 30.8, $pdf->getPerformanceCategory($data['performance'], '0'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Высокий приоритет
        $pdf->addUniversalBar(47, 41.3, $pdf->getPerformanceCategory($data['performance'], '1'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Средний приоритет
        $pdf->addUniversalBar(47, 51.9, $pdf->getPerformanceCategory($data['performance'], '2'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Двухминутные задачи
        $pdf->addUniversalBar(47, 62.5, $pdf->getPerformanceCategory($data['performance'], '2_min'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        $pdf->saveOnDisk($path.'bank_4');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(160, 80);
        $pdf->addSinglePage('bank_5', 0, 0, 190.5, 80);

        $pdf->addPercentSmallInfo($data['management']['total'], 149.7, 1.5);
        //48.9
        $pdf->addUniversalBar(61, 22.1, $data['management'][1]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//1
        $pdf->addUniversalBar(61, 32.7, $data['management'][2]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//2
        $pdf->addUniversalBar(61, 43.3, $data['management'][3]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//3


        $pdf->saveOnDisk($path.'bank_5');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(198, 104);
        $pdf->addSinglePage('bank_6', 0, 0, 205, 104);

        $pdf->addPercentBigInfo($data['management'][1]['total'], 1.3, 4.5);
        //60 - 23
        $pdf->addUniversalBar(75, 27.5, $data['management'][1]['1_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
        $pdf->addUniversalBar(75, 37.5, $data['management'][1]['1_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
        $pdf->addUniversalBar(75, 47.5, $data['management'][1]['1_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive

        $pdf->addUniversalBar(150, 27.5, $data['management'][1]['1_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
        $pdf->addUniversalBar(150, 37.5, $data['management'][1]['1_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
        $pdf->addUniversalBar(150, 47.5, $data['management'][1]['1_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
        $pdf->addUniversalBar(150, 57.5, $data['management'][1]['1_4']['-'], 54.14, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_NEGATIVE);//1.4 negative

        $pdf->saveOnDisk($path.'bank_6');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(198, 95);
        $pdf->addSinglePage('bank_7', 0, 0, 205, 95);

        $pdf->addPercentBigInfo($data['management'][2]['total'], 0.5, 4.7);

        $pdf->addUniversalBar(75, 28, $data['management'][2]['2_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.1 positive
        $pdf->addUniversalBar(75, 38, $data['management'][2]['2_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.2 positive
        $pdf->addUniversalBar(75, 48, $data['management'][2]['2_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.3 positive

        $pdf->addUniversalBar(150, 28, $data['management'][2]['2_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.1 negative
        $pdf->addUniversalBar(150, 38, $data['management'][2]['2_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.2 negative
        $pdf->addUniversalBar(150, 48, $data['management'][2]['2_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.3 negative

        $pdf->saveOnDisk($path.'bank_7');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(198, 105);
        $pdf->addSinglePage('bank_8', 0, 0, 205, 105);

        $pdf->addPercentBigInfo($data['management'][3]['total'], 0.8, 4.3);

        $pdf->addUniversalBar(75, 28, $data['management'][3]['3_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.1 positive
        $pdf->addUniversalBar(75, 38, $data['management'][3]['3_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.2 positive
        $pdf->addUniversalBar(75, 48, $data['management'][3]['3_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.3 positive
        $pdf->addUniversalBar(75, 58, $data['management'][3]['3_4']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.4 positive

        $pdf->addUniversalBar(150, 28, $data['management'][3]['3_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.1 negative
        $pdf->addUniversalBar(150, 38, $data['management'][3]['3_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.2 negative
        $pdf->addUniversalBar(150, 48, $data['management'][3]['3_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.3 negative
        $pdf->addUniversalBar(150, 58, $data['management'][3]['3_4']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.4 negative

        $pdf->saveOnDisk($path.'bank_8');

        $pdf = new AssessmentPDF();
        $pdf->setImagesDir('simulation_details_v2_for_bank/images/');
        $pdf->setEpsSize(148, 100);
        $pdf->addSinglePage('bank_9', 0, 0, 176.5, 100);
        $pdf->addPercentSmallInfo($data['performance']['total'], 45.8, 11.6);

        //Срочно
        $pdf->addUniversalBar(47, 31.7, $pdf->getPerformanceCategory($data['performance'], '0'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Высокий приоритет
        $pdf->addUniversalBar(47, 42.5, $pdf->getPerformanceCategory($data['performance'], '1'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Средний приоритет
        $pdf->addUniversalBar(47, 52.8, $pdf->getPerformanceCategory($data['performance'], '2'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Двухминутные задачи
        $pdf->addUniversalBar(47, 63.9, $pdf->getPerformanceCategory($data['performance'], '2_min'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        $pdf->saveOnDisk($path.'bank_9');
    }
}
