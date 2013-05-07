<?php
/**
 * @property integer $id
 * @property integer $sim_id
 * @property integer $mail_id
 * @property integer $window
 * @property string $start_time 'H:i:s'
 * @property string $end_time 'H:i:s'
 * @property string $leg_type
 * @property string $leg_action
 * @property integer $activity_action_id
 * @property string $category
 * @property integer $is_keep_last_category
 *
 * @property Simulation $simulation
 * @property ActivityAction $activityAction
 */
class LogActivityActionAgregated extends CActiveRecord
{
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
            (TimeTools::timeToSeconds($this->end_time) - TimeTools::timeToSeconds($this->start_time))
        );
    }

    /**
     * @return bool
     */
    public function isMail()
    {
        return (in_array($this->leg_type, ['Inbox_leg', 'Outbox_leg']));
    }

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @param string $className
     * @return
     */
    public static function model($className = __CLASS__)
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
