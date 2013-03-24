<?php

/**
 * This is the model class for table "tasks".
 *
 * The followings are the available columns in table 'tasks':
 * @property integer $id
 * @property string $title
 * @property string $start_time
 * @property integer $duration
 * @property integer $type
 * @property integer $sim_id
 * @property string $code
 * @property string $start_type
 * @property integer $category
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property DayPlan[] $dayPlans
 * @property DayPlanAfterVacation[] $dayPlanAfterVacations
 * @property DayPlanLog[] $dayPlanLogs
 * @property Simulation $sim
 * @property Todo[] $todos
 */
class Task extends CActiveRecord
{
    const NO_BLOCK = 1;
    const BLOCK = 2;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Task the static model class
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
		return 'tasks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('title, duration, type, import_id', 'required'), TODO:Нужно посмотреть почему валиться
			array('duration, type, sim_id, category', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>200),
			array('code, start_type', 'length', 'max'=>5),
			array('import_id', 'length', 'max'=>14),
			array('start_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, start_time, duration, type, sim_id, code, start_type, category, import_id', 'safe', 'on'=>'search'),
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
			'dayPlans' => array(self::HAS_MANY, 'DayPlan', 'task_id'),
			'dayPlanAfterVacations' => array(self::HAS_MANY, 'DayPlanAfterVacation', 'task_id'),
			'dayPlanLogs' => array(self::HAS_MANY, 'DayPlanLog', 'task_id'),
			'sim' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
			'todo' => array(self::HAS_MANY, 'Todo', 'task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'start_time' => 'Start Time',
			'duration' => 'Duration',
			'type' => 'Type',
			'sim_id' => 'Sim',
			'code' => 'Code',
			'start_type' => 'Start Type',
			'category' => 'Category',
			'import_id' => 'Import',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('type',$this->type);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('start_type',$this->start_type,true);
		$criteria->compare('category',$this->category);
		$criteria->compare('import_id',$this->import_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Выбрать согласно набору задач
     * @param $titles
     * @return Task
     */
    public function byTitles($titles)
    {
        $titles = implode("','", $titles);

        $this->getDbCriteria()->mergeWith(array(
            'condition' => "title in ('{$titles}')"
        ));
        return $this;
    }

    /**
     * Выбрать согласно набору задач
     * @param array $ids
     * @return Tasks
     */
    public function byIds($ids)
    {

        $ids = implode(',', $ids);

        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }

    /**
     * Выбрать конкретную задачу
     * @deprecated
     * @param $simId
     * @return Tasks
     */
    public function bySimId($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }

    /**
     * Выбрать конкретную задачу
     * @param $simId
     * @internal param int $id
     * @deprecated
     * @return Tasks
     */
    public function bySimIdOrNull($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => " (sim_id = {$simId} OR sim_id IS NULL) "
        ));
        return $this;
    }

    /**
     * Выбрать конкретную задачу
     * @param int $id
     * @deprecated
     * @return Tasks
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }

    /**
     * Выбрать по коду задачи
     * @param int $code
     * @return Tasks
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }



    public function byStartType($startType)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "start_type = '{$startType}'"
        ));
        return $this;
    }
}