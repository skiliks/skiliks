<?php

/**
 * This is the model class for table "assessment_category".
 *
 * The followings are the available columns in table 'assessment_category':
 * @property string $code
 *
 * The followings are the available model relations:
 * @property AssessmentOverall[] $assessmentOveralls
 * @property Weight[] $weights
 */
class AssessmentCategory extends CActiveRecord
{
    const MANAGEMENT_SKILLS  = 'management';
    const PRODUCTIVITY       = 'performance';
    const TIME_EFFECTIVENESS = 'time';
    const PERSONAL           = 'personal'; // Does not calculate, just for slug!
    const OVERALL            = 'overall';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AssessmentCategory the static model class
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
		return 'assessment_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code', 'required'),
			array('code', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('code', 'safe', 'on'=>'search'),
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
			'assessmentOveralls' => array(self::HAS_MANY, 'AssessmentOverall', 'assessment_category_code'),
			'weights' => array(self::HAS_MANY, 'Weight', 'assessment_category_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'code' => 'Code',
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

		$criteria->compare('code',$this->code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}