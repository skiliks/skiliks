<?php
/**
 * @property integer id
 * @property integer sim_id
 * @property integer mail_id
 * @property integer window
 * @property datetime start_time
 * @property datetime end_time
 * @property Simulation simulation
 * @property ActivityAction activityAction
 */
class LogActivityActionAgregated extends CActiveRecord
{
    public $id;
    
    public $sim_id;
    
    public $leg_type;
    
    public $leg_action;
    
    public $activity_action_id;
    
    public $category;
    
    public $is_keep_last_category;
    
    /**
     * @var string 'hh:ii:ss'
     */
    public $start_time;
    
    /**
     * @var string 'hh:ii:ss'
     */
    public $end_time;
    
    /**
     * @var string 'hh:ii:ss'
     */
    public $duration;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public function updateDuration() {
        $this->duration = TimeTools::secondsToTime(
            (TimeTools::TimeToSeconds($this->end_time) - TimeTools::TimeToSeconds($this->start_time))
        );
    }

    /**
     * @return bool
     */
    public function isMail()
    {
        return (in_array($this->leg_type, ['Inbox_leg', 'Outbox_leg', '']));
    }

    /**
     * @return int
     */
    public function getDurationInSeconds()
    {
        if (null === $this->duration) {
            return 0;
        }

        list($hours, $minutes, $seconds) = explode(':', $this->duration);

        return $hours*60*60 + $minutes*60 + $seconds;
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'activityAction'  => array(self::BELONGS_TO, 'ActivityAction', 'activity_action_id'),
            'simulation'      => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_activity_action_agregated';
    }
}
