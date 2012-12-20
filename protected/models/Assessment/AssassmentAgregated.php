<?php
/**
 * @author slavka
 */
class AssassmentAgregated extends CActiveRecord
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
     * characters_points.id
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
     * @param string $className
     * @return AssassmentAgregated
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
            return 'assassment_agregated';
    }
}

