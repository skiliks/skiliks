<?php

/**
 * This is the model class for table "decline_explanation".
 *
 * The followings are the available columns in table 'decline_explanation':
 * @property integer $id
 * @property integer $invite_id
 * @property string $invite_recipient_id
 * @property string $invite_owner_id
 * @property integer $vacancy_label
 * @property integer $reason_id
 * @property string $description
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property DeclineReason $reason
 * @property Invites $invite
 * @property User $inviteOwner
 * @property User $inviteRecipient
 */
class DeclineExplanation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DeclineExplanation the static model class
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
		return 'decline_explanation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reason_id, description', 'required'),
			array('invite_id, vacancy_label, reason_id', 'numerical', 'integerOnly'=>true),
			array('invite_recipient_id, invite_owner_id', 'length', 'max'=>10),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, invite_id, invite_recipient_id, invite_owner_id, vacancy_label, reason_id, description, created_at', 'safe', 'on'=>'search'),
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
			'reason' => array(self::BELONGS_TO, 'DeclineReason', 'reason_id'),
			'invite' => array(self::BELONGS_TO, 'Invite', 'invite_id'),
			'inviteOwner' => array(self::BELONGS_TO, 'User', 'invite_owner_id'),
			'inviteRecipient' => array(self::BELONGS_TO, 'User', 'invite_recipient_id'),
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
			'invite_recipient_id' => 'Invite Recipient',
			'invite_owner_id' => 'Invite Owner',
			'vacancy_label' => 'Vacancy Label',
			'reason_id' => 'Reason',
			'description' => 'Description',
			'created_at' => 'Created At',
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
		$criteria->compare('invite_recipient_id',$this->invite_recipient_id);
		$criteria->compare('invite_owner_id',$this->invite_owner_id);
		$criteria->compare('vacancy_label',$this->vacancy_label);
		$criteria->compare('reason_id',$this->reason_id);
		$criteria->compare('description',$this->description);
		$criteria->compare('created_at',$this->created_at);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}