<?php

/**
 * This is the model class for table "day_plan".
 *
 * The followings are the available columns in table 'day_plan':
 * @property integer $id
 * @property integer $sim_id
 * @property string $date
 * @property string $day
 * @property integer $task_id
 *
 * The followings are the available model relations:
 * @property Simulation $sim
 * @property Task $task
 */
class DayPlan extends CActiveRecord
{
    const DAY_1              = 'day-1';
    const DAY_2              = 'day-2';
    const DAY_AFTER_VACATION = 'after-vacation';
    const DAY_TODO           = 'todo';

    protected static $allowedDays = ['day-1', 'day-2', 'after-vacation', 'todo'];

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DayPlan the static model class
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
		return 'day_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, day, task_id', 'required'),
			array('sim_id, task_id', 'numerical', 'integerOnly'=>true),
			array('date', 'safe'),
			array('day', 'isDayAllowed'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, date, day, task_id', 'safe', 'on'=>'search'),
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
			'sim' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
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
			'date' => 'Date',
			'day' => 'Day',
			'task_id' => 'Task',
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
		$criteria->compare('date',$this->date);
		$criteria->compare('position',$this->position);
		$criteria->compare('task_id',$this->task_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param string $day
     * @return bool
     */
    public function isDayAllowed($day)
    {
        if (!in_array($this->day, self::$allowedDays)) {
            $this->addError('day', Yii::t('site', 'Неверный день'));
        }
    }
}