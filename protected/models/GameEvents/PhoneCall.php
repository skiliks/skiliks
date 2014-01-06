<?php

/**
 * Какие звонки и когда были совершены в рамках симуляции
 *
 * @property integer $id
 * @property integer $sim_id
 * @property integer $call_time, real time, Unix age seconds
 * @property integer $from_id character.id
 * @property integer $to_id character.id
 * @property integer $dialog_code, (dialog_id)
 * @property integer $theme_id
 *
 * Call type:
 * 0 - in call
 * 1 - out call
 * 2 - missed call
 * @property integer $call_type
 */
class PhoneCall extends CActiveRecord
{
    const IN_CALL     = 0;
    const OUT_CALL    = 1;
    const MISSED_CALL = 2;

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param string $className
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