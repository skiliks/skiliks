<?php



/**
 * Модель диалогов. Хранит реплики диалогов и связь диалогов с событиями.
 * 
 * Связана с моделями: Characters, CharactersStates, DialogSubtypes, EventsResults, EventsSamples.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Dialogs extends CActiveRecord{
    
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
}

?>
