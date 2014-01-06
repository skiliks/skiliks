<?php

/**
 * Состояние флагов пользователя в рамках конкретной симуляции
 *
 * @property integer $id
 * @property integer $sim_id
 * @property string $flag, 'F1', 'F2', ...
 * @property integer $value, 0 or 1
 */
class SimulationFlag extends CActiveRecord
{
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

    public function relations() {
        return array(
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'flagObj'    => array(self::BELONGS_TO, 'Flag', 'flag'),
        );
    }
}
