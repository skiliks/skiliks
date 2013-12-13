<?php
/**
 * @property integer $id
 * @property string  $window
 * @property string  $sub_window
 * @property string  $window_uid , md5, windows unique ID - currently used to determine several mail new windows
 * @property string  $start_time , '00:00::00' game time
 * @property string  '00:00::00' , '00:00::00' game time
 * @property integer $sim_id

 */
class LogWindow extends CActiveRecord
{
    public function __toString()
    {
        return sprintf("%s\t%s\t%s\t%s", $this->start_time, $this->end_time, $this->window, $this->window_uid);
    }

    /**
     * @return string
     */
    public function dump(){
        return $this->__toString() . "\n";
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

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_windows';
    }

    public function relations()
    {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'window_obj' => array(self::BELONGS_TO, 'Window', 'window'),
        );
    }
}
