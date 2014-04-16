<?php

use application\components\Logging\LogTableList as LogTableList;

/**
 * Модель симуляции.
 *
 * @property int id
 * @property int user_id
 * @property string start
 * @property string end
 * @property int mode
 * @property int type
 * @property string paused
 * @property int skipped
 * @property int scenario_id
 * @property string $uuid
 * @property string $results_popup_partials_path
 * @property string $results_popup_cache
 * @property int $is_emergency_panel_allowed
 * @property string $screen_resolution
 * @property string $window_resolution
 * @property string $user_agent
 * @property string $ipv4
 * @property string $status
 * @property string $percentile
 * @property string $assessment_version
 * @property string $popup_tests_cache
 * @property string $behaviours_cache
 *
 * @property SimulationCompletedParent[] $completed_parent_activities
 * @property AssessmentAggregated[] $assessment_aggregated
 * @property LogWindow[] $log_windows
 * @property LogActivityAction[] $log_activity_actions
 * @property LogActivityActionAggregated[] $log_activity_actions_aggregated
 * @property LogActivityActionAggregated214d[] $log_activity_actions_aggregated_214d
 * @property LogMail[] $log_mail
 * @property LogDialog[] $log_dialogs
 * @property LogDialog[] $log_meetings
 * @property AssessmentCalculation[] $assessment_calculation
 * @property AssessmentPoint[] $assessment_points
 * @property LogDocument[] $log_documents
 * @property DayPlanLog[] $log_day_plan
 * @property SimulationExcelPoint[] $simulation_excel_points
 * @property PerformancePoint[] $performance_points
 * @property PerformanceAggregated[] $performance_aggregated
 * @property StressPoint[] $stress_points
 * @property AssessmentOverall[] $assessment_overall
 * @property Scenario $game_type
 * @property SimulationLearningArea[] $learning_area
 * @property SimulationLearningGoal[] $learning_goal
 * @property SimulationLearningGoalGroup[] $learning_goal_group
 * @property TimeManagementAggregated[] $time_management_aggregated
 * @property Invite $invite
 * @property MailBox[] $mail_box_outbox
 * @property SimulationFlag[] $simFlags
 * @property LogAssessment214g[] $logAssessment214g
 * @property UniversalLog[] $universal_log
 * @property YumUser $user
 */

class Simulation extends CActiveRecord
{
    const MODE_PROMO_ID       = 1;
    const MODE_DEVELOPER_ID   = 2;

    const MODE_PROMO_LABEL     = 'promo';
    const MODE_DEVELOPER_LABEL = 'developer';

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_INTERRUPTED = 'interrupted';
    const STATUS_COMPLETE    = 'complete';

    const ASSESSMENT_VERSION_1 = 'v1';
    const ASSESSMENT_VERSION_2 = 'v2';

    public $id;

    //public $assessment_version;

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @param string $time, 'H:i'
     * @param string $date, 'Y:-m-d'
     * @return string
     */
    public function formatDateForMissedCalls($time, $date = null)
    {
        if (null === $date) {
            $date = $this->game_type->scenario_config->game_date_data;
        }

        return $date . ' | ' . $time;
    }

    /**
     * @return string
     */
    public function getModeLabel() {
        return ($this->mode == self::MODE_PROMO_ID) ? self::MODE_PROMO_LABEL : self::MODE_DEVELOPER_LABEL;
    }

    /**
     * @param bool $isConsoleCall
     * @return bool|string
     */
    public function saveLogsAsExcel($isConsoleCall = false)
    {
        $logTableList = new LogTableList($this);
        $excelWriter = $logTableList->asExcel();
        //$excelWriter->save($this->getLogFilename($this->id));
        $excelWriter->save($this->getLogFilename($this->id));
        if ($isConsoleCall) {
            // just console notification
            return $this->getLogFilename($this->id)."- stored \r\n";
        }

        return true;
    }

    /**
     * @return string
     */
    public function getLogFileName()
    {
        return __DIR__.'/../logs/'.sprintf("%s-log.xlsx", $this->id);
    }

