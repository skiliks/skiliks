<?php

/**
 * This is the model class for table "decline_reason".
 *
 * The followings are the available columns in table 'decline_reason':
 * @property integer $id
 * @property string $label
 * @property integer $sort_order
 * @property integer $is_display
 *
 * The followings are the available model relations:
 * @property DeclineExplanation[] $declineExplanations
 */
class DeclineReason extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DeclineReason the static model class
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
		return 'decline_reason';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('label', 'required'),
			array('sort_order, is_display', 'numerical', 'integerOnly'=>true),
			array('label', 'length', 'max'=>120),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, label, sort_order, is_display', 'safe', 'on'=>'search'),
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
			'declineExplanations' => array(self::HAS_MANY, 'DeclineExplanation', 'reason_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'label' => 'Label',
			'sort_order' => 'Sort Order',
			'is_display' => 'Is Display',
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
		$criteria->compare('label',$this->label);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('is_display',$this->is_display);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}