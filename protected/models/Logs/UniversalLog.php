<?php

/**
 * This is the model class for table "universal_log".
 *
 * The followings are the available columns in table 'universal_log':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $window_id
 * @property integer $mail_id
 * @property integer $file_id
 * @property integer $replica_id
 * @property integer $last_dialog_id
 * @property integer $activity_action_id
 * @property string $start_time
 * @property string $end_time
 * @property integer $meeting_id
 * @property string $window_uid
 *
 * The followings are the available model relations:
 * @property Meeting $meeting
 * @property Replica $replica
 * @property ActivityAction $activityAction
 * @property Replica $lastDialog
 * @property MyDocument $file
 * @property MailBox $mail
 * @property Simulation $sim
 * @property Window $window
 */
class UniversalLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UniversalLog the static model class
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
		return 'universal_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('start_time', 'required'),
			array('sim_id, window_id, mail_id, file_id, replica_id, last_dialog_id, meeting_id', 'numerical', 'integerOnly'=>true),
			array('window_uid', 'length', 'max'=>32),
			array('end_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, window_id, mail_id, file_id, replica_id, last_dialog_id, activity_action_id, start_time, end_time, meeting_id, window_uid', 'safe', 'on'=>'search'),
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
			'replica' => array(self::BELONGS_TO, 'Replica', 'replica_id'),
			'activityAction' => array(self::BELONGS_TO, 'ActivityAction', 'activity_action_id'),
			'lastDialog' => array(self::BELONGS_TO, 'Replica', 'last_dialog_id'),
			'file' => array(self::BELONGS_TO, 'MyDocument', 'file_id'),
			'mail' => array(self::BELONGS_TO, 'MailBox', 'mail_id'),
			'sim' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
			'window' => array(self::BELONGS_TO, 'Window', 'window_id'),
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
			'window_id' => 'Window',
			'mail_id' => 'Mail',
			'file_id' => 'File',
			'replica_id' => 'Replica',
			'last_dialog_id' => 'Last Dialog',
			'activity_action_id' => 'Activity Action',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'meeting_id' => 'Meeting',
			'window_uid' => 'Window Uid',
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
		$criteria->compare('window_id',$this->window_id);
		$criteria->compare('mail_id',$this->mail_id);
		$criteria->compare('file_id',$this->file_id);
		$criteria->compare('replica_id',$this->replica_id);
		$criteria->compare('last_dialog_id',$this->last_dialog_id);
		$criteria->compare('activity_action_id',$this->activity_action_id);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('meeting_id',$this->meeting_id);
		$criteria->compare('window_uid',$this->window_uid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}