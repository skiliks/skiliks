<?php



/**
 * Модель симуляции.
 * 
 * Связана с моделями: Users.
 *
 * @property int difficulty
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Simulations extends CActiveRecord
{
    const SIMULATION_DAY_DATE = '10.09.2012';
    
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
     * @return Simulations 
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
     * @return Simulations 
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
     * @return Simulations 
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
            if (GameTime::timeToSeconds($event_trigger->trigger_time) < ($newHours*60 + $newMinutes)*60) {
                $event_trigger->delete();
            }
        }
    }

    public function relations()
    {
        return ['events_triggers' => [self::HAS_MANY, 'EventsTriggers', 'sim_id']];
    }

    /**
     * Выбрать по идентификатору
     * @param int $id
     * @return Simulations 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'id = '.(int)$id
        ));
        return $this;
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


