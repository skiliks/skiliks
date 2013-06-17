<?php

/**
 * This is the model class for table "user_account_personal".
 *
 * The followings are the available columns in table 'user_account_personal':
 * @property string $user_id
 * @property integer $industry_id
 * @property integer $professional_status_id
 * @property string $birthday
 * @property string $location
 *
 * The followings are the available model relations:
 * @property YumUser $user
 * @property Industry $industry
 * @property ProfessionalStatus $professional_status
 */
class UserAccountPersonal extends CActiveRecord
{

    public function getTariffLabel()
    {
        return null;
    }

    /* ---------------------------------------------------------------------------------------------------- */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserAccountPersonal the static model class
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
		return 'user_account_personal';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id'                 , 'required'),
			//array('industry_id'           , 'required', 'message' => Yii::t('site', 'Industry is required')),
			array('professional_status_id', 'required', 'message' => Yii::t('site', 'Professional status is required')),
			array('user_id'                 , 'length'   , 'max'=>10),
			array('location'                 , 'length'   , 'max'=>255),
			array('birthday'                 , 'date'   , 'format'=>'yyyy-M-d'),
            array('birthday', 'validBirthday', 'type' => 'date', 'message' => '{attribute}: is not a date!', 'dateFormat' => 'yyyy-MM-dd'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, industry_id, professional_status_id,birthday,location', 'safe', 'on'=>'search'),
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
			'user'     => array(self::BELONGS_TO, 'YumUser'    , 'user_id'),
			'industry' => array(self::BELONGS_TO, 'Industry', 'industry_id'),
			'professional_status' => array(self::BELONGS_TO, 'ProfessionalStatus', 'professional_status_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id'     => Yii::t('site', 'User'),
			'industry_id' => Yii::t('site', 'Professional area'),
			'professional_status_id' => Yii::t('site', 'Professional status'),
			'birthday' => Yii::t('site', 'Birthday'),
			'location' => Yii::t('site', 'Location'),
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('industry_id',$this->industry_id);
		$criteria->compare('professional_status_id',$this->professional_status);
		$criteria->compare('birthday',$this->birthday);
		$criteria->compare('location',$this->location);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @return DateTime|null
     */
    public function getBirthdayDate($format)
    {
        if(empty($this->birthday)){
            return '';
        }else{
            return (new DateTime($this->birthday))->format($format);
        }
    }

    public function setBirthdayDate($date)
    {
        $this->birthday = $date['year'].'-'.$date['month'].'-'.$date['day'];
    }

    public function validBirthday($attribute, $params) {

            $date = explode('-', $this->attributes[$attribute]);
            if(checkdate((int)$date[1], (int)$date[2], (int)$date[0])){
                if(strtotime($this->attributes[$attribute]) >= strtotime('1910-01-01') && strtotime($this->attributes[$attribute]) <= strtotime('2010-01-01')) {
                    return true;
                }else{
                    $this->birthday = null;
                    $this->addError('birthday[day]', Yii::t('site', 'Incorrect birthday'));
                    $this->addError('birthday[month]', Yii::t('site', 'Incorrect birthday'));
                    $this->addError('birthday[year]', Yii::t('site', 'Incorrect birthday'));
                }
            }else{
                $this->birthday = null;
                $this->addError('birthday[day]', Yii::t('site', 'Incorrect birthday'));
                $this->addError('birthday[month]', Yii::t('site', 'Incorrect birthday'));
                $this->addError('birthday[year]', Yii::t('site', 'Incorrect birthday'));

            }

    }
}