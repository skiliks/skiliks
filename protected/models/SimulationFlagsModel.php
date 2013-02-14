<?php


/**
 * состояние флагов пользователя в рамках конкретной симуляции
 *
 * Связана с моделями:  Simulations
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationFlagsModel extends CActiveRecord
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
     * 'F1', 'F2', ...
     * @var string
     */
    public $flag;
    
    /**
     * @var integer
     */
    public $value;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    public function afterSave() {
        // @1229
        // send email if exist emails related to flag $this->flag {
        if (1 == $this->value) {
            MailBoxService::sendEmailsRelatedToFlag($this->simulation, $this->flag);
            // @todo check is email come to frontend
        }
        // send email if exist emails related to flag $this->flag }
    }

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return SimulationFlagsModel 
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
            return 'simulation_flags';
    }
    
    /**
     * Выбрать согласно заданной симуляции
     * @param int $simId
     * @return SimulationFlagsModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному флагу
     * @param string $flag
     * @return SimulationFlagsModel 
     */
    public function byFlag($flag)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "flag = '{$flag}'"
        ));
        return $this;
    }

    public function relations() {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
        );
    }
}


