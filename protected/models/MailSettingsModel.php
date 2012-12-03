<?php


/**
 * Модель настроек почты в рамках симуляции
 *
 * Связана с моделями:  Simulations.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailSettingsModel extends CActiveRecord{
    
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


