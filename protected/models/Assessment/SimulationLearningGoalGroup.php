<?php

/**
 * This is the model class for table "simulation_learning_goal_group".
 *
 * The followings are the available columns in table 'simulation_learning_goal_group':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $learning_goal_group_id
 * @property string $value
 * @property string $percent
 * @property string $problem
 * @property string $total_positive
 * @property string $total_negative
 * @property string $max_positive
 * @property string $max_negative
 * @property string $coefficient
 *
 * The followings are the available model relations:
 * @property LearningGoalGroup $learningGoalGroup
 * @property Simulation $simulation
 */
class SimulationLearningGoalGroup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SimulationLearningGoalGroup the static model class
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
		return 'simulation_learning_goal_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, learning_goal_group_id', 'required'),
			array('sim_id, learning_goal_group_id', 'numerical', 'integerOnly'=>true),
			array('value, percent, problem', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, learning_goal_group_id, value, percent, problem', 'safe', 'on'=>'search'),
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
			'learningGoalGroup' => array(self::BELONGS_TO, 'LearningGoalGroup', 'learning_goal_group_id'),
            'learningGoal' => array(self::HAS_MANY, 'SimulationLearningGoal', 'learning_goal_group_id'),
			'sim' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sim_id' => 'Sim',
			'learning_goal_group_id' => 'Learning Goal Group',
			'value' => 'Value',
			'percent' => 'Percent',
			'problem' => 'Problem',
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
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('learning_goal_group_id',$this->learning_goal_group_id);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('percent',$this->percent,true);
		$criteria->compare('problem',$this->problem,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getReducingCoefficient()
    {
        return LearningGoalAnalyzer::getReducingCoefficient($this->problem);
    }
}