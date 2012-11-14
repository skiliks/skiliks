<?php



/**
 * Хранит значение оценки поведения для конкретного диалога.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPoints extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return CharactersPoints 
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
            return 'characters_points';
    }
    
    public function byDialog($dialogId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "dialog_id = '{$dialogId}'"
        ));
        return $this;
    }
    
    public function byPoint($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = '{$pointId}'"
        ));
        return $this;
    }
}

?>
