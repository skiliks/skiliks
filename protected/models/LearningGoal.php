<?php

/**
 * @param float $max_negative_value
 * @param string $code
 * @param string $title
 * @param string $import_id
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
}

