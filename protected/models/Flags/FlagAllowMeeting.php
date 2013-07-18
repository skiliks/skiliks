<?php

/**
 * This is the model class for table "flag_allow_meeting".
 *
 * The followings are the available columns in table 'flag_allow_meeting':
 * @property integer $id
 * @property string $flag_code
 * @property integer $meeting_id
 * @property integer $value
 * @property string $import_id
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property Meeting $meeting
 * @property Flag $flag
 */
class FlagAllowMeeting extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FlagAllowMeeting the static model class
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
		return 'flag_allow_meeting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('flag_code, meeting_id, import_id, scenario_id', 'required'),
			array('meeting_id, value, scenario_id', 'numerical', 'integerOnly'=>true),
			array('flag_code', 'length', 'max'=>10),
			array('import_id', 'length', 'max'=>14),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, flag_code, meeting_id, value, import_id, scenario_id', 'safe', 'on'=>'search'),
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
			'flag' => array(self::BELONGS_TO, 'Flag', 'flag_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'flag_code' => 'Flag Code',
			'meeting_id' => 'Meeting',
			'value' => 'Value',
			'import_id' => 'Import',
			'scenario_id' => 'Scenario',
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
		$criteria->compare('flag_code',$this->flag_code,true);
		$criteria->compare('meeting_id',$this->meeting_id);
		$criteria->compare('value',$this->value);
		$criteria->compare('import_id',$this->import_id,true);
		$criteria->compare('scenario_id',$this->scenario_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}