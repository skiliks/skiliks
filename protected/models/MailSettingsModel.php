<?php


/**
 * Модель настроек почты в рамках симуляции
 *
 * Связана с моделями:  Simulations.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailSettingsModel extends CActiveRecord
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
     * is messageArriveSound anables
     * @var int (bool)
     */
    public $messageArriveSound;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @return mixed array
     */
    public function getSettingsArray()
    {
        return array(
            'messageArriveSound' => $this->messageArriveSound
        );
    }
    
    /**
     * @param Simulations $simulation
     * @param integer $messageArriveSound
     * 
     * @return boolean
     */
    public static function updateSimulationSettings($simulation, $messageArriveSound)
    {
        $result = false;
        
        $MailSettingsEntity = self::model()->bySimulation($simulation->id)->find();
        if (NULL !== $MailSettingsEntity) {
            $MailSettingsEntity->messageArriveSound = (int)$messageArriveSound;
            $MailSettingsEntity->update();
            $result = true;
        } 
        
        return $result;
    }
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MailSettingsModel 
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
            return 'mail_settings';
    }
    
    /**
     * Выбрать по заданной симуляции
     * @param int $simId
     * @return MailSettingsModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
}


