<?php



/**
 * Хранит значение оценки поведения для конкретного диалога.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ReplicaPoint extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * dialog.id
     * @var integer
     */
    public $dialog_id;
    
    /**
     * characters_points.id
     * @var integer
     */
    public $point_id;
    
    /**
     * @var integer
     */
    public $add_value;
    
    /* ----------------------------------- */
    
    /**
     *
     * @param type $className
     * @return ReplicaPoint
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
    
    /**
     * @param string $ids
     * @return array of ReplicaPoint
     */
    public function byIdsNotIn($ids)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " `id` NOT IN ({$ids}) "
        ));
        return $this;
    }
    
    /**
     * Выборка оценки по конкретному диалогу
     * @param int $dialogId идентификатор диалога
     * @return ReplicaPoint
     */
    public function byDialog($dialogId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "dialog_id = '{$dialogId}'"
        ));
        return $this;
    }
    
    /**
     * Выборка по идентификатору оценки
     * @param int $pointId
     * @return ReplicaPoint
     */
    public function byPoint($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = '{$pointId}'"
        ));
        return $this;
    }
}


