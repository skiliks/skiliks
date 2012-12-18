<?php

/**
 * This is the model class for table "activity_action".
 *
 * The followings are the available columns in table 'activity_action':
 * @property integer $id
 * @property string $activity_id
 * @property integer $dialog_id
 * @property integer $mail_id
 * @property integer $document_id
 *
 * The followings are the available model relations:
 * @property Activity $activity
 * @property Dialogs $dialog
 * @property MailTemplateModel $mail
 * @property MyDocumentsTemplateModel $document
 */
class ActivityAction extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ActivityAction the static model class
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
		return 'activity_action';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_id', 'required'),
			array('dialog_id, mail_id, document_id', 'numerical', 'integerOnly'=>true),
			array('activity_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, activity_id, dialog_id, mail_id, document_id', 'safe', 'on'=>'search'),
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
			'activity' => array(self::BELONGS_TO, 'Activity', 'activity_id'),
			'dialog' => array(self::BELONGS_TO, 'Dialogs', 'dialog_id'),
			'mail' => array(self::BELONGS_TO, 'MailTemplateModel', 'mail_id'),
			'document' => array(self::BELONGS_TO, 'MyDocumentsTemplateModel', 'document_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'activity_id' => 'Activity',
			'dialog_id' => 'Dialog',
			'mail_id' => 'Mail',
			'document_id' => 'Document',
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
		$criteria->compare('activity_id',$this->activity_id,true);
		$criteria->compare('dialog_id',$this->dialog_id);
		$criteria->compare('mail_id',$this->mail_id);
		$criteria->compare('document_id',$this->document_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}