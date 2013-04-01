<?php

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
 * @property string managerial_skills
 * @property string managerial_productivity
 * @property string time_management_effectiveness
 * @property string overall_manager_rating
 *
 * @property SimulationCompletedParent[] completed_parent_activities
 * @property AssessmentAggregated[] assessment_aggregated
 * @property LogWindow[] log_windows
 * @property LogActivityAction[] log_activity_actions
 * @property LogActivityActionAgregated[] log_activity_actions_aggregated
 * @property LogMail[] log_mail
 * @property LogDialog[] log_dialogs
 * @property AssessmentCalculation[] assessment_calculation
 * @property AssessmentPoint[] assessment_points
 * @property LogDocument[] log_documents
 * @property DayPlanLog[] log_day_plan
 * @property SimulationExcelPoint[] simulation_excel_points
 * @property PerformancePoint[] performance_points
 * @property StressPoint[] stress_points
 *
 * @author Sergey Suzdaltsev, мать его <sergey.suzdaltsev@gmail.com>
 */
class Simulation extends CActiveRecord
{
    const SIMULATION_DAY_DATE = '04.10.2012';
    
    const MODE_PROMO_ID       = 1;
    const MODE_DEVELOPER_ID   = 2;

    const MODE_PROMO_LABEL     = 'promo';
    const MODE_DEVELOPER_LABEL = 'developer';

    const TYPE_FULL = 1;
    const TYPE_LITE = 2;

    public static $typeLabel = [
        self::TYPE_FULL => 'full',
        self::TYPE_LITE => 'lite'
    ];

    public $id;

    /** ------------------------------------------------------------------------------------------------------------ **/

    public static function formatDateForMissedCalls($time, $date = self::SIMULATION_DAY_DATE)
    {
        return $date . ' | ' . $time;
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
     */
    public function getGameTime()
    {
        $variance = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($this->start) - $this->skipped;
        $variance = $variance * Yii::app()->params['public']['skiliksSpeedFactor'];

        $startTime = explode(':', Yii::app()->params['simulation'][$this->getTypeLabel()]['start']);
        $unixtime = $variance + $startTime[0] * 3600 + $startTime[1] * 60;
        return gmdate('H:i:s', $unixtime);
    }

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
            'user'                            => [self::BELONGS_TO, 'YumUser', 'user_id'],
            'events_triggers'                 => [self::HAS_MANY, 'EventTrigger', 'sim_id'],
            'log_windows'                     => [self::HAS_MANY, 'LogWindow', 'sim_id', 'order' => 'start_time, end_time'],
            'log_mail'                        => [self::HAS_MANY, 'LogMail', 'sim_id', 'order' => 'start_time, end_time'],
            'log_plan'                        => [self::HAS_MANY, 'DayPlanLog', 'sim_id', 'order' => 'start_time, end_time'],
            'log_dialogs'                     => [self::HAS_MANY, 'LogDialog', 'sim_id', 'order' => 'start_time, end_time'],
            'log_documents'                   => [self::HAS_MANY, 'LogDocument', 'sim_id', 'order' => 'start_time, end_time'],
            'log_activity_actions'            => [self::HAS_MANY, 'LogActivityAction', 'sim_id', 'order' => 'start_time, end_time'],
            'log_day_plan'                    => [self::HAS_MANY, 'DayPlanLog', 'sim_id'],
            'log_activity_actions_aggregated' => [self::HAS_MANY, 'LogActivityActionAgregated', 'sim_id', 'order' => 'start_time, end_time'],
            'universal_log'                   => [self::HAS_MANY, 'UniversalLog', 'sim_id', 'order' => 'start_time, end_time'],
            'completed_parent_activities'     => [self::HAS_MANY, 'SimulationCompletedParent', 'sim_id'],
            'assessment_aggregated'           => [self::HAS_MANY, 'AssessmentAggregated', 'sim_id', 'with' => 'point', 'order' => 'point.type_scale'],
            'performance_points'              => [self::HAS_MANY, 'PerformancePoint', 'sim_id'],
            'stress_points'                   => [self::HAS_MANY, 'StressPoint', 'sim_id'],
            'assessment_points'               => [self::HAS_MANY, 'AssessmentPoint', 'sim_id'],
            'assessment_planing_points'       => [self::HAS_MANY, 'AssessmentPlaningPoint', 'sim_id'],
            'assessment_calculation'          => [self::HAS_MANY, 'AssessmentCalculation', 'sim_id'],
            'simulation_excel_points'         => [self::HAS_MANY, 'SimulationExcelPoint', 'sim_id'],
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

    public function getAssessmentPointsByScale()
    {
        $result = [
            HeroBehaviour::TYPE_POSITIVE => 0,
            HeroBehaviour::TYPE_NEGATIVE => 0,
            HeroBehaviour::TYPE_PERSONAL => 0,
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
            if (HeroBehaviour::TYPE_NEGATIVE == $point->type_scale) {
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
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'id = ' . (int)$id
        ));
        return $this;
    }

    public function checkLogs()
    {
        $this->checkMailLogs();
        $this->checkDialogLogs();
        $this->checkActivityLogs();
        $this->checkWindowLogs();
        $this->checkActivityAggregatedLogs();
        $this->checkUniversalLogs();
    }

    public function checkWindowLogs()
    {
        $unixtime = 0;
        foreach ($this->log_windows as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty window end time");
            }
            if ($unixtime && ($unixtime + Yii::app()->params['public']['skiliksSpeedFactor'] < strtotime($log->start_time))) {
                throw new Exception("Time gap");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap");
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
                throw new Exception("Empty activity end time");
            }
            if ($unixtime && ($unixtime + 10 < strtotime($log->start_time))) {
                throw new Exception("Time gap");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap");
            }
            $unixtime = strtotime($log->end_time);
            $total += $unixtime - strtotime($log->start_time);

            if (empty($start)) {
                $start = strtotime($log->start_time);
            }
        }

        if (isset($log, $start)) {
            if (abs($unixtime - $start - $total) > 2 * Yii::app()->params['public']['skiliksSpeedFactor']) {
                throw new Exception("Time difference is too big");
            }
        }
    }

