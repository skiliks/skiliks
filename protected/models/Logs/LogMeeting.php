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
		return array(
			array('sim_id, meeting_id, start_time', 'required'),
			array('sim_id, meeting_id', 'numerical', 'integerOnly'=>true),
			array('id, sim_id, meeting_id, start_time, end_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
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
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
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