<?php



/**
 * Какие звонки и когда были совершены в рамках симуляции
 * 
 * Связана с моделями:  Simulations, Characters.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneCallsModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return PhoneCallsModel 
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
            return 'phone_calls';
    }
    
    /**
     * Выбрать согласно заданной симуляции
     * @param int $simId
     * @return PhoneCallsModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
}

?>
