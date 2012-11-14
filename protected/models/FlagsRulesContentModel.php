<?php



/**
 * Содержит флаги, связанный с конкретными правилами флагов 
 * а также значения флагов для этих правил.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsRulesContentModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return FlagsRulesContentModel 
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
            return 'flags_rules_content';
    }
    
    
    public function byRule($ruleId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "rule_id = {$ruleId}"
        ));
        return $this;
    }
}

?>
