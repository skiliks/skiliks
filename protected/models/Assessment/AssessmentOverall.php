<?php

/**
 * This is the model class for table "assessment_overall".
 *
 * The followings are the available columns in table 'assessment_overall':
 * @property integer $id
 * @property integer $sim_id
 * @property string $assessment_category_code
 * @property float $value
 *
 * The followings are the available model relations:
 * @property AssessmentCategory $assessmentCategoryCode
 * @property Simulation $sim
 */
class AssessmentOverall extends CActiveRecord
{
    const CODE_OVERALL = 'overall';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentOverall the static model class
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
		return 'assessment_overall';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sim_id', 'numerical', 'integerOnly'=>true),
			array('assessment_category_code', 'length', 'max'=>50),
			array('value', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sim_id, assessment_category_code, value', 'safe', 'on'=>'search'),
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
			'assessmentCategoryCode' => array(self::BELONGS_TO, 'AssessmentCategory', 'assessment_category_code'),
			'sim' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sim_id' => 'Sim',
			'assessment_category_code' => 'Assessment Category Code',
			'value' => 'Value',
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
		$criteria->compare('sim_id',$this->sim_id);
		$criteria->compare('assessment_category_code',$this->assessment_category_code,true);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}