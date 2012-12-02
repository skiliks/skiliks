<?php


/**
 * Модель персонажей. Хранит информацию о персонажах.
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
    
    /**
     * Ограничивает выборку па заданному набору персонажей
     * @param array $ids набор персонажей
     * @return Characters 
     */
    public function byIds($ids)
    {
        $list = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$list})"
        ));
        return $this;
    }
    
    /**
     * Ограничивает выборку по коду персонажа
     * @param string $code
     * @return Characters 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * Выборка по конкретному коду персонажа.
     * @param int $id идентификатор персонажа
     * @return Characters 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
}


