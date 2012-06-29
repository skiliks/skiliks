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
    
    public function byBrench($brenchId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'brench_id = '.$brenchId
        ));
        return $this;
    }
}

?>
