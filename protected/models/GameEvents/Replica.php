<?php
/**
 * Модель диалогов. Хранит реплики диалогов и связь диалогов с событиями.
 *
 * @property integer $id
 * @property integer $ch_from, characters.id
 * @property integer $ch_to, characters.id
 * @property integer $dialog_subtype, dialog_subtypes
 * @property string  $text
 * @property string  $code, dialog code 'ET1.1', 'E8' ...
 * @property string  $next_event_code, 'ET1.1', 'D1', 'P1', 'M1', 'MS2'
 * @property string  $next_event
 * @property string  $sound
 * @property string  $type_of_init, Replica initialization type: dialog, icon, time, flex etc.
 * @property string  $flag_to_switch
 * @property string  $flag_to_switch_2
 * @property integer $event_result - remove it!
 * @property integer $step_number, step in dialog
 * @property integer $replica_number, number of replica in dialog step
 * @property integer $delay
 * @property integer $excel_id
 * @property boolean $fantastic_result
 * @property boolean $is_final_replica
 * @property boolean $demo
 * @property integer $duration
 *
 * @property Character from_character
 * @property Character to_character
 * @property ActivityParent[] termination_parent_actions
 * @property DialogSubtype $dialogSubtype
 *
 */
class Replica extends CActiveRecord implements IGameAction
{

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return Replica
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @todo; fix this dirty trick
     * probebly we need aliaces for dialog_subtype instead of ids
     * @return bool
     */
    public function isEvent()
    {
        return (1 === (int)$this->dialog_subtype || 5 === (int)$this->dialog_subtype);
    }
    
    public function isPhoneCall()
    {
        return (1 === (int)$this->dialog_subtype);
    }    

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'replica';
    }
    
    // old function
    public function byBranch($branchId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'branch_id = '.$branchId
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному идентификатору диалога
     * @param int $id
     * @return Replica
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array('condition' => 'id = '.$id));
        return $this;
    }

    /**
     * Выбрать по коду и номеру шага.
     * @param string $code
     * @param int $stepNumber
     * @return Replica
     */
    public function byCodeAndStepNumber($code, $stepNumber)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}' and step_number = {$stepNumber}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по номеру шага
     * @param int $stepNumber
     * @return Replica
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
     * @return Replica
     */
    public function byReplicaNumber($replicaNumber)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "replica_number = {$replicaNumber}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по коду диалога
     * @param string $code
     * @return Replica
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать по полю excel_id - это исходный номер из эксель документа
     * @param int $excelId
     * @return Replica
     */
    public function byExcelId($excelId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "excel_id = '{$excelId}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать реплики для демо режима
     * @param int $simulationType
     * @return Replica
     */
    public function byDemo($simulationType)
    {
        if ($simulationType == 1) {  //
            $this->getDbCriteria()->mergeWith(array(
                'condition' => "demo = 1"
            ));
        }    
        return $this;
    }

    /**
     * Gets first replica of the dialog
     * @param $code
     * @return Replica
     */
    public function getFirstReplica($code) {
        $criteria = new CDbCriteria();
        $criteria->compare('code', $code);
        $criteria->compare('step_number', 1);
        $criteria->order = 'replica_number';
        return $this->find($criteria);
    }

    public function relations()
    {
        return [
            'from_character'             => [self::BELONGS_TO, 'Character', 'ch_from'],
            'to_character'               => [self::BELONGS_TO, 'Character', 'ch_to'],
            'dialogSubtype'               => [self::BELONGS_TO, 'DialogSubtype', 'dialog_subtype'],
            'termination_parent_actions' => [self::HAS_MANY, 'ActivityParent', 'dialog_id']
        ];
    }
}


