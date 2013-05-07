<?php
/**
 * По сути справочник оцениваемых поведений.
 *
 * @property integer $id
 * @property string $code
 * @property string $title
 * @property string $scale
 * @property string $learning_goal_code
 * @property integer $group_id
 * @property integer $type_scale; 1 - positive, 2 - negative, 3 - personal
 * @property float $add_value
 *
 * @property LearningGoal learning_goal
 */
class HeroBehaviour extends CActiveRecord
{
    const TYPE_ID_POSITIVE = 1;
    const TYPE_ID_NEGATIVE = 2;
    const TYPE_ID_PERSONAL = 3;

    const TYPE_SLUG_POSITIVE = 'positive';
    const TYPE_SLUG_NEGATIVE = 'negative';
    const TYPE_SLUG_PERSONAL = 'personal';

    /**
     * @return array
     */
    public static function getExcludedFromAssessmentBehavioursCodes()
    {
        return ['214g1', '214g2', '214g3', '214g4', '32110', '32112', '32113',
            '32114', '3312', '3335', '341a1', '341a8', '341c1', '341c2', '371a1',
            '371a2', '371a3', '371a4', '371a5', '371b1', '371b2', '371b3', '8211'];
    }

    /**
     * @param $typeScalaCode
     * @return string
     */
    public static function getTypeScaleName($typeScalaCode)
    {
        switch ($typeScalaCode) {
            case self::TYPE_ID_POSITIVE: return self::TYPE_SLUG_POSITIVE;
            case self::TYPE_ID_NEGATIVE: return self::TYPE_SLUG_NEGATIVE;
            case self::TYPE_ID_PERSONAL: return self::TYPE_SLUG_PERSONAL;
        }
    }

    /**
     * @param string $name
     * @return int|null
     */
    public static function getScaleId($name) {
        switch ($name) {
            case self::TYPE_SLUG_POSITIVE; return self::TYPE_ID_POSITIVE; break;
            case self::TYPE_SLUG_NEGATIVE; return self::TYPE_ID_NEGATIVE; break;
            case self::TYPE_SLUG_PERSONAL; return self::TYPE_ID_PERSONAL; break;
        }
        
        return null;
    }


    /**
     * User representation of type scale
     * @return string
     */
    public function getTypeScaleSlug()
    {
        if ($this->isPositive()) {
            return self::TYPE_SLUG_POSITIVE;
        } elseif ($this->isNegative()) {
            return self::TYPE_SLUG_NEGATIVE;
        } elseif ($this->isPersonal()) {
            return self::TYPE_SLUG_PERSONAL;
        }
    }

    /**
     * @return boolean
     */
    public function isPositive()
    {
        return (self::TYPE_ID_POSITIVE == $this->type_scale);
    }

    /**
     * @return boolean
     */
    public function isNegative()
    {
        return (self::TYPE_ID_NEGATIVE == $this->type_scale);
    }

    /**
     * @return boolean
     */
    public function isPersonal()
    {
        return (self::TYPE_ID_PERSONAL == $this->type_scale);
    }

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
            return 'hero_behaviour';
    }

    /**
     * 
     */
    public function relations()
    {
        return [
            'learning_goal' => [self::BELONGS_TO, 'LearningGoal', 'learning_goal_id'],
            'group' => [self::BELONGS_TO, 'AssessmentGroup', 'group_id'],
            'type' => [self::BELONGS_TO, 'TypeScale', 'type_scale'],
        ];
    }
}


