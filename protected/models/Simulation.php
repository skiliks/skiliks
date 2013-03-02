<?php

/**
 * Модель симуляции.
 *
 * @property int difficulty
 * @property SimulationCompletedParent[] completed_parent_activities
 * @property AssessmentAggregated[] assessment_points
 * @prorepty LogWindow[] log_windows
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Simulation extends CActiveRecord
{
    const SIMULATION_DAY_DATE = '04.10.2012';
    
    const TYPE_PROMOTION = 1;
    const TYPE_DEVELOP   = 2;
    
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

        $start_time = explode(':', Yii::app()->params['public']['simulationStartTime']);
        $unixtimeMins = round($variance/60) + $start_time[0] * 60 + $start_time[1];
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
            'user'                        => [self::BELONGS_TO, 'Users', 'user_id'],
            'events_triggers'             => [self::HAS_MANY, 'EventTrigger', 'sim_id'],
            'log_windows'                 => [self::HAS_MANY, 'LogWindow', 'sim_id', 'order' => 'start_time'],
            'log_mail'                    => [self::HAS_MANY, 'LogMail', 'sim_id', 'order' => 'start_time'],
            'log_plan'                    => [self::HAS_MANY, 'DayPlanLog', 'sim_id', 'order' => 'start_time'],
            'log_dialogs'                 => [self::HAS_MANY, 'LogDialog', 'sim_id', 'order' => 'start_time'],
            'log_activity_actions'        => [self::HAS_MANY, 'LogActivityAction', 'sim_id', 'order' => 'start_time'],
            'completed_parent_activities' => [self::HAS_MANY, 'SimulationCompletedParent', 'sim_id'],
            'assessment_points'           => [self::HAS_MANY, 'AssessmentAggregated', 'sim_id', 'with' => 'point',  'order' => 'point.type_scale'],
            'simulation_assessment_rules' => [self::HAS_MANY, 'SimulationAssessmentRule', 'sim_id'],
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
        /*$result = [];
        foreach ($assessmentRules as $assessmentRule) {
            $result[] = ['activity_id' => $assessmentRule->assessment_rule_id];
        }*/

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
     * Shows is simulation run in develop mode (or promotion)
     * 
     * @return boolean
     */
    public function isDevelopMode() {
        return 2 == $this->type;
    }
}


