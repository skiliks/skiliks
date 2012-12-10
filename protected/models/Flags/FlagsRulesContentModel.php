<?php



/**
 * Содержит флаги, связанный с конкретными правилами флагов 
 * а также значения флагов для этих правил.
 * 
 * Связана с моделями: FlagsRulesModel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsRulesContentModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var integer
     */
    public $rule_id;
    
    /**
     * Flag name, like 'F1', 'F22'
     * @var string
     */
    public $flag;
    
    /**
     * True or false - Flag must be true, flag must be false
     * @var boolean
     */
    public $value;

    // -----------------------------------------------------------------------------------------------------------------
    
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
    
    /**
     * Выбрать по конкретному правилу
     * @param int $ruleId
     * @return FlagsRulesContentModel 
     */
    public function byRule($ruleId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "rule_id = {$ruleId}"
        ));
        return $this;
    }
    
    
    /**
     * Выбрать по конкретному правилу
     * @param string $flag
     * @return FlagsRulesContentModel 
     */
    public function byFlagName($flag)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "flag = '{$flag}'"
        ));
        return $this;
    }    
    
    // -------------------------------------------------------------------------
    
    /**
     * @param integer $id
     * 
     * @return \FlagsRulesModel
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getRuleId()
    {
        return (int)$this->rule_id;
    }
    
    /**
     * @param integer $ruleId
     * 
     * @return \FlagsRulesContentModel
     */
    public function setRuleId($ruleId)
    {
        $this->rule_id = (int)$ruleId;
        return $this;
    }
    
    public function getFlagName()
    {
        return $this->flag;
    }
    
    /**
     * @param string $flagName
     * 
     * @return \FlagsRulesContentModel
     */
    public function setFlagName($flagName)
    {
        $this->flag = $flagName;
        return $this;
    }
    
    public function getValue()
    {
        return (boolean)$this->value;
    }
    
    /**
     * @param boolean $value
     * 
     * @return \FlagsRulesContentModel
     */
    public function setValue($value)
    {
        // MySQL store boolean as TinyInt(1)
        $this->value = (int)$value;
        return $this;
    }
}

