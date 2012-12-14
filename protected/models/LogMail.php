<?php
/**
 * @property integer id
 * @property integer sim_id
 * @property integer mail_id
 * @property integer window
 * @property datetime start_time
 * @property datetime end_time
 */
class Logmail extends CActiveRecord 
{
    public $id;
    
    public $sim_id;
    
    public $mail_id;
    
    public $window;
    
    public $start_time;
    
    public $end_time;
    
    public $mail_task_id;
    
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
    
    public function bySimId($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    public function byWindow($v)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "window = {$v}"
        ));
        return $this;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_mail';
    }
}