    /**
     * @param YumUser $user
     * @param Invite $invite
     *
     * @return bool
     */
    public function isAllowedToSeeResults(YumUser $user)
    {
        // просто проверка
        if (null === $user) {
            return false;
        }

        if (null === $this->invite && true === $this->game_type->isAllowOverride()) {
            return true;
        }

        if (null === $this->invite) {
            return false;
        }

        // просто проверка
        if (false === $this->invite->isCompleted()) {
            return false;
        }

        if($user->isAdmin()) {
            return true;
        }

        // создатель всегда может
        if ($this->invite->owner_id == $user->id) {
            return true;
        }

        // истанная проверка - is_display_simulation_results, это главный переметр
        // при решении отображать результаты симуляции или нет
        if (1 === (int)$this->invite->is_display_simulation_results) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getStatusCss()
    {
        $arr = [
            self::STATUS_IN_PROGRESS => 'label-warning',
            self::STATUS_INTERRUPTED => 'label-important',
            self::STATUS_COMPLETE => 'label-success',
        ];

        if (isset($arr[$this->status])) {
            return $arr[$this->status];
        }

        return '';
    }

    /**
     * @param YumUser $user, to check is it allowed for this $user to clean events queue
     * @param boolean $exceptEmails
     * @return boolean
     */
    public function cleanEventsQueue($user, $exceptEmails = true)
    {
        if ($user->id != $this->user->id) {
            return false;
        }

        if (false === $user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            return false;
        }

        if ($exceptEmails) {
            // UPDATE with JOIN works in Yii 1.1.14 only
            // @link: http://stackoverflow.com/questions/10579587/yii-update-with-join
            // so - use findAll + foreach

            $events = EventTrigger::model()->findAll(
                ' sim_id = :sim_id ',
                [
                    'sim_id' => $this->id,
                ]
            );

            /**
             * @var  EventTrigger $event
             */
            foreach ($events as $event) {

                // clean time for non emails
                if (false == $event->event_sample->isMail()) {
                    $event->trigger_time = null;
                    $event->save();
                }
            }
        } else {
            EventTrigger::model()->updateAll(
                [
                    'trigger_time' => NULL,
                ],
                ' sim_id = :sim_id ',
                [
                    'sim_id' => $this->id,
                ]
            );
        }

        return true;
    }

    /**
     * @param $user
     * @param $type
     * @return CActiveRecord|null
     */
    public function getLastSimulation($user, $type) {
        $scenario = Scenario::model()->findByAttributes(['slug' => $type]);
        if ($scenario) {
            $this->getDbCriteria()->mergeWith(array(
                'condition' => "user_id = :user_id AND end is not null AND scenario_id = :scenario_id ORDER BY id DESC",
                'params' => ['user_id' => $user->id, 'scenario_id' => $scenario->id],
            ));
            return $this->find();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isFull() {
        return ($this->game_type->slug === Scenario::TYPE_FULL) ? true : false;
    }

    /**
     * @return bool
     */
    public function isTutorial() {

        return ($this->game_type->slug === Scenario::TYPE_TUTORIAL)?true:false;

    }

    /**
     * @return mixed
     */
    public function getSpeedFactor()
    {
        return Yii::app()->params['public'][
        $this->mode == self::MODE_DEVELOPER_ID ? 'skiliksDeveloperModeSpeedFactor' : 'skiliksSpeedFactor'
        ];
    }

    /**
     * @param string $category
     * @return float|null
     */
    public function getCategoryAssessment($category = AssessmentCategory::OVERALL)
    {
        foreach ($this->assessment_overall as $rate) {
            if ($rate->assessment_category_code == $category) {
                return round($rate->value);
            }
        }
        return null;
    }

    /**
     * @param $category
     * @return float|null
     */
    public function getCategoryAssessmentWithoutRound($category = AssessmentCategory::OVERALL) {
        foreach ($this->assessment_overall as $rate) {
            if ($rate->assessment_category_code == $category) {
                return $rate->value;
            }
        }
        return null;
    }

    /**
     * @return string, JSON
     */
    public function getAssessmentDetails()
    {
        // use cached results popup data
        if (null !== $this->results_popup_cache) {
            $cache = unserialize($this->results_popup_cache);
            if(!is_array($cache)){
                $cache = [];
            }
            if($this->assessment_version === Simulation::ASSESSMENT_VERSION_1 ) {
                $empty_cache = '{"management":{"1":{"total":"0","1_1":{"+":"0","-":"0"},"1_2":{"+":"0","-":"0"},"1_3":{"+":"0","-":"0"},"1_5":{"+":"0","-":"0"},"1_4":{"+":"0","-":"0"}},"2":{"total":"0","2_1":{"+":"0","-":"0"},"2_2":{"+":"0","-":"0"},"2_3":{"+":"0","-":"0"}},"3":{"total":"0","3_1":{"+":"0","-":"0"},"3_2":{"+":"0","-":"0"},"3_3":{"+":"0","-":"0"},"3_4":{"+":"0","-":"0"}},"total":"0"},"performance":{"0":"0","1":"0","2":"0","total":"0","2_min":"0"},"time":{"total":"0","workday_overhead_duration":"0","time_spend_for_1st_priority_activities":"0","time_spend_for_non_priority_activities":"0","time_spend_for_inactivity":"0","1st_priority_documents":"0","1st_priority_meetings":"0","1st_priority_phone_calls":"0","1st_priority_mail":"0","1st_priority_planning":"0","non_priority_documents":"0","non_priority_meetings":"0","non_priority_phone_calls":"0","non_priority_mail":"0","non_priority_planning":"0","efficiency":"0"},"overall":"0","percentile":{"total":"0"},"personal":{"9":"0","10":"0","11":"0","12":"0","13":"0","14":"0","15":"0","16":"0"},"additional_data":{"management":"0","performance":"0","time":"0"}}';

                return json_encode(array_replace_recursive(json_decode($empty_cache, true), $cache));

            }
            if($this->assessment_version === Simulation::ASSESSMENT_VERSION_2 ) {
                $empty_cache = '{"management":{"1":{"total":"0","1_1":{"+":"0","-":"0"},"1_2":{"+":"0","-":"0"},"1_3":{"+":"0","-":"0"},"1_4":{"+":"0","-":"0"}},"2":{"total":"0","2_1":{"+":"0","-":"0"},"2_2":{"+":"0","-":"0"},"2_3":{"+":"0","-":"0"}},"3":{"total":"0","3_1":{"+":"0","-":"0"},"3_2":{"+":"0","-":"0"},"3_3":{"+":"0","-":"0"},"3_4":{"+":"0","-":"0"}},"total":"0"},"performance":{"0":"0","1":"0","2":"0","total":"0","2_min":"0"},"time":{"total":"0","workday_overhead_duration":"0","time_spend_for_1st_priority_activities":"0","time_spend_for_non_priority_activities":"0","time_spend_for_inactivity":"0","1st_priority_documents":"0","1st_priority_meetings":"0","1st_priority_phone_calls":"0","1st_priority_mail":"0","1st_priority_planning":"0","non_priority_documents":"0","non_priority_meetings":"0","non_priority_phone_calls":"0","non_priority_mail":"0","non_priority_planning":"0","efficiency":"0"},"overall":"0","percentile":{"total":"0"},"personal":{"9":"0","10":"0","11":"0","12":"0","13":"0","14":"0","15":"0","16":"0"},"additional_data":{"management":"0","performance":"0","time":"0"}}';

                return json_encode(array_replace_recursive(json_decode($empty_cache, true), $cache));

            }
            return json_encode($cache);
        }

        if ($this->game_type->isLite()) {
            return StaticSiteTools::getRandomAssessmentDetails();
        }

        if($this->isCalculateTheAssessment()) {
            $result = [];

            // Overall results
            foreach ($this->assessment_overall as $rate) {
                if ($rate->assessment_category_code == AssessmentCategory::OVERALL) {
                    $result[AssessmentCategory::OVERALL] = $rate->value;
                } else {
                    $result[$rate->assessment_category_code] = ['total' => $rate->value];
                }
            }

            // Management
            foreach ($this->learning_area as $row) {
                if ($row->learningArea->code <= 8) {
                    $result[AssessmentCategory::MANAGEMENT_SKILLS][$row->learningArea->code] = ['total' => $row->value];
                }
            }

            foreach ($this->learning_goal_group as $row) {
                if ($row->learningGoalGroup->learning_area_code <= 3) {
                    $result[AssessmentCategory::MANAGEMENT_SKILLS]
                    [$row->learningGoalGroup->learning_area_code]
                    [$row->learningGoalGroup->code] = ['+' => $row->percent, '-' => $row->problem];
                }
            }

            // Productivity
            foreach ($this->performance_aggregated as $row) {
                $result[AssessmentCategory::PRODUCTIVITY][$row->category_id] = $row->percent;
            }

            // Time management
            foreach ($this->time_management_aggregated as $row) {
                $result[AssessmentCategory::TIME_EFFECTIVENESS][$row->slug] = $row->value;
            }

            // Personal
            $result[AssessmentCategory::PERSONAL] = [];
            foreach ($this->learning_area as $row) {
                if ($row->learningArea->code > 8) {
                    $result[AssessmentCategory::PERSONAL][$row->learningArea->code] = $row->value;
                }
            }

            // get weight, just to use them like labels {
            $id = $this->game_type->id;
            if (Scenario::TYPE_LITE == $this->game_type->slug ) {
                $id = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL])->id;
            }

            $weights = Weight::model()->findAllByAttributes([
                'scenario_id' => $id,
                'rule_id'     => 1
            ]);

            foreach ($weights as $weight) {
                $result['additional_data'][$weight->assessment_category_code] = $weight->value;
            }
            // get weight, just to use them like labels }

            if($this->assessment_version === Simulation::ASSESSMENT_VERSION_2 ) {
                $empty_cache = '{"management":{"1":{"total":"0","1_1":{"+":"0","-":"0"},"1_2":{"+":"0","-":"0"},"1_3":{"+":"0","-":"0"},"1_4":{"+":"0","-":"0"}},"2":{"total":"0","2_1":{"+":"0","-":"0"},"2_2":{"+":"0","-":"0"},"2_3":{"+":"0","-":"0"}},"3":{"total":"0","3_1":{"+":"0","-":"0"},"3_2":{"+":"0","-":"0"},"3_3":{"+":"0","-":"0"},"3_4":{"+":"0","-":"0"}},"total":"0"},"performance":{"0":"0","1":"0","2":"0","total":"0","2_min":"0"},"time":{"total":"0","workday_overhead_duration":"0","time_spend_for_1st_priority_activities":"0","time_spend_for_non_priority_activities":"0","time_spend_for_inactivity":"0","1st_priority_documents":"0","1st_priority_meetings":"0","1st_priority_phone_calls":"0","1st_priority_mail":"0","1st_priority_planning":"0","non_priority_documents":"0","non_priority_meetings":"0","non_priority_phone_calls":"0","non_priority_mail":"0","non_priority_planning":"0","efficiency":"0"},"overall":"0","percentile":{"total":"0"},"personal":{"9":"0","10":"0","11":"0","12":"0","13":"0","14":"0","15":"0","16":"0"},"additional_data":{"management":"0","performance":"0","time":"0"}}';

                $result = array_replace_recursive(json_decode($empty_cache, true), $result);

            }
        }else{
            $result = '{"management":{"total":"0.00","1":{"total":"0.000000","1_1":{"+":"0.00","-":"0.00"},"1_2":{"+":"0.00","-":"0.00"},"1_3":{"+":"0.00","-":"0.00"},"1_5":{"+":"0.00","-":"0.00"},"1_4":{"+":"0.00","-":"0.00"}},"3":{"total":"0.00","3_1":{"+":"0.00","-":"0.00"},"3_2":{"+":"0.00","-":"0.00"},"3_3":{"+":"0.00","-":"0.00"},"3_4":{"+":"0.00","-":"0.00"}},"2":{"total":"0.000000","2_1":{"+":"0.00","-":"0.00"},"2_2":{"+":"0.00","-":"0.00"},"2_3":{"+":"0.00","-":"0.00"}}},"performance":{"total":"0.00"},"time":{"total":"0.00","workday_overhead_duration":"0.00","time_spend_for_1st_priority_activities":"0.00","time_spend_for_non_priority_activities":"0.00","time_spend_for_inactivity":"0.00","1st_priority_documents":"0.00","1st_priority_meetings":"0.00","1st_priority_phone_calls":"0.00","1st_priority_mail":"0.00","1st_priority_planning":"0.00","non_priority_documents":"0.00","non_priority_meetings":"0.00","non_priority_phone_calls":"0.00","non_priority_mail":"0.00","non_priority_planning":"0.00","efficiency":"0.00"},"overall":"0.00","personal":{"9":"0.000000","10":"0.000000","12":"0.000000","13":"0.000000","14":"0.000000","15":"0.000000","16":"0.000000","11":"0.000000"},"additional_data":{"management":"0.00","performance":"0.00","time":"0.00"}}';
            $result = json_decode($result);
        }

        // cache results popup data
        $this->results_popup_cache = serialize($result);
        $this->save(false);

        return json_encode($result);
    }

    /**
     * @return boolean
     */
    public function isCalculateTheAssessment() {
        return $this->game_type->isCalculateAssessment();
    }

    /**
     * @return string
     */
    public function getCurrentGameTime() {
        $lastLog = LogServerRequest::model()->find([
            'order'     => 'real_time DESC',
            'condition' => 'sim_id = '.$this->id,
            'limit' => 1,
        ]);

        return isset($lastLog) ? $lastLog->backend_game_time : null;
    }

    /**
     * Считает и заносит в БД процентиль
     */
    public function calculatePercentile()
    {
        if (0 == $this->getCategoryAssessmentWithoutRound()) {
            return;
        }

        // is developer?
        if (in_array($this->user->profile->email, UserService::$developersEmails) ||
            0 < strpos($this->user->profile->email, 'gty1991') ||
            0 < strpos($this->user->profile->email, '@drdrb.com') ||
            0 < strpos($this->user->profile->email, '@rmqkr.net') ||
            0 < strpos($this->user->profile->email, '@mqkr.net') ||
            0 < strpos($this->user->profile->email, '@skiliks.com') ||
            0 < strpos($this->user->profile->email, 'sarnavskyi89')
        ) {
            // set zero percentile for developer
            $assessmentRecord = $this->setAssessmentOverallPercentile(0);
            $assessmentRecord->save();

            return;
        }

        $this->refresh();

        // считаем количесво пользователей пошедших симуляцию, не разработчиков
        $realUsersCondition = $this->getSimulationRealUsersCondition();
        $all = AssessmentOverall::model()->with('sim', 'sim.user', 'sim.user.profile')
            ->count($realUsersCondition);

        // считаем количесво пользователей пошедших симуляцию, не разработчиков
        // но у которых оценка меньще или равна оценке за текущую симуляцию
        $lessThanMeCondition = $realUsersCondition .
            sprintf(' AND (t.value <= %s) ', $this->getCategoryAssessmentWithoutRound());
        $lessThanMe = AssessmentOverall::model()->with('sim', 'sim.user', 'sim.user.profile')
            ->count($lessThanMeCondition);

        if (0 == $lessThanMe) {
            // случай с первым пользователем (после реинициализации БД будет  пройденных симуляций)
            if (1 == $all) {
                $percentileValue = 100;
            } else {
                $percentileValue = 0;
            }
        } else {
            // при расчёте процентиля в время симстопа, статус текущей симуляции ещё IN_PROGRESS
            // а в ращёте участвуют только COMPLETE симуляции - чтоб герентировать правильность оценки
            // таким образом надо увеличить оба счётчика ($all и $lessThanMe) на единицу - на текущую симуляцию
            // чтоб получить правильный процентиль
            if ($this->status !== self::STATUS_COMPLETE) {
                $all = $all + 1;
                $lessThanMe = $lessThanMe + 1;
            }

            $percentileValue = ($lessThanMe/$all)*100;
        }

        $assessmentRecord = $this->setAssessmentOverallPercentile($percentileValue);
        $assessmentRecord->save();
    }

    /**
     * @param float $percentileValue
     *
     * @return AssessmentOverall
     */
    public function setAssessmentOverallPercentile($percentileValue) {
        $assessmentRecord = AssessmentOverall::model()->findByAttributes([
            'assessment_category_code' => AssessmentCategory::PERCENTILE,
            'sim_id'                   => $this->id
        ]);

        if (null == $assessmentRecord) {
            $assessmentRecord = new AssessmentOverall();
            $assessmentRecord->assessment_category_code = AssessmentCategory::PERCENTILE;
            $assessmentRecord->sim_id = $this->id;
        }

        $assessmentRecord->value = number_format(round($percentileValue, 2), 2, '.', '');

        return $assessmentRecord;
    }

    /**
     * @return array|CActiveRecord|mixed|null
     * Метод псевдостатический возвращает все симуляции реальных пользователей
     */

    public function getRealUsersSimulations() {

        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $condition = " profile.email NOT LIKE '%gty1991%' ".
            " AND profile.email NOT LIKE '%@skiliks.com' ".
            " AND profile.email NOT LIKE '%@drdrb.com' ".
            " AND profile.email NOT LIKE '%@rmqkr.net' ".
            " AND profile.email NOT LIKE 'sarnavskyi89%' ".
            " AND t.start > '2013-08-01 00:00:00' ".
            " AND profile.email NOT IN (".implode(',', UserService::$developersEmails).") " .
            " AND t.mode = ".self::MODE_PROMO_ID.
            " AND t.scenario_id = " . $scenario->id .
            " AND t.status = '" . self::STATUS_COMPLETE . "' ";
        return self::model()->with('user', 'user.profile')->findAll($condition);
    }

    /**
     * Метод возвращает критерий для поиска всех завершенных, полных
     * симуляций реальных пользователей
     *
     * @param string $additionalCondition
     *
     * @return array|CActiveRecord|mixed|null
     *
     */
    public function getSimulationRealUsersCondition( $additionalCondition = '',
                                                     $assessmentCategory = AssessmentCategory::OVERALL ) {
        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $condition = " profile.email NOT LIKE '%gty1991%' ".
            " AND profile.email NOT LIKE '%@skiliks.com' ".
            " AND profile.email NOT LIKE '%@drdrb.com' ".
            " AND profile.email NOT LIKE '%@rmqkr.net' ".
            " AND profile.email NOT LIKE '%@mqkr.net' ".
            " AND profile.email NOT LIKE 'sarnavskyi89%' ".
            " AND sim.start > '2013-08-01 00:00:00' ".
            " AND profile.email NOT IN (".implode(',', UserService::$developersEmails).")
              AND sim.mode = ".self::MODE_PROMO_ID."
              AND sim.scenario_id = " . $fullScenario->id . "
              AND sim.status = '" . self::STATUS_COMPLETE . "'" .
            sprintf(" AND t.assessment_category_code = '%s' ", $assessmentCategory) .
            $additionalCondition//.
            //' ORDER BY ' . $order
        ;

        return $condition;
    }

    /**
     * Setting status to simulation as interrupted and saves it
     */
    public function interruptSimulation()
    {
        $this->status = self::STATUS_INTERRUPTED;
        $this->simulation->save();
    }

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return Simulation
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'simulations';
    }

