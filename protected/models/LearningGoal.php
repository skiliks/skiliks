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
     * @return HeroBehaviour
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
            return 'learning_goal';
    }
    
    /**
     * Выборка цели по коду.
     * @param string $code
     * @return HeroBehaviour
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '$code'"
        ));
        return $this;
    }

}

