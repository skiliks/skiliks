<?php


/**
 * состояние флагов пользователя в рамках конкретной симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationFlag extends CActiveRecord
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

    /**
     * sends Email if it is immediate
     * @return void
     */
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
     * @param string $className
     * @return SimulationFlag 
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
     * Выбрать по заданному флагу
     * @deprecated SQL injection
     * @param string $flag
     * @return SimulationFlag 
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
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'flagObj'    => array(self::BELONGS_TO, 'Flag', 'flag'),
        );
    }
}