    /**
     * Выбрать по заданному пользователю.
     * @param int $uid
     * @return Simulation
     */
    public function byUid($uid)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'user_id = ' . (int)$uid
        ));
        return $this;
    }

    /**
     * Выбрать ближайшую симуляцию
     * @return Simulation
     */
    public function nearest()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => 'id DESC',
            'limit' => 1
        ));
        return $this;
    }

    /**
     * Returns current simulation time
     * @param $precision string second or minute
     * @return string Return H:i:s or H:i
     */
    public function getGameTime($precision='second')
    {
        $time = Yii::app()->request->getParam('time');
        if( null === $time ) {
            // for unit tests with time {
            if (isset(Yii::app()->session['gameTime'])) {
                if($precision === 'second') {
                    return date('H:i:s', strtotime(Yii::app()->session['gameTime']));
                } else if($precision === 'minute') {
                    return date('H:i', strtotime(Yii::app()->session['gameTime']));
                } else {
                    throw new Exception("Unknown precision type ".$precision);
                }
            }
            // for unit tests with time }

            $variance = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($this->start) - $this->skipped;
            $variance = $variance * $this->getSpeedFactor();

            $startTime = explode(':', $this->game_type->start_time);
            $unixtime = $variance + $startTime[0] * 3600 + $startTime[1] * 60 + $startTime[2];

            if($precision === 'second'){
                return gmdate('H:i:s', $unixtime); //for unit tests or console
            } else if($precision === 'minute') {
                return gmdate('H:i', $unixtime);
            } else{
                throw new Exception("Unknown precision type ".$precision);
            }
        }else{
            if($precision === 'second'){
                return date('H:i:s', strtotime($time));
            } else if($precision === 'minute') {
                return date('H:i', strtotime($time));
            } else{
                throw new Exception("Unknown precision type ".$precision);
            }
        }

    }

    /**
     * @param string $newHours
     * @param string $newMinutes
     */
    public function deleteOldTriggers($newHours, $newMinutes)
    {
        foreach ($this->events_triggers as $event_trigger) {
            if ($event_trigger->trigger_time == null) {
                continue;
            }
            if (preg_match('/^M/', $event_trigger->event_sample->code)) {
                continue;
            }
            if ($event_trigger->trigger_time == '00:00:00') {
                continue;
            }
            if (GameTime::timeToSeconds($event_trigger->trigger_time) < ($newHours * 60 + $newMinutes) * 60) {
                $event_trigger->delete();
            }
        }
    }

    public function relations()
    {
        return [
            'user'                                 => [self::BELONGS_TO, 'YumUser', 'user_id'],
            'events_triggers'                      => [self::HAS_MANY, 'EventTrigger', 'sim_id'],
            'log_windows'                          => [self::HAS_MANY, 'LogWindow', 'sim_id', 'order' => 'start_time, end_time'],
            'log_mail'                             => [self::HAS_MANY, 'LogMail', 'sim_id', 'order' => 'start_time, end_time'],
            'log_plan'                             => [self::HAS_MANY, 'DayPlanLog', 'sim_id', 'order' => 'start_time, end_time'],
            'log_dialogs'                          => [self::HAS_MANY, 'LogDialog', 'sim_id', 'order' => 'start_time, end_time'],
            'log_meetings'                         => [self::HAS_MANY, 'LogMeeting', 'sim_id', 'order' => 'start_time, end_time'],
            'log_documents'                        => [self::HAS_MANY, 'LogDocument', 'sim_id', 'order' => 'start_time, end_time'],
            'log_activity_actions'                 => [self::HAS_MANY, 'LogActivityAction', 'sim_id', 'order' => 'start_time, end_time'],
            'log_day_plan'                         => [self::HAS_MANY, 'DayPlanLog', 'sim_id'],
            'log_activity_actions_aggregated'      => [self::HAS_MANY, 'LogActivityActionAggregated', 'sim_id', 'order' => 'start_time, end_time'],
            'log_activity_actions_aggregated_214d' => [self::HAS_MANY, 'LogActivityActionAggregated214d', 'sim_id', 'order' => 'start_time, end_time'],
            'universal_log'                        => [self::HAS_MANY, 'UniversalLog', 'sim_id'],
            'completed_parent_activities'          => [self::HAS_MANY, 'SimulationCompletedParent', 'sim_id'],
            'assessment_aggregated'                => [self::HAS_MANY, 'AssessmentAggregated', 'sim_id', 'with' => 'point', 'order' => 'point.type_scale'],
            'performance_points'                   => [self::HAS_MANY, 'PerformancePoint', 'sim_id'],
            'performance_aggregated'               => [self::HAS_MANY, 'PerformanceAggregated', 'sim_id'],
            'stress_points'                        => [self::HAS_MANY, 'StressPoint', 'sim_id'],
            'assessment_points'                    => [self::HAS_MANY, 'AssessmentPoint', 'sim_id'],
            'assessment_planing_points'            => [self::HAS_MANY, 'AssessmentPlaningPoint', 'sim_id'],
            'assessment_calculation'               => [self::HAS_MANY, 'AssessmentCalculation', 'sim_id'],
            'time_management_aggregated'           => [self::HAS_MANY, 'TimeManagementAggregated', 'sim_id'],
            'simulation_excel_points'              => [self::HAS_MANY, 'SimulationExcelPoint', 'sim_id'],
            'assessment_overall'                   => [self::HAS_MANY, 'AssessmentOverall', 'sim_id', 'order'=>'id ASC'],
            'game_type'                            => [self::BELONGS_TO, 'Scenario', 'scenario_id'],
            'learning_area'                        => [self::HAS_MANY, 'SimulationLearningArea', 'sim_id'],
            'learning_goal'                        => [self::HAS_MANY, 'SimulationLearningGoal', 'sim_id'],
            'learning_goal_group'                  => [self::HAS_MANY, 'SimulationLearningGoalGroup', 'sim_id'],
            'invite'                               => [self::HAS_ONE, 'Invite', 'simulation_id'],
            'simFlags'                             => [self::HAS_MANY, 'SimulationFlag', 'sim_id'],
            'logAssessment214g'                    => [self::HAS_MANY, 'LogAssessment214g', 'sim_id'],
            'mail_box_outbox'                      => [self::HAS_MANY, 'MailBox', 'sim_id', 'condition'=>'mail_box_outbox.group_id = 2 or mail_box_outbox.group_id = 3'],
            //''                                => [self::HAS_MANY, 'MailBox', 'sim_id', 'condition'=>'mail_box_inbox.type = 1 or mail_box_inbox.type = 3'],
        ];
    }

    /**
     * Data for log after simulation table
     *
     * May return point value full sums for positive, negative, personal scale,
     *
     * @return array
     */
    public function getAssessmentSumByScale()
    {
        $assessmentPoints = $this->assessment_aggregated;
        $result = [];
        foreach ($assessmentPoints as $assessmentPoint) {
            if (!isset($result[$assessmentPoint->point->type_scale])) {
                $result[$assessmentPoint->point->type_scale] = 0;
            }
            $typeScale = $assessmentPoint->point->type_scale;
            $result[$typeScale] += $assessmentPoint->value;
        }

        // round to make precession predictable for selenium tests
        foreach ($result as $key => $value) {
            $result[$key] = round($result[$key], 3);
        }

        return $result;
    }

    /**
     * @return array of AssessmentPoint
     */
    public function getAssessmentPointsByScale()
    {
        $result = [
            HeroBehaviour::TYPE_ID_POSITIVE => 0,
            HeroBehaviour::TYPE_ID_NEGATIVE => 0,
            HeroBehaviour::TYPE_ID_PERSONAL => 0,
        ];

        // count heroBehavour "1" & "0" {

        $ones = [];
        $count = [];
        $points = [];

        foreach ($this->assessment_points as $assessmentPoint) {
            // save used heroBehavours
            $points[$assessmentPoint->point->code] = $assessmentPoint->point;

            // count "1"
            if (false == isset($ones[$assessmentPoint->point->code])) {
                $ones[$assessmentPoint->point->code] = 0;
            }
            $ones[$assessmentPoint->point->code] += $assessmentPoint->value;

            // count total
            if (false == isset($count[$assessmentPoint->point->code])) {
                $count[$assessmentPoint->point->code] = 0;
            }
            $count[$assessmentPoint->point->code]++;
        }
        // count heroBehavour "1" & "0" }

        // calculate mark by scale
        foreach ($points as $point) {
            if ($point->isNegative()) {
                $result[$point->type_scale] += $ones[$point->code]*$point->scale;
            } else {
                $result[$point->type_scale] += ($ones[$point->code]/$count[$point->code])*$point->scale;
            }
        }

        // round to make precession predictable for selenium tests
        foreach ($result as $key => $value) {
            $result[$key] = round($result[$key], 3);
        }

        return $result;
    }


    /**
     * Выбрать по идентификатору
     * @deprecated
     * @param int $id
     * @return Simulation
     */
