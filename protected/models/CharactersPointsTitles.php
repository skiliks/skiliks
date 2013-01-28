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
     * 2 - negative
     * 3 - personal
     * 
     * @var integer
     */
    public $type_scale;
    
    /**
     * @var string
     */
    public $learning_goal_code;
    
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

    /**
     * Amaizing! Id for property without table in DB.
     * @param string $name
     * @return int|null
     */
    public static  function getCharacterpointScaleId($name) {
        switch ($name) {
            case 'positive'; return 1; break;
            case 'negative'; return 2; break;
            case 'personal'; return 3; break;
        }
        
        return null;
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
    
    /**
     * 
     */
    public function relations()
    {
        return [
            'learning_goal' => [self::BELONGS_TO, 'LearningGoal', 'code'],
        ];
    }
}


