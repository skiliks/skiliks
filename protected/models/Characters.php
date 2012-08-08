<?php


/**
 * Модель персонажей
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Characters extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return Characters
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
            return 'characters';
    }
    
    public function byIds($ids)
    {
        $list = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$list})"
        ));
        return $this;
    }
}

?>
