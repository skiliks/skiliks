<?php



/**
 * Модель диалогов. Хранит реплики диалогов и связь диалогов с событиями.
 * 
 * Связана с моделями: Characters, CharactersStates, DialogSubtypes, EventsResults, EventsSamples.
 *
 * @property Characters from_character
 * @property Characters to_character
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Dialogs extends CActiveRecord
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
     * characters_states.id
     * @var integer
     */
    public $ch_from_state;    
    
    /**
     * characters.id
     * @var integer
     */
    public $ch_to;
    
    /**
     * characters_states.ids
     * @var integer
     */
    public $ch_to_state;
    
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
     * Dialog code, 'ET1.1', 'E8' ...
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
     * "F1", "F2", ...
     * @var string
     */
    public $flag; 
    
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
     * @var string | NULL
     */
    public $flag_to_swith;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return Dialogs 
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
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
            return 'dialogs';
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
     * @return Dialogs 
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
     * @return Dialogs 
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
     * @return Dialogs 
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
     * @return Dialogs 
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
     * @return Dialogs 
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
     * @return Dialogs 
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
     * @return Dialogs 
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

    public function relations()
    {
        return [
            'from_character' => [self::BELONGS_TO, 'Characters', 'ch_from'],
            'to_character' => [self::BELONGS_TO, 'Characters', 'ch_to'],
        ];
    }
}


