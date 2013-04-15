<?php

/**
 * This is the model class for table "simulation_learning_goal".
 *
 * The followings are the available columns in table 'simulation_learning_goal':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $learning_goal_id
 * @property float $percent
 * @property float $value
 * @property float $problem
 *
 * The followings are the available model relations:
 * @property LearningGoal $learningGoal
 * @property Simulation $sim
 */
class SimulationLearningGoal extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SimulationLearningGoal the static model class
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
		return 'simulation_learning_goal';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, learning_goal_id', 'required'),
			array('sim_id, learning_goal_id', 'numerical', 'integerOnly'=>true),
			array('value', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, learning_goal_id, value', 'safe', 'on'=>'search'),
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
			'learningGoal' => array(self::BELONGS_TO, 'LearningGoal', 'learning_goal_id'),
			'sim' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
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
			'learning_goal_id' => 'Learning Goal',
			'value' => 'Value',
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
		$criteria->compare('learning_goal_id',$this->learning_goal_id);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getReducingCoefficient()
    {
        if ($this->problem <= 10) {
            return 1;
        } elseif ($this->problem <= 50) {
            return 0.5;
        } else {
            return 0;
        }
    }
}