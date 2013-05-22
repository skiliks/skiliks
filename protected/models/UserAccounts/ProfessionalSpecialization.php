<?php

/**
 * This is the model class for table "professional_specialization".
 *
 * The followings are the available columns in table 'professional_specialization':
 * @property integer $id
 * @property integer $professional_occupation_id
 * @property string $label
 *
 * The followings are the available model relations:
 * @property ProfessionalOccupation $professionalOccupation
 * @property Vacancy[] $vacancies
 */
class ProfessionalSpecialization extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProfessionalSpecialization the static model class
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
		return 'professional_specialization';
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
			array('label', 'length', 'max'=>120)
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
			'vacancies' => array(self::HAS_MANY, 'Vacancy', 'professional_specialization_id'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}