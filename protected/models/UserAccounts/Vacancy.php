<?php

/**
 * This is the model class for table "vacancy".
 *
 * The followings are the available columns in table 'vacancy':
 * @property integer $id
 * @property integer $professional_occupation_id
 * @property integer $professional_specialization_id
 * @property string $label
 * @property string $link
 * @property string $import_id
 * @property integer $user_account_corporate_id
 * @property string position_level_slug
 *
 * The followings are the available model relations:
 * @property ProfessionalOccupation $professionalOccupation
 * @property ProfessionalSpecialization $professionalSpecialization
 * @property PositionLevel $positionLevel
 */
class Vacancy extends CActiveRecord
{
    /** ------------------------------------------------------------------------------------------- */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Vacancy the static model class
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
		return 'vacancy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('label', 'required', 'message' => Yii::t('site', 'Vacancy title is required')),
			array('position_level_slug', 'required', 'message' => Yii::t('site', 'Position level is required')),
			array('professional_occupation_id', 'required', 'message' => Yii::t('site', 'Professional occupation is required')),
			array('professional_specialization_id', 'required', 'message' => Yii::t('site', 'Specialization is required')),
			array('professional_occupation_id, professional_specialization_id', 'numerical', 'integerOnly'=>true),
            array('professional_occupation_id',  'numerical', 'min' => 1, 'message' => '{attribute} cannot be blank.'),
			array('label', 'length', 'max' => 120),
			array('import_id', 'length', 'max' => 60),
			array('link', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, professional_occupation_id, professional_specialization_id, label, link, import_id', 'safe', 'on'=>'search'),
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
			'professionalOccupation'     => array(self::BELONGS_TO, 'ProfessionalOccupation', 'professional_occupation_id'),
			'professionalSpecialization' => array(self::BELONGS_TO, 'ProfessionalSpecialization', 'professional_specialization_id'),
			'userAccountCorporate'       => array(self::BELONGS_TO, 'UserAccountCorporate', 'user_account_corporate_id'),
			'positionLevel'       => array(self::BELONGS_TO, 'PositionLevel', 'position_level_slug'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                             => Yii::t('site', 'ID'),
			'professional_occupation_id'     => Yii::t('site', 'Industry'),
			'professional_specialization_id' => Yii::t('site', 'Specialization'),
			'position_level_slug'            => Yii::t('site', 'Position level'),
			'label'                          => Yii::t('site', 'Vacancy label'),
			'link'                           => Yii::t('site', 'Link to vacancy description'),
			'import_id'                      => Yii::t('site', 'Import'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($userId = null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('professional_occupation_id',$this->professional_occupation_id);
		$criteria->compare('professional_specialization_id',$this->professional_specialization_id);
		$criteria->compare('label',$this->label);
		$criteria->compare('link',$this->link);
		$criteria->compare('import_id',$this->import_id);
		$criteria->compare('user_id', $userId ?: $this->user_id);

		return new CActiveDataProvider($this, [
			'criteria'   => $criteria,
            'pagination' => [
                'pageSize' => 20,
                'pageVar'  => 'page'
            ]
		]);
	}

    public function byUser($userId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'user_id = :userId',
            'params' => ['userId' => $userId]
        ));
        return $this;
    }
}