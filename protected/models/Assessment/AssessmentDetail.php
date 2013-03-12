<?php

/**
 * This is the model class for table "assessment_detail".
 *
 * The followings are the available columns in table 'assessment_detail':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $point_id
 * @property integer $dialog_id
 * @property integer $task_id
 * @property integer $mail_id
 *
 * The followings are the available model relations:
 * @property Replica $dialog
 * @property MailBox $mail
 * @property HeroBehaviour $point
 * @property Simulation $sim
 * @property Todo $task
 */
class AssessmentDetail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentDetail the static model class
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
		return 'assessment_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id', 'required'),
			array('sim_id, point_id, dialog_id, task_id, mail_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, point_id, dialog_id, task_id, mail_id', 'safe', 'on'=>'search'),
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
			'replica' => array(self::BELONGS_TO, 'Replica', 'dialog_id'),
			'mail' => array(self::BELONGS_TO, 'MailBox', 'mail_id'),
			'point' => array(self::BELONGS_TO, 'HeroBehaviour', 'point_id'),
			'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
			'task' => array(self::BELONGS_TO, 'Todo', 'task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sim_id' => 'Simulation',
			'point_id' => 'Point',
			'dialog_id' => 'Replica',
			'task_id' => 'Task',
			'mail_id' => 'Mail',
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
		$criteria->compare('point_id',$this->point_id);
		$criteria->compare('dialog_id',$this->dialog_id);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('mail_id',$this->mail_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @return ReplicaPoint
     */
    public function getReplicaPoint()
    {
        return ReplicaPoint::model()->findByAttributes(['point_id' => $this->point_id, 'dialog_id' => $this->dialog_id]);
    }
}