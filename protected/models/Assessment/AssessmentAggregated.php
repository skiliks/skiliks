<?php
/**
 * @property CharactersPointsTitles point
 * @author slavka
 */
class AssessmentAggregated extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;    
    
    /**
     * characters_points_title.id
     * @var integer
     */
    public $point_id;
    
    /**
     * @var float
     */
    public $value; 
    
    /* -------------------------------------------------------------------------------------------------------------- */    
    
    /**
     * Выборка по идентификатору оценки
     * @param int $pointId
     * @return AssassmentAgregated
     */
    public function byPoint($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = '{$pointId}'"
        ));
        return $this;
    }
    
    /**
     * Выборка по симуляции
     * 
     * @param int $simId
     * @return AssassmentAgregated
     */
    public function bySimId($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * 
     * @param Simulations $simulation
     * @return array of AssessmentAgregated
     */
    public function findAllInSimulation($simulation)
    {
        return $this->model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);
    }
    
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
            'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
            'point' => array(self::BELONGS_TO, 'CharactersPointsTitles', 'point_id'),
        );
    }
}

