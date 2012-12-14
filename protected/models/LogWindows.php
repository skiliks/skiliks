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
class LogWindows extends CActiveRecord 
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
    public $winwod;
    
    /**
     * See LogHelper subwindow codes
     * @var integer
     */
    public $sub_winwod;
    
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
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return Characters
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
}
