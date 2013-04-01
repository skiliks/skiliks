<?php

/**
 * This is the model class for table "day_plan_after_vacation".
 *
 * The followings are the available columns in table 'day_plan_after_vacation':
 * @property integer $id
 * @property integer $task_id
 * @property integer $sim_id
 * @property string $date
 *
 * The followings are the available model relations:
 * @property Simulations $sim
 * @property Tasks $task
 */
class DayPlanAfterVacation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DayPlanAfterVacation the static model class
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
		return 'day_plan_after_vacation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, sim_id', 'required'),
			array('task_id, sim_id', 'numerical', 'integerOnly'=>true),
			array('date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, sim_id, date', 'safe', 'on'=>'search'),
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
			'sim' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
			'task' => array(self::BELONGS_TO, 'Tasks', 'task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'task_id' => 'Task',
			'sim_id' => 'Sim',
			'date' => 'Date',
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
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('date',$this->date);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Выборка по симуляции
     *
     * @param int $simId
     * @return DayPlanAfterVacation
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id={$simId}"
        ));
        return $this;
    }
}