<?php

/**
 * This is the model class for table "assessment_planing_point".
 *
 * The followings are the available columns in table 'assessment_planing_point':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $hero_behaviour_id
 * @property integer $task_id
 * @property integer $type_scale
 * @property string $value
 *
 * The followings are the available model relations:
 * @property Tasks $task
 * @property HeroBehaviour $heroBehaviour
 * @property Simulations $sim
 */
class AssessmentPlaningPoint extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentPlaningPoint the static model class
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
		return 'assessment_planing_point';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, hero_behaviour_id, task_id, type_scale, value', 'required'),
			array('sim_id, hero_behaviour_id, task_id, type_scale', 'numerical', 'integerOnly'=>true),
			array('value', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, hero_behaviour_id, task_id, type_scale, value', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Tasks', 'task_id'),
			'heroBehaviour' => array(self::BELONGS_TO, 'HeroBehaviour', 'hero_behaviour_id'),
			'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
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
			'hero_behaviour_id' => 'Hero Behaviour',
			'task_id' => 'Task',
			'type_scale' => 'Type Scale',
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
		$criteria->compare('hero_behaviour_id',$this->hero_behaviour_id);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('type_scale',$this->type_scale);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}