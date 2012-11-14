<?php



/**
 * По сути справочник целей поведения. Хранит код, название, scale.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitles extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return CharactersPointsTitles 
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
            return 'characters_points_titles';
    }
    
    /**
     * Выбрать записи, у которых нет родителей
     * @return CharactersPointsTitles 
     */
    public function withoutParents()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'parent_id is null'
        ));
        return $this;
    }
    
    /**
     * Выбрать цель, у которой есть родители
     * @return CharactersPointsTitles 
     */
    public function withParents()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'parent_id > 0'
        ));
        return $this;
    }
    
    /**
     * Выборка цели по коду.
     * @param string $code
     * @return CharactersPointsTitles 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '$code'"
        ));
        return $this;
    }
}

?>
