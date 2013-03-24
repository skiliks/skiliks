<?php

/**
 * This is the model class for table "todo".
 *
 * The followings are the available columns in table 'todo':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $task_id
 * @property string $adding_date
 *
 * The followings are the available model relations:
 * @property AssessmentPoints[] $assessmentPoints
 * @property Simulations $sim
 * @property Tasks $task
 */
class Todo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Todo the static model class
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
		return 'todo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, task_id', 'required'),
			array('sim_id, task_id', 'numerical', 'integerOnly'=>true),
			array('adding_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, task_id, adding_date', 'safe', 'on'=>'search'),
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
			'assessmentPoints' => array(self::HAS_MANY, 'AssessmentPoints', 'task_id'),
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
			'sim_id' => 'Sim',
			'task_id' => 'Task',
			'adding_date' => 'Adding Date',
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
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('adding_date',$this->adding_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Выбрать в рамках заданной симуляции
     * @param int $simId
     * @return Todo
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id={$simId}"
        ));
        return $this;
    }

    /**
     * Выбрать по заданной задаче
     * @param int $taskId
     * @return Todo
     */
    public function byTask($taskId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "task_id={$taskId}"
        ));
        return $this;
    }

    /**
     * Выбрать самую свежую задачу
     * @return Todo
     */
    public function byLatestAddingDate()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "adding_date desc"
        ));
        return $this;
    }

}