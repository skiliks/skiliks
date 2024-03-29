<?php

/**
 * This is the model class for table "stress_rule".
 *
 * The followings are the available columns in table 'stress_rule':
 * @property integer $id
 * @property integer $replica_id
 * @property integer $mail_id
 * @property integer $value
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property StressPoint[] $stressPoints
 * @property MailTemplate $mail
 * @property Replica $replica
 */
class StressRule extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StressRule the static model class
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
		return 'stress_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('replica_id, mail_id, value', 'numerical', 'integerOnly'=>true),
			array('import_id', 'length', 'max'=>14),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, replica_id, mail_id, value, import_id', 'safe', 'on'=>'search'),
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
			'stressPoints' => array(self::HAS_MANY, 'StressPoint', 'stress_rule_id'),
			'mail' => array(self::BELONGS_TO, 'MailTemplate', 'mail_id'),
			'replica' => array(self::BELONGS_TO, 'Replica', 'replica_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'replica_id' => 'Replica',
			'mail_id' => 'Mail',
			'value' => 'Value',
			'import_id' => 'Import',
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
		$criteria->compare('replica_id',$this->replica_id);
		$criteria->compare('mail_id',$this->mail_id);
		$criteria->compare('value',$this->value);
		$criteria->compare('import_id',$this->import_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}