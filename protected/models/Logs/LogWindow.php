<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 05.12.12
 * Time: 21:52
 * To change this template use File | Settings | File Templates.
 * @property mixed window
 * @property mixed sub_window
 * @property mixed start_time
 * @property int sim_id
 */
class LogWindow extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;
    
    /**
     * See LogHelper window codes
     * @var integer
     */
    public $window;

    /**
     * '00:00::00' current game day
     * @var string
     */
    public $start_time;
    
    /**
     * '00:00::00' current game day
     * @var string
     */
    public $end_time;

    /**
     * windows unique ID - currently used to determine several mail new windows
     * @var string, md5
     */
    public $window_uid;

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

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_windows';
    }

    protected function afterSave()
    {
        /**
         * @var ActivityAction $activityAction
         */
        $activityAction = ActivityAction::model()->findByPriority(
            ['window_id' => $this->window],
            null,
            $this->simulation
        );
        if ($activityAction !== null) {
            $activityAction->appendLog($this);
        }
        parent::afterSave();
    }

    public function dump(){
        echo $this->__toString() . "\n";
    }

    public function __toString()
    {
        return sprintf("%s\t%s\t%s\t%s", $this->start_time, $this->end_time, $this->window, $this->window_uid);
    }

    public function relations()
    {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'window_obj' => array(self::BELONGS_TO, 'Window', 'window'),
        );
    }
}
