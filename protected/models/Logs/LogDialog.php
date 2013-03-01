<?php
/**
 * @property integer id
 * @property integer sim_id
 * @property integer dialog_id
 * @property datetime start_time
 * @property datetime end_time
 * @property integer last_id
 * @property Replica dialog
 * @property Simulation simulation
 */
class LogDialog extends CActiveRecord
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
     * @return LogDialog
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function afterSave()
    {
        /** @var $activityAction ActivityAction */
        $activityAction = ActivityAction::model()->findByPriority(
            ['dialog_id' => $this->dialog_id],
            NULL,
            $this->simulation
        );

        if ($this->getLastReplica()) {
            $logActivityAction = LogActivityAction::model()->findByAttributes(['start_time' => $this->start_time, 'sim_id' => $this->sim_id]);
                if ($logActivityAction === null) {
                foreach ($this->getLastReplica()->termination_parent_actions as $parentAction) {
                    if (!$parentAction->isTerminatedInSimulation($this->simulation)) {
                        $parentAction->terminateInSimulation($this->simulation);
                    }
                };
            }
        }


        if (null !== $activityAction) {
            $activityAction->appendLog($this);
        }
        parent::afterSave();
    }

    public function relations()
    {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'dialog' => [self::BELONGS_TO, 'Replica', 'dialog_id']
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
     * @return LogDialog
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
     * @return LogDialog
     */
    public function byDialogId($dialogId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "dialog_id = {$dialogId}"
        ));
        return $this;
    }

    public function dump()
    {
        printf("%s %s\n", $this->start_time, $this->last_id);
    }

    /**
     * @param int $replicaId
     * @return LogDialog
     */
    public function byLastReplicaId($replicaId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "last_id = {$replicaId}"
        ));
        return $this;
    }

    /**
     * Returns last replica object
     * @return Replica
     */
    public function getLastReplica()
    {
        return Replica::model()->findByAttributes(['excel_id' => $this->last_id]);
    }
    
    /**
     * @param string $sort
     * @return LogDialog
     */
    public function orderById($sort = 'DESC')
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "id $sort"
        ));
        return $this;
    }
    
}
