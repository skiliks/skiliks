<?php



/**
 * Модель правил флагов
 *
 * Связана с моделями: Dialogs.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsRulesModel extends CActiveRecord
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var string
     */
    private $rule_name;
    
    /**
     * Record id, related to Dialog MySQL id
     * @var integer
     */
    private $rec_id;
    
    /**
     * @var integer
     */
    private $step_number;
    
    /**
     * @var integer
     */
    private $replica_number;
    
    // -----------------------------------------------------------------------------------------------------------------
    
    /**
     *
     * @param type $className
     * @return FlagsRulesModel 
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
            return 'flags_rules';
    }
    
    /**
     * Выбрать по именим правила
     * @param string $name
     * @return FlagsRulesModel 
     */
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "rule_name = '{$name}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать по номеру шага
     * @param int $stepNumber
     * @return FlagsRulesModel 
     */
    public function byStepNumber($stepNumber)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "step_number = {$stepNumber}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по номеру реплики
     * @param int $replicaNumber
     * @return FlagsRulesModel 
     */
    public function byReplicaNumber($replicaNumber)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "replica_number = {$replicaNumber}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по номеру записи
     * @param int $recordId
     * @return FlagsRulesModel 
     */
    public function byRecordIdOrNull($recordId)
    {
        if (null !== $recordId) {
            $this->getDbCriteria()->mergeWith(array(
                'condition' => "rec_id = {$recordId} OR rec_id is null"
            ));
        } else {
            $this->getDbCriteria()->mergeWith(array(
                'condition' => "rec_id is null"
            ));
        }
        return $this;
    }
    
    // -------------------------------------------------------------------------
    
    public function getEventCode()
    {
        return (string)$this->rule_name;
    }
    
    /**
     * @param string $eventCode
     * 
     * @return \FlagsRulesModel
     */
    public function setEventCode($eventCode)
    {
        $this->rule_name = $eventCode;
        return $this;
    }
    
    public function getRecordId()
    {
        return (int)$this->rec_id;
    }
    
    /**
     * @param null|integer $recordId
     * 
     * @return \FlagsRulesModel
     */
    public function setRecordId($recordId)
    {
        $this->rec_id = (int)$recordId;
        return $this;
    }
    
    public function getStepNo()
    {
        return (int)$this->step_number;
    }
    
    /**
     * @param integer $stepNo
     * 
     * @return \FlagsRulesModel
     */
    public function setStepNo($stepNo)
    {
        $this->step_number = (int)$stepNo;
        return $this;
    } 
    
    public function getReplicaNo()
    {
        return (int)$this->step_number;
    }
    
    /**
     * @param integer $replicaCode
     * 
     * @return \FlagsRulesModel
     */
    public function setReplicaNo($replicaNo)
    {
        $this->replica_number = $replicaNo;
        return $this;
    }
    
    /**
     * @param integer $id
     * 
     * @return \FlagsRulesModel
     */
    public function getId()
    {
        return (int)$this->id;
    }
}

