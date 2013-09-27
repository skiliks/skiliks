<?php

/**
 * This is the model class for table "flag_switch_time".
 *
 * The followings are the available columns in table 'flag_switch_time':
 * @property integer $id
 * @property string $flag_code
 * @property integer $value
 * @property string $time
 * @property integer $scenario_id
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 * @property Flag $flagCode
 */
class FlagSwitchTime extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FlagSwitchTime the static model class
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
		return 'flag_switch_time';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('flag_code, scenario_id, import_id', 'required'),
			array('value, scenario_id', 'numerical', 'integerOnly'=>true),
			array('flag_code', 'length', 'max'=>10),
			array('import_id', 'length', 'max'=>14),
			array('time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, flag_code, value, time, scenario_id, import_id', 'safe', 'on'=>'search'),
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
			'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
			'flagCode' => array(self::BELONGS_TO, 'Flag', 'flag_code'),
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
			'value' => 'Value',
			'time' => 'Time',
			'scenario_id' => 'Scenario',
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
		$criteria->compare('flag_code',$this->flag_code,true);
		$criteria->compare('value',$this->value);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('scenario_id',$this->scenario_id);
		$criteria->compare('import_id',$this->import_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}