<?php

/**
 * This is the model class for table "weight".
 *
 * The followings are the available columns in table 'weight':
 * @property integer $id
 * @property integer $rule_id
 * @property string $performance_rule_category_id
 * @property integer $hero_behaviour_id
 * @property string $assessment_category_code
 * @property float $value
 * @property integer $scenario_id
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 * @property AssessmentCategory $assessmentCategory
 * @property HeroBehaviour $heroBehaviour
 * @property ActivityCategory $performanceRuleCategory
 */
class Weight extends CActiveRecord
{
    const RULE_OVERALL_RATE = 1;
    const RULE_PERFORMANCE = 2;
    const RULE_DECISION_MAKING = 3;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Weight the static model class
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
		return 'weight';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('import_id', 'required'),
			array('rule_id, hero_behaviour_id, scenario_id', 'numerical', 'integerOnly'=>true),
			array('performance_rule_category_id, value', 'length', 'max'=>10),
			array('assessment_category_code', 'length', 'max'=>50),
			array('import_id', 'length', 'max'=>14),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, rule_id, performance_rule_category_id, hero_behaviour_id, assessment_category_code, value, scenario_id, import_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
			'assessmentCategory' => array(self::BELONGS_TO, 'AssessmentCategory', 'assessment_category_code'),
			'heroBehaviour' => array(self::BELONGS_TO, 'HeroBehaviour', 'hero_behaviour_id'),
			'performanceRuleCategory' => array(self::BELONGS_TO, 'ActivityCategory', 'performance_rule_category_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'rule_id' => 'Rule',
			'performance_rule_category_id' => 'Performance Rule Category',
			'hero_behaviour_id' => 'Hero Behaviour',
			'assessment_category_code' => 'Assessment Category Code',
			'value' => 'Value',
			'scenario_id' => 'Scenario',
			'import_id' => 'Import',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('rule_id',$this->rule_id);
		$criteria->compare('performance_rule_category_id',$this->performance_rule_category_id,true);
		$criteria->compare('hero_behaviour_id',$this->hero_behaviour_id);
		$criteria->compare('assessment_category_code',$this->assessment_category_code,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('scenario_id',$this->scenario_id);
		$criteria->compare('import_id',$this->import_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}