<?php

/**
 * This is the model class for table "user_account_corporate".
 *
 * The followings are the available columns in table 'user_account_corporate':
 * @property string $user_id
 * @property integer $industry_id
 * @property string $corporate_email
 * @property boolean is_corporate_email_verified
 * @property datetime corporate_email_verified_at
 * @property boolean corporate_email_activation_code
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Industry $industry
 */
class UserAccountCorporate extends CActiveRecord
{

    /* ----------------------------------------------------------------------------------------------------- */

    /**
     * @return string
     */
    public function generateActivationKey()
    {
        $this->corporate_email_activation_code = YumEncrypt::encrypt(microtime().$this->corporate_email, $this->user->salt);

        if (!$this->isNewRecord) {
            $this->save(false, array('activationKey'));
        }

        return $this->corporate_email_activation_code;
    }

    /* ----------------------------------------------------------------------------------------------------- */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserAccountCorporate the static model class
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
		return 'user_account_corporate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id'         , 'required'),
			array('corporate_email' , 'required'),
			array('corporate_email' , 'unique'),
            array('corporate_email' , 'CEmailValidator'),
            array('corporate_email' , 'isCorporateEmail'),
			array('industry_id'     , 'numerical', 'integerOnly'=>true),
			array('user_id'         , 'length'   , 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, industry_id', 'safe', 'on'=>'search'),
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
			'user'     => array(self::BELONGS_TO, 'YumUser' , 'user_id'),
			'industry' => array(self::BELONGS_TO, 'Industry', 'industry_id'),
			'tariff' => array(self::BELONGS_TO, 'Tariff', 'tariff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id'             => Yii::t('site', 'User'),
			'corporate_email'     => Yii::t('site', 'Corporate email'),
			'industry_id'         => Yii::t('site', 'Industry'),
			'company_size_id'     => Yii::t('site', 'Company Size'),
			'company_description' => Yii::t('site', 'Company description'),
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('industry_id',$this->industry_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param $attribute, attribute name
     */
    public function isCorporateEmail($attribute)
    {
        if(false == UserService::isCorporateEmail($this->$attribute)) {
            $this->addError($attribute, Yii::t('site', 'This is free e-mail! Type your corporate e-mail.'));
        }
    }
}