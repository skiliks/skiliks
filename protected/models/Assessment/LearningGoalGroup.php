<?php

/**
 * This is the model class for table "learning_goal_group".
 *
 * The followings are the available columns in table 'learning_goal_group':
 * @property integer $id
 * @property string $code
 * @property string $title
 * @property string $import_id
 * @property integer $scenario_id
 * @property integer $learning_area_id
 * @property string $learning_area_code
 *
 * The followings are the available model relations:
 * @property SimulationLearningGoalGroup[] $simulationLearningGoalGroups
 * @property LearningGoal[] $learningGoals
 */
class LearningGoalGroup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LearningGoalGroup the static model class
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
		return 'learning_goal_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, scenario_id', 'required'),
			array('scenario_id', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>5),
			array('import_id', 'length', 'max'=>14),
			array('title', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, code, title, import_id, scenario_id', 'safe', 'on'=>'search'),
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
			'simulationLearningGoalGroups' => array(self::HAS_MANY, 'SimulationLearningGoalGroup', 'learning_goal_group_id'),
            'learningGoals' => array(self::HAS_MANY, 'LearningGoal', 'learning_goal_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => 'Code',
			'title' => 'Title',
			'import_id' => 'Import',
			'scenario_id' => 'Scenario',
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
		$criteria->compare('code',$this->code,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('import_id',$this->import_id,true);
		$criteria->compare('scenario_id',$this->scenario_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}