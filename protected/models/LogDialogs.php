<?php
/**
 * @property integer id
 * @property integer sim_id
 * @property integer dialog_id
 * @property datetime start_time
 * @property datetime end_time
 * @property integer last_id
 */
class LogDialogs extends CActiveRecord
{
    public $id;
    
    public $sim_id;
    
    public $mail_id;
    
    public $dialog_id;
    
    public $start_time;
    
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

    protected function afterSave()
    {
        $activity_action = ActivityAction::model()->findByPriority(array('dialog_id' => $this->dialog_id));
        if (null !== $activity_action) {
            $activity_action->appendLog($this);
        }
        parent::afterSave();
    }

    public function relations()
    {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_dialogs';
    }
    
}
