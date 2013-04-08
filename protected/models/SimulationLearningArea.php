<?php

/**
 * This is the model class for table "simulation_learning_area".
 *
 * The followings are the available columns in table 'simulation_learning_area':
 * @property integer $id
 * @property integer $learning_area_id
 * @property double $value
 * @property integer $sim_id
 *
 * The followings are the available model relations:
 * @property LearningArea $learningArea
 */
class SimulationLearningArea extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SimulationLearningArea the static model class
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
		return 'simulation_learning_area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('learning_area_id', 'required'),
			array('learning_area_id', 'numerical', 'integerOnly'=>true),
			array('value', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, learning_area_id, value', 'safe', 'on'=>'search'),
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
			'learningArea' => array(self::BELONGS_TO, 'LearningArea', 'learning_area_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'learning_area_id' => 'Learning Area',
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
		$criteria->compare('learning_area_id',$this->learning_area_id);
		$criteria->compare('value',$this->value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}