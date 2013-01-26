<?php



/**
 * По сути справочник целей поведения. Хранит код, название, scale.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitles extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * characters_points_titles.id
     * @var integer
     */
    public $parent_id;
    
    /**
     * @var string
     */
    public $code;
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var float
     */
    public $scale;  
    
    /**
     * 1 - positive
     * 2 -negative
     * 3 - personal
     * 
     * @var integer
     */
    public $type_scale;
    
    const TYPE_POSITIVE = 1;
    const TYPE_NEGATIVE = 2;
    const TYPE_PERSONAL  = 3;
    
    /* ------------------------------------------------------------*/
    
    /**
     * @return boolean
     */
    public function isPositive() 
    {
        return (self::TYPE_POSITIVE == $this->type_scale);
    }
    
    /**
     * @return boolean
     */
    public function isNegative() 
    {
        return (self::TYPE_NEGATIVE == $this->type_scale);
    }
    
    /**
     * @return boolean
     */
    public function isPersonal() 
    {
        return (self::TYPE_PERSONAL == $this->type_scale);
    }


    /* ------------------------------------------------------------*/
    
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
      * @return CharactersPointsTitles 
     */
    public function byIsBehaviour()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'parent_id IS NOT NULL'
        ));
        return $this;
    }
    
    /**
     * @return CharactersPointsTitles 
     */
    public function negative()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'type_scale = 2'
        ));
        return $this;
    }
    
    /**
     * @return CharactersPointsTitles 
     */
    public function positive()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'type_scale = 1'
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


