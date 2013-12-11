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
    /**
     * Returns last replica object
     * @return Replica
     */
    public function getLastReplica()
    {
        return Replica::model()->findByAttributes(['excel_id' => $this->last_id]);
    }

    /**
     * ???
     */
    protected function afterSave()
    {
        if ($this->getLastReplica()) {
            foreach ($this->getLastReplica()->termination_parent_actions as $parentAction) {
                if (!$parentAction->isTerminatedInSimulation($this->simulation)) {
                    $parentAction->terminateInSimulation($this->simulation, $this->end_time);
                }
            };
        }

        parent::afterSave();
    }

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

    /**
     * @return array of string
     */
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
}
