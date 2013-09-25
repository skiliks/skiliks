<?php

/**
 * This is the model class for table "position_level".
 *
 * The followings are the available columns in table 'position_level':
 * @property string $slug
 * @property string $label
 *
 * The followings are the available model relations:
 * @property Vacancy[] $vacancies
 */
class PositionLevel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PositionLevel the static model class
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
		return 'position_level';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('slug, label', 'required'),
			array('slug', 'length', 'max'=>50),
			array('label', 'length', 'max'=>120),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('slug, label', 'safe', 'on'=>'search'),
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
			'vacancies' => array(self::HAS_MANY, 'Vacancy', 'position_level_slug'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'slug' => 'Slug',
			'label' => 'Label',
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

		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('label',$this->label,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}