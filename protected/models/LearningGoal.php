<?php

/**
 * @property float $max_negative_value
 * @property string $code
 * @property string $title
 * @property string $import_id
 * @property string $learning_area_code
 *
 * @property LearningArea $learningArea
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
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'learningArea' => array(self::BELONGS_TO, 'LearningArea', 'learning_area_code'),
        );
    }
}

