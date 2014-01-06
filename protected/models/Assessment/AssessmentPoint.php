<?php

/**
 * This is the model class for table "assessment_points".
 *
 * The followings are the available columns in table 'assessment_points':
 * @property integer $id
 * @property integer $sim_id
 * @property integer $point_id
 * @property integer $dialog_id
 * @property integer $task_id
 * @property integer $mail_id
 * @property integer $value
 *
 * The followings are the available model relations:
 * @property Replica $dialog
 * @property MailTemplate $mail
 * @property HeroBehaviour $point
 * @property Simulation $sim
 */
class AssessmentPoint extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentPoint the static model class
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
		return 'assessment_points';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('sim_id', 'required'),
			array('sim_id, point_id, dialog_id, task_id, mail_id,value', 'numerical', 'integerOnly'=>true),
			array('id, sim_id, point_id, dialog_id, task_id, mail_id,value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'replica' => array(self::BELONGS_TO, 'Replica', 'dialog_id'),
			'mail' => array(self::BELONGS_TO, 'MailTemplate', 'mail_id'),
			'point' => array(self::BELONGS_TO, 'HeroBehaviour', 'point_id'),
			'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
			'task' => array(self::BELONGS_TO, 'DayPlan', 'task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'ID',
			'sim_id'    => 'Simulation',
			'point_id'  => 'Point',
			'dialog_id' => 'Replica',
			'task_id'   => 'Task',
			'mail_id'   => 'Mail',
			'value'     => 'Value',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('point_id',$this->point_id);
		$criteria->compare('dialog_id',$this->dialog_id);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('mail_id',$this->mail_id);
		$criteria->compare('value',$this->value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}