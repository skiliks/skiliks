<?php
/**
 *
 * @property float coefficient_for_fixed_value
 * @property float fixed_value
 * @property float value
 * @property int point_id
 * @property int id
 * @property int sim_id
 *
 * @property HeroBehaviour point
 *
 * @author slavka
 */
class AssessmentAggregated extends CActiveRecord
{
    // Place your specific code there

    /* -------------------------------------------------------------------------------------------------------------- */    

    /**
     * @param string $className
     * @return AssessmentAggregated
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
            return 'assessment_aggregated';
    }
    
    public function relations()
    {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'point' => array(self::BELONGS_TO, 'HeroBehaviour', 'point_id'),
        );
    }
}

