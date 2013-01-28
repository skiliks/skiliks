<?php

/**
 *
 * @author slavka
 */
class LearningGoal extends CActiveRecord
{
    /**
     * @var string
     */
    public $code;
    
    /**
     * @var string
     */
    public $title;
    
    /* ------------------------------------------------------------*/

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
            return 'learning_goals';
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

