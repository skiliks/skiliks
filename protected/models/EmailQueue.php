<?php

/**
 * This is the model class for table "emails_queue".
 *
 * The followings are the available columns in table 'emails_queue':
 * @property integer $id
 * @property string $subject
 * @property string $sender_email
 * @property string $recipients
 * @property string $copies
 * @property string $body
 * @property string $attachments
 * @property string $created_at
 * @property string $sended_at
 * @property string $status
 * @property string $errors
 */
class EmailQueue extends CActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SENDED = 'sended';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EmailQueue the static model class
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
		return 'emails_queue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subject, sender_email', 'length', 'max'=>200),
			array('status', 'length', 'max'=>30),
			array('recipients, copies, body, attachments, created_at, sended_at, errors', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, subject, sender_email, recipients, copies, body, attachments, created_at, sended_at, status, errors', 'safe', 'on'=>'search'),
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
			'subject' => 'Subject',
			'sender_email' => 'Sender Email',
			'recipients' => 'Recipients',
			'copies' => 'Copies',
			'body' => 'Body',
			'attachments' => 'Attachments',
			'created_at' => 'Created At',
			'sended_at' => 'Sended At',
			'status' => 'Status',
			'errors' => 'Errors',
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
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('sender_email',$this->sender_email,true);
		$criteria->compare('recipients',$this->recipients,true);
		$criteria->compare('copies',$this->copies,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('attachments',$this->attachments,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('sended_at',$this->sended_at,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('errors',$this->errors,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}