//    public function byId($id)
//    {
//        $this->getDbCriteria()->mergeWith(array(
//            'condition' => 'id = ' . (int)$id
//        ));
//        return $this;
//    }

    /**
     * Используется в dev режиме для выявления багов логирования
     */
    public function checkLogs()
    {
        if (Yii::app()->params['public']['isUseStrictAssertsWhenSimStop']) {
            $this->checkMailLogs();
            $this->checkDialogLogs();
            $this->checkActivityLogs();
            $this->checkWindowLogs();
            $this->checkActivityAggregatedLogs();
            $this->checkUniversalLogs();
            $this->checkDayPlan();
        }
    }

    /**
     * @throws Exception
     */
    public function checkWindowLogs()
    {
        $unixtime = 0;
        foreach ($this->log_windows as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty window end time WindowLogs");
            }
            if ($unixtime && ($unixtime + 3 * $this->getSpeedFactor() < strtotime($log->start_time))) {
                throw new Exception("Time gap WindowLogs");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap WindowLogs");
            }
            $unixtime = strtotime($log->end_time);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function checkActivityLogs()
    {
        $unixtime = 0;
        $total = 0;
        foreach ($this->log_activity_actions as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty activity end time ActivityLogs");
            }
            if ($unixtime && ($unixtime + 3 * $this->getSpeedFactor() < strtotime($log->start_time))) {
                throw new Exception("Time gap ActivityLogs");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap ActivityLogs");
            }
            $unixtime = strtotime($log->end_time);
            $total += $unixtime - strtotime($log->start_time);

            if (empty($start)) {
                $start = strtotime($log->start_time);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function checkActivityAggregatedLogs()
    {
        $unixtime = 0;
        $total = 0;
        foreach ($this->log_activity_actions_aggregated as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty activity end time ActivityAggregatedLogs");
            }
            if ($unixtime && ($unixtime + 3 * $this->getSpeedFactor() < strtotime($log->start_time))) {
                throw new Exception("Time gap ActivityAggregatedLogs");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap ActivityAggregatedLogs");
            }
            $unixtime = strtotime($log->end_time);
            $total += $unixtime - strtotime($log->start_time);

            if (empty($start)) {
                $start = strtotime($log->start_time);
            }
        }


    }

    /**
     * @return array
     * @throws Exception
     */
    public function checkDialogLogs()
    {
        $unixtime = 0;
        foreach ($this->log_dialogs as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty end time DialogLogs");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap DialogLogs");
            }
            $unixtime = strtotime($log->end_time);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function checkMailLogs()
    {
        $unixtime = 0;
        foreach ($this->log_mail as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty mail end time for " . $log->primaryKey." MailLogs");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap MailLogs");
            }
            $unixtime = strtotime($log->end_time);
        }
    }

    /**
     * @throws Exception
     */
    public function checkUniversalLogs()
    {
        $unixtime = 0;
        $total = 0;
        foreach ($this->universal_log as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty activity end time UniversalLogs");
            }
            if ($unixtime && ($unixtime + 3 * $this->getSpeedFactor() < strtotime($log->start_time))) {
                throw new Exception("Time gap UniversalLogs");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap UniversalLogs");
            }
            $unixtime = strtotime($log->end_time);
            $total += $unixtime - strtotime($log->start_time);

            if (empty($start)) {
                $start = strtotime($log->start_time);
            }
        }


    }

    /**
     * Shows is simulation run in develop mode (or promotion)
     *
     * @return boolean
     */
    public function isDevelopMode() {
        return self::MODE_DEVELOPER_ID == $this->mode;
    }

    /**
     * @param null $userId
     * @return CActiveDataProvider
     */
    public function search($userId = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('user_id', $userId ?: $this->user_id);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'id',
                'sortVar' => 'sort',
                'attributes' => [
                    'id'
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
                'pageVar' => 'page'
            ]
        ]);
    }

    /**
     * @return array|mixed|null
     */
    public function getOverall() {
        $assessment = AssessmentOverall::model()->findByAttributes([
            'sim_id'=>$this->id,
            'assessment_category_code' => AssessmentCategory::OVERALL
        ]);
        if(null === $assessment){
            return null;
        }else{
            return $assessment->value;
        }
    }

    /**
     * @return array|mixed|null
     */
    public function getPercentile() {
        $assessment = AssessmentOverall::model()->findByAttributes([
            'sim_id' => $this->id,
            'assessment_category_code' => AssessmentCategory::PERCENTILE
        ]);
        if(null === $assessment){
            return null;
        }else{
            return $assessment->value;
        }
    }

    public function mergeAssessment($empty, $current) {
        $new = [];
        foreach($empty as $key => $item) {
            if(isset($current[$key])){
                $new[$key] = $current[$key];

            } else {
                $new[$key] = $item;
            }
        }
    }

    private function _merge_recursive($empty, $current, $new) {
        foreach($empty as $key => $item) {
            if(isset($current[$key])){
                $new[$key] = $current[$key];
            } else {
                $new[$key] = $item;
            }
        }
    }

}