    public function checkActivityAggregatedLogs()
    {
        $unixtime = 0;
        $total = 0;
        foreach ($this->log_activity_actions_aggregated as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty activity end time");
            }
            if ($unixtime && ($unixtime + 10 < strtotime($log->start_time))) {
                throw new Exception("Time gap");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap");
            }
            $unixtime = strtotime($log->end_time);
            $total += $unixtime - strtotime($log->start_time);

            if (empty($start)) {
                $start = strtotime($log->start_time);
            }
        }

        if (isset($log, $start)) {
            if (abs($unixtime - $start - $total) > 2 * Yii::app()->params['public']['skiliksSpeedFactor']) {
                throw new Exception("Time difference is too big");
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
                throw new Exception("Empty end time");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap");
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
                throw new Exception("Empty mail end time for " . $log->primaryKey);
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap");
            }
            $unixtime = strtotime($log->end_time);
        }
    }

    public function checkUniversalLogs()
    {
        $unixtime = 0;
        $total = 0;
        foreach ($this->universal_log as $log) {
            if (!$log->end_time || $log->end_time == '00:00:00') {
                throw new Exception("Empty activity end time");
            }
            if ($unixtime && ($unixtime + 10 < strtotime($log->start_time))) {
                throw new Exception("Time gap");
            }
            if ($unixtime > strtotime($log->start_time)) {
                throw new Exception("Time overlap");
            }
            $unixtime = strtotime($log->end_time);
            $total += $unixtime - strtotime($log->start_time);

            if (empty($start)) {
                $start = strtotime($log->start_time);
            }
        }

        if (isset($log, $start)) {
            if (abs($unixtime - $start - $total) > 2 * Yii::app()->params['public']['skiliksSpeedFactor']) {
                throw new Exception("Time difference is too big");
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
     * @return string
     */
    public function getTypeLabel()
    {
        return self::$typeLabel[$this->type];
    }

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

    public function getLastSimulation($user) {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "user_id = :user_id ORDER BY id DESC",
            'params' => ['user_id'=>$user->id],
        ));
        return $this->find();
    }
}


