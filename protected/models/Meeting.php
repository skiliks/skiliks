<?php

/**
 * This is the model class for table "meeting".
 *
 * The followings are the available columns in table 'meeting':
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $icon_text
 * @property string $popup_text
 * @property integer $duration
 * @property integer $task_id
 * @property string $import_id
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property Task $task
 */
class Meeting extends CActiveRecord implements IGameAction
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Meeting the static model class
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
		return 'meeting';
	}

    public function getCode()
    {
        return $this->code;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, import_id, scenario_id', 'required'),
			array('duration, task_id, scenario_id', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>10),
			array('name', 'length', 'max'=>100),
			array('import_id', 'length', 'max'=>14),
			array('icon_text, popup_text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, code, name, icon_text, popup_text, duration, task_id, import_id, scenario_id', 'safe', 'on'=>'search'),
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
			'code' => 'Code',
			'name' => 'Name',
			'icon_text' => 'Icon Text',
			'popup_text' => 'Popup Text',
			'duration' => 'Duration',
			'task_id' => 'Task',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('icon_text',$this->icon_text,true);
		$criteria->compare('popup_text',$this->popup_text,true);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('import_id',$this->import_id,true);
		$criteria->compare('scenario_id',$this->scenario_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}