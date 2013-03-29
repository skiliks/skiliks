<?php

/**
 * This is the model class for table "day_plan_log".
 *
 * The followings are the available columns in table 'day_plan_log':
 * @property integer $id
 * @property integer $uid
 * @property string $snapshot_date
 * @property string $date
 * @property integer $day
 * @property integer $task_id
 * @property integer $snapshot_time
 * @property integer $sim_id
 * @property integer $todo_count
 *
 * The followings are the available model relations:
 * @property Simulations $sim
 * @property Tasks $task
 */
class DayPlanLog extends CActiveRecord
{
    const TODAY = 1;

    const TOMORROW = 2;

    const AFTER_VACATION = 3;

    const TODO = 4;

    const ON_11_00 = 1;

    const ON_18_00 = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DayPlanLog the static model class
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
		return 'day_plan_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid, day, task_id', 'required'),
			array('uid, day, task_id, snapshot_time, sim_id, todo_count', 'numerical', 'integerOnly'=>true),
			array('snapshot_date, date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, uid, snapshot_date, date, day, task_id, snapshot_time, sim_id, todo_count', 'safe', 'on'=>'search'),
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
			'uid' => 'Uid',
			'snapshot_date' => 'Snapshot Date',
			'date' => 'Date',
			'day' => 'Day',
			'task_id' => 'Task',
			'snapshot_time' => 'Snapshot Time',
			'sim_id' => 'Sim',
			'todo_count' => 'Todo Count',
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
		$criteria->compare('uid',$this->uid);
		$criteria->compare('snapshot_date',$this->snapshot_date,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('day',$this->day);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('snapshot_time',$this->snapshot_time);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('todo_count',$this->todo_count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Выборка по симуляции
     *
     * @param int $simId
     * @return DayPlanLog
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
}