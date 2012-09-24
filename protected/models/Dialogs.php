<?php



/**
 * Модель диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Dialogs extends CActiveRecord{
    
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
    
    public function byBranch($branchId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'branch_id = '.$branchId
        ));
        return $this;
    }
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array('condition' => 'id = '.$id));
        return $this;
    }
    
    public function byCodeAndStepNumber($code, $stepNumber)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}' and step_number = {$stepNumber}"
        ));
        return $this;
    }
    
    public function byStepNumber($stepNumber)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "step_number = {$stepNumber}"
        ));
        return $this;
    }
    
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
}

?>
