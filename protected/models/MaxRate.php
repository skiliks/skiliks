<?php

/**
 * This is the model class for table "max_rate".
 *
 * The followings are the available columns in table 'max_rate':
 * @property integer $id
 * @property string $type
 * @property integer $rate
 * @property integer $learning_goal_id
 * @property integer $hero_behaviour_id
 * @property string $performance_rule_category_id
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property ActivityCategory $performanceRuleCategory
 * @property HeroBehaviour $heroBehaviour
 * @property LearningGoal $learningGoal
 * @property Scenario $scenario
 */
class MaxRate extends CActiveRecord
{
    const TYPE_FAIL = 'fail_rate';
    const TYPE_SUCCESS = 'success_rate';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MaxRate the static model class
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
		return 'max_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rate, learning_goal_id, hero_behaviour_id', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>50),
			array('performance_rule_category_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, rate, learning_goal_id, hero_behaviour_id, performance_rule_category_id', 'safe', 'on'=>'search'),
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
			'performanceRuleCategory' => array(self::BELONGS_TO, 'ActivityCategory', 'performance_rule_category_id'),
			'heroBehaviour' => array(self::BELONGS_TO, 'HeroBehaviour', 'hero_behaviour_id'),
			'learningGoal' => array(self::BELONGS_TO, 'LearningGoal', 'learning_goal_id'),
			'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'rate' => 'Rate',
			'learning_goal_id' => 'Learning Goal',
			'hero_behaviour_id' => 'Hero Behaviour',
			'performance_rule_category_id' => 'Performance Rule Category',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('learning_goal_id',$this->learning_goal_id);
		$criteria->compare('hero_behaviour_id',$this->hero_behaviour_id);
		$criteria->compare('performance_rule_category_id',$this->performance_rule_category_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}