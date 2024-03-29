<?php

/**
 * @property float $max_negative_value
 * @property string $code
 * @property string $title
 * @property string $import_id
 * @property string $learning_area_code
 * @property integer $learning_goal_group_id
 * @property LearningArea $learningArea
 * @property HeroBehaviour[] $heroBehaviours
 * @property Scenario $scenario_id
 *
 * @author slavka
 */
class LearningGoal extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

    /**
     *
     * @param string $className
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
        return array(
            'learningArea' => array(self::BELONGS_TO, 'LearningArea', 'learning_area_code'),
            'heroBehaviours' => array(self::HAS_MANY, 'HeroBehaviour', 'learning_goal_id'),
        );
    }
}

