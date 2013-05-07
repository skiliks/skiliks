<?php

/**
 * Какие звонки и когда были совершены в рамках симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneCall extends CActiveRecord
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
     * real time, Unix age seconds
     * @var integer
     */
    public $call_time; 
    
    const IN_CALL     = 0;
    const OUT_CALL    = 1;
    const MISSED_CALL = 2;
    
    /**
     * 0 - in call
     * 1 - out call
     * 2 - missed call
     * 
     * @var integer
     */
    public $call_type;
    
    /**
     * character.id
     * @var integer
     */
    public $from_id; 
    
    /**
     * character.id
     * @var integer
     */
    public $to_id;

    /**
     * phone_calls.id
     * @var integer
     */
    public $dialog_code;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return PhoneCall
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
            return 'phone_calls';
    }
}


