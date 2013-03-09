<?php

/**
 * Модель симуляции.
 *
 * @property int difficulty
 * @property SimulationCompletedParent[] completed_parent_activities
 * @property AssessmentAggregated[] assessment_points
 * @property LogWindow[] log_windows
 * @property LogActivityAction[] log_activity_actions
 * @property LogMail[] log_mail
 * @property LogDialog[] log_dialogs
 * @property SimulationMailPoint[] simulation_mail_points
 * @property LogDialogPoint[] assessment_dialog_points
 * @property LogDocument[] log_documents
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
    
    /**
     * @var integer
     */
    public $id;
    
    /**
     * character.id
     * @var integer
     */
    public $user_id;   
    
    /**
     * @var integer
     */
    public $status;
    
    /**
     * real time, Unix age seconds
     * @var integer
     */
    public $start;
    
    /**
     * real time, Unix age seconds
     * @var integer
     */
    public $end; 
    
    /**
     * @var integer
     */
    public $difficulty;
    
    /**
     * @var integer
     */
    public $type; // 1 - promotion mode (for users), 2 - develop mode (to debug)
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public static function formatDateForMissedCalls($time, $date = self::SIMULATION_DAY_DATE) {
        return $date.' | '.$time;
    }


    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return Simulation
     */
    public static function model($className=__CLASS__)
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
            'condition' => 'user_id = '.(int)$uid
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
    public function getGameTime() {
        if (!$this) throw new Exception('Не могу определить симуляцию');

        $variance = GameTime::getUnixDateTime(GameTime::setNowDateTime()) - GameTime::getUnixDateTime($this->start);
        $variance = $variance * Yii::app()->params['public']['skiliksSpeedFactor'];

        $startTime = explode(':', Yii::app()->params['public']['simulationStartTime']);
        $unixtimeMins = round($variance/60) + $startTime[0] * 60 + $startTime[1];
        return gmdate('H:i:s', $unixtimeMins*60);
    }

    public function deleteOldTriggers($newHours, $newMinutes) {
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
            if (GameTime::timeToSeconds($event_trigger->trigger_time) < ($newHours*60 + $newMinutes)*60) {
                $event_trigger->delete();
            }
        }
    }

    public function relations()
    {
        return [
            'user'                              => [self::BELONGS_TO, 'Users', 'user_id'],
            'events_triggers'                   => [self::HAS_MANY, 'EventTrigger', 'sim_id'],
            'log_windows'                       => [self::HAS_MANY, 'LogWindow', 'sim_id', 'order' => 'start_time, end_time'],
            'log_mail'                          => [self::HAS_MANY, 'LogMail', 'sim_id', 'order' => 'start_time, end_time'],
            'log_plan'                          => [self::HAS_MANY, 'DayPlanLog', 'sim_id', 'order' => 'start_time, end_time'],
            'log_dialogs'                       => [self::HAS_MANY, 'LogDialog', 'sim_id', 'order' => 'start_time, end_time'],
            'log_documents'                       => [self::HAS_MANY, 'LogDocument', 'sim_id', 'order' => 'start_time, end_time'],
            'log_activity_actions'              => [self::HAS_MANY, 'LogActivityAction', 'sim_id', 'order' => 'start_time, end_time'],
            'log_activity_actions_aggregated'   => [self::HAS_MANY, 'LogActivityActionAgregated', 'sim_id', 'order' => 'start_time, end_time'],
            'universal_log'                     => [self::HAS_MANY, 'UniversalLog', 'sim_id', 'order' => 'start_time, end_time'],
            'completed_parent_activities'       => [self::HAS_MANY, 'SimulationCompletedParent', 'sim_id'],
            'assessment_points'                 => [self::HAS_MANY, 'AssessmentAggregated', 'sim_id', 'with' => 'point',  'order' => 'point.type_scale'],
            'simulation_assessment_rules'       => [self::HAS_MANY, 'SimulationAssessmentRule', 'sim_id'],
            'assessment_dialog_points'          => [self::HAS_MANY, 'LogDialogPoint', 'sim_id'],
            'simulation_mail_points'            => [self::HAS_MANY, 'SimulationMailPoint', 'sim_id'],
        ];
    }

    public function getAssessmentResults()
    {
        $assessmentPoints = $this->assessment_points;
        $result = [];
        foreach ($assessmentPoints as $assessmentPoint) {
            if (!isset($result[$assessmentPoint->point->type_scale])) {
                $result[$assessmentPoint->point->type_scale] = 0;
            }
            $typeScale = $assessmentPoint->point->type_scale;
            $result[$typeScale] += $assessmentPoint->value;
        }

        return $result;
    }

    public function getAssessmentRules()
    {
        $assessmentRules = $this->simulation_assessment_rules;
        return $assessmentRules;
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
            'condition' => 'id = '.(int)$id
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
        return self::MODE_DEVELOPER_ID == $this->type;
    }

    /**
     *
     */
    public function getAssessmentPointDetails()
    {
        return array_merge(
            $this->simulation_mail_points,
            $this->assessment_dialog_points,
            # Todo handle this shit
            LogHelper::getMailPointsDetail(LogHelper::RETURN_DATA, ['sim_id' => $this->primaryKey])['data']
        );
    }
}


