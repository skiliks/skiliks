<?php

/**
 * This is the model class for table "log_invite".
 *
 * The followings are the available columns in table 'log_invite':
 * @property integer $id
 * @property integer $invite_id
 * @property string $status
 * @property integer $sim_id
 * @property string $action
 * @property string $real_date
 * @property string $comment
 */
class LogInvite extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogInvite the static model class
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
		return 'log_invite';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invite_id, sim_id', 'numerical', 'integerOnly'=>true),
			array('status', 'length', 'max'=>40),
			array('action, real_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, invite_id, status, sim_id, action, real_date', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'invite_id' => 'Invite',
			'status' => 'Status',
			'sim_id' => 'Sim',
			'action' => 'Action',
			'real_date' => 'Read Date',
			'comment' => 'Comment',
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
		$criteria->compare('invite_id',$this->invite_id);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('real_date',$this->real_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}