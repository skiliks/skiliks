<?php

/**
 * Какие звонки и когда были совершены в рамках симуляции
 * 
 * Связана с моделями:  Simulations, Characters.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneCallsModel extends CActiveRecord
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
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return PhoneCallsModel 
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
    
    /**
     * Выбрать согласно заданной симуляции
     * @param int $simId
     * @return PhoneCallsModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
}


