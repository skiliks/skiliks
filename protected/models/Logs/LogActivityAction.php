<?php

/**
 * This is the model class for table "log_activity_action".
 *
 * The followings are the available columns in table 'log_activity_action':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $activity_action_id
 * @property integer $window
 * @property string $window_uid
 * @property string $start_time
 * @property string $end_time
 * @property integer $is_final_activity
 *
 * The followings are the available model relations:
 * @property Simulations $sim
 * @property ActivityAction $activityAction
 * @property mixed mail_id, MailBox id
 * @property mixed document_id, MyDocument id
 */
class LogActivityAction extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogActivityAction the static model class
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
		return 'log_activity_action';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id, activity_action_id', 'required'),
			array('sim_id, activity_action_id, window', 'numerical', 'integerOnly'=>true),
			array('start_time, end_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, activity_action_id, window, start_time, end_time', 'safe', 'on'=>'search'),
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
			'simulation' => array(self::BELONGS_TO, 'Simulations', 'sim_id'),
			'activityAction' => array(self::BELONGS_TO, 'ActivityAction', 'activity_action_id'),
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
			'activity_action_id' => 'Activity Action',
			'window' => 'Window',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
		);
	}

    /**
     * Prints all needed info in one row
     */
    public function dump() {
        printf("%s  %8s  %-15s\t%-10s\n",
                $this->start_time,
                $this->end_time !== null ? $this->end_time : '—',
                $this->activityAction->activity_id,
                $this->activityAction->mail !== null ? $this->activityAction->mail->code : '—'
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
		$criteria->compare('activity_action_id',$this->activity_action_id);
		$criteria->compare('window',$this->window);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}