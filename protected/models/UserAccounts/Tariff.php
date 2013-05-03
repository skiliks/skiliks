<?php

/**
 * This is the model class for table "tariff".
 *
 * The followings are the available columns in table 'tariff':
 * @property integer $id
 * @property string $label
 * @property integer $is_free
 * @property string $price
 * @property string $price_usd
 * @property string $safe_amount
 * @property string $safe_amount_usd
 * @property integer $simulations_amount
 * @property string $description
 * @property string $benefits
 * @property string $slug
 *
 * The followings are the available model relations:
 * @property UserAccountCorporate[] $userAccountCorporates
 */
class Tariff extends CActiveRecord
{
    const SLUG_LITE = 'lite';
    const SLUG_STARTER = 'starter';
    const SLUG_PROFESSIONAL = 'professional';
    const SLUG_BUSINESS = 'business';

    /* ----------------------------------------------------------------------------------------------------- */

    public function getPrice()
    {
        return Yii::app()->getLanguage() == 'ru' ? $this->price : $this->price_usd;
    }

    public function getSaveAmount()
    {
        return Yii::app()->getLanguage() == 'ru' ? $this->safe_amount : $this->safe_amount_usd;
    }

    public function getFormattedPrice()
    {
        if ($this->is_free) {
            return Yii::t('site', 'Бесплатно');
        }

        $lang = Yii::app()->getLanguage();
        $currency = $lang == 'ru' ? 'RUB' : 'USD';
        return  StaticSiteTools::getI18nCurrency($this->getPrice(), $currency, $lang);
    }

    public function getFormattedSafeAmount($prefix = '')
    {
        if ($this->is_free) {
            return Yii::t('site', '1 Month free');
        }

        $lang = Yii::app()->getLanguage();
        $currency = $lang == 'ru' ? 'RUB' : 'USD';
        return  $prefix.($lang == 'en' ? '$' : '').StaticSiteTools::getI18nCurrency($this->getSaveAmount(), $currency, $lang, '#').($lang == 'ru' ? ' р' : '');
    }

    public function getFormattedLabel()
    {
        return (null === $this->label) ? 'Не задан' : $this->label;
    }

    /**
     * @return string
     */
    public function getFormattedSimulationsAmount()
    {
        $postfix = '';
        if (self::SLUG_LITE != $this->slug && Yii::app()->getLanguage() == 'ru') {
            $postfix = '*';
        }

        if (null == $this->simulations_amount) {
            return '0 ' . Yii::t('site', 'simulations') . $postfix;
        } else {
            return sprintf('%d %s%s',
                $this->simulations_amount,
                Yii::t('site', 1 == $this->simulations_amount ? 'simulation' : 'simulations'),
                $postfix
            );
        }
    }

    /**
     * @param YumUser$user
     *
     * @return bool
     */
    public function isUserCanChooseTariff($user)
    {
        if (Yii::app()->getLanguage() != 'ru') {
            return false;
        }

        if (self::SLUG_LITE !== $this->slug) {
            return false;
        }

        if (false == $user->isAuth()) {
            return true;
        }

        if ($user->isPersonal()) {
            return true;
        }

        return true;
    }

    /**
     * @param YumUser $user
     * @return string
     */
    public function getFormattedLinkLabel($user)
    {
        if ($user->isCorporate() && null != $user->getAccount()->tariff_id && $this->id === $user->getAccount()->tariff_id) {
            return Yii::t('site', 'Current plan');
        } else {
            return Yii::t('site', 'Subscribe');
        }
    }

    /* ----------------------------------------------------------------------------------------------------- */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tariff the static model class
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
		return 'tariff';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('label, price', 'required'),
			array('is_free, simulations_amount', 'numerical', 'integerOnly'=>true),
			array('label', 'length', 'max'=>120),
			array('price, safe_amount', 'length', 'max'=>10),
			array('currency', 'length', 'max'=>3),
			array('description, benefits', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, label, is_free, price, safe_amount, currency, simulations_amount, description, benefits', 'safe', 'on'=>'search'),
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
			'userAccountCorporates' => array(self::HAS_MANY, 'UserAccountCorporate', 'tariff_id'),
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
			'is_free' => 'Is Free',
			'price' => 'Price',
			'safe_amount' => 'Safe Amount',
			'currency' => 'Currency',
			'simulations_amount' => 'Simulations Value',
			'description' => 'Description',
			'benefits' => 'Benefits',
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
		$criteria->compare('is_free',$this->is_free);
		$criteria->compare('price',$this->price);
		$criteria->compare('safe_amount',$this->safe_amount);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('simulations_amount',$this->simulations_amount);
		$criteria->compare('description',$this->description);
		$criteria->compare('benefits',$this->benefits);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}