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
     * @param string $className
     * @return LogDialogs
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
        }else{
            throw new CException("The dialogue should have an id");//TODO:Проверить
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
    
    /**
     * @param int $simulationId
     * @return LogDialogs 
     */
    public function bySimulationId($simulationId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simulationId}"
        ));
        return $this;
    }
    
    /**
     * @param int $dialogId
     * @return LogDialogs 
     */
    public function byDialogId($dialogId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "dialog_id = {$dialogId}"
        ));
        return $this;
    }
    
    /**
     * @param string $sort
     * @return LogDialogs 
     */
    public function orderById($sort = 'DESC')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "id $sort"
        ));
        return $this;
    }
    
}
