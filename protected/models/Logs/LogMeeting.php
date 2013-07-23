<?php

/**
 * This is the model class for table "log_meeting".
 *
 * The followings are the available columns in table 'log_meeting':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $meeting_id
 * @property string $start_time
 * @property string $end_time
 *
 * The followings are the available model relations:
 * @property Meeting $meeting
 * @property Simulation $simulation
 */
class LogMeeting extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogMeeting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    protected function afterSave()
    {
        /** @var $activityAction ActivityAction */
        $activityAction = ActivityAction::model()->findByPriority(
            ['meeting_id' => $this->meeting_id],
            NULL,
            $this->simulation
        );

        if (null !== $activityAction && null !== $this->window_uid) {
            $activityAction->appendLog($this);
        }

        parent::afterSave();
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'log_meeting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, meeting_id, start_time', 'required'),
			array('sim_id, meeting_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, meeting_id, start_time, end_time', 'safe', 'on'=>'search'),
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
			'meeting' => array(self::BELONGS_TO, 'Meeting', 'meeting_id'),
			'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
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
			'meeting_id' => 'Meeting',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
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
		$criteria->compare('meeting_id',$this->meeting_id);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}