<?php



/**
 * Модель диалогов. Хранит реплики диалогов и связь диалогов с событиями.
 *
 * @property Character from_character
 * @property Character to_character
 * @property ActivityParent[] termination_parent_actions
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Replica extends CActiveRecord implements IGameAction
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * characters.id
     * @var integer
     */
    public $ch_from;

    /**
     * characters.id
     * @var integer
     */
    public $ch_to;

    /**
     * dialog_subtypes.id
     * @var integer
     */
    public $dialog_subtype;
    
    /**
     * @var string
     */
    public $text; 

    /**
     * events_results.id
     * @var integer
     */
    public $event_result;   
    
    /**
     * Replica code, 'ET1.1', 'E8' ...
     * @var string
     */
    public $code;
    
    /**
     * @var integer
     */
    public $step_number;
    
    /**
     * @var integer
     */
    public $replica_number;
    
    /**
     * event_samples.id
     * @var integer
     */
    public $next_event;
    
    /**
     * In game minutes
     * Delay after event starts
     * @var integer
     */
    public $delay;    
    
    /**
     * @var boolean (integer)
     */
    public $is_final_replica;
    
    /**
     * name of sound file
     * @var string
     */
    public $sound;
    
    /**
     * ID in source excel document, used to define dialod line in reimport case
     * @var integer
     */
    public $excel_id;
    
    /**
     * "D1", "E1.2" ...
     * @var string
     */
    public $next_event_code;    

    /**
     * Is this replica used in demo
     * @var boolean (integer)
     */
    public $demo;    
    
    /**
     * Replica initialization type: dialog, icon, time, flex etc.
     * @var string
     */
    public $type_of_init;

    /**
     * "F1", "F2", ...
     * @var string | NULL
     */
    public $flag_to_switch;
    
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
     * @param string $ids
     * @return array of \Dialogs
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT IN ({$ids}) "
        ));
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
            'termination_parent_actions' => [self::HAS_MANY, 'ActivityParent', 'dialog_id']
        ];
    }
}


