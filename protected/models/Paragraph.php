<?php

/**
 * This is the model class for table "paragraph".
 *
 * The followings are the available columns in table 'paragraph':
 * @property integer $id
 * @property integer $scenario_id
 * @property string $alias
 * @property string $type
 * @property string $label
 * @property integer $order_number
 * @property string $value_1
 * @property string $value_2
 * @property string $value_3
 * @property string $method
 * @property string $import_id
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 */
class Paragraph extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Paragraph the static model class
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
		return 'paragraph';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('scenario_id', 'required'),
			array('scenario_id, order_number', 'numerical', 'integerOnly'=>true),
			array('alias, label, value_1, value_2, value_3, method', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, scenario_id, alias, label, order_number, value_1, value_2, value_3, method', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'scenario_id' => 'Scenario',
			'alias' => 'Alias',
			'label' => 'Label',
			'order_number' => 'Order Number',
			'value_1' => 'Value 1',
			'value_2' => 'Value 2',
			'value_3' => 'Value 3',
			'method' => 'Method',
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
		$criteria->compare('scenario_id',$this->scenario_id);
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('order_number',$this->order_number);
		$criteria->compare('value_1',$this->value_1,true);
		$criteria->compare('value_2',$this->value_2,true);
		$criteria->compare('value_3',$this->value_3,true);
		$criteria->compare('method',$this->method,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}