<?php



/**
 * Модель правил флагов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsRulesModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return FlagsRulesModel 
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
            return 'flags_rules';
    }
    
    
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "rule_name = '{$name}'"
        ));
        return $this;
    }
}

?>
