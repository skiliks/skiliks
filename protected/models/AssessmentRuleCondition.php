<?php

/**
 * This is the model class for table "assessment_rule_conditions".
 *
 * The followings are the available columns in table 'assessment_rule_conditions':
 * @property integer $id
 * @property integer $assessment_rule_id
 * @property integer $dialog_id
 * @property integer $mail_id
 *
 * The followings are the available model relations:
 * @property MailTemplate $mail
 * @property AssessmentRule $assessmentRule
 * @property Replica $replica
 */
class AssessmentRuleCondition extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentRuleCondition the static model class
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
		return 'assessment_rule_conditions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assessment_rule_id', 'required'),
			array('assessment_rule_id, dialog_id, mail_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, assessment_rule_id, dialog_id, mail_id', 'safe', 'on'=>'search'),
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
			'mail'           => array(self::BELONGS_TO, 'MailTemplate', 'mail_id'),
			'assessmentRule' => array(self::BELONGS_TO, 'AssessmentRule', 'assessment_rule_id'),
			'dialog'         => array(self::BELONGS_TO, 'Replica', 'dialog_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'assessment_rule_id' => 'Assessment Rule',
			'dialog_id' => 'Replica',
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
		$criteria->compare('assessment_rule_id',$this->assessment_rule_id);
		$criteria->compare('dialog_id',$this->dialog_id);
		$criteria->compare('mail_id',$this->mail_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}