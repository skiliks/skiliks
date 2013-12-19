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
 * @property string $weight
 * @property string $is_display_on_tariffs_page
 *
 * The followings are the available model relations:
 * @property UserAccountCorporate[] $userAccountCorporates
 */
class Tariff extends CActiveRecord
{
    const SLUG_FREE = 'free';
    const SLUG_LITE_FREE = 'lite_free';
    const SLUG_LITE = 'lite';
    const SLUG_STARTER = 'starter';
    const SLUG_PROFESSIONAL = 'professional';
    const SLUG_BUSINESS = 'business';

    public static $tarifs = [
        self::SLUG_FREE,
        self::SLUG_LITE_FREE,
        self::SLUG_LITE,
        self::SLUG_STARTER,
        self::SLUG_PROFESSIONAL,
        self::SLUG_BUSINESS,
    ];

    /* ----------------------------------------------------------------------------------------------------- */

    /**
     * Возвращает цену в валюте, согластно текущей локали пользователя
     *
     * @param string $lang, ISO2: 'ru','en'
     *
     * @return string
     */
    public function getPrice($lang)
    {
        return $lang == 'ru' ? $this->price : $this->price_usd;
    }

    /**
     * Возвращает денежную экономию в валюте, согластно текущей локали пользователя
     * (для рекламмы тарифа на странице цен и тарифов)
     *
     * @param string $lang, ISO2: 'ru','en'
     *
     * @return string
     */
    public function getSaveAmount($lang)
    {
        return $lang == 'ru' ? $this->safe_amount : $this->safe_amount_usd;
    }

    /**
     * Возвращает цену в валюте, согластно текущей локали пользователя + обозначение валюты
     *
     * @param bool $withCurrency
     * @param string $lang, ISO2: 'ru','en'
     *
     * @return string
     */
    public function getFormattedPrice($lang, $withCurrency = false)
    {
        /* почему здесь не используется getPrice() ? */
        $price = $this->getPrice($lang);

        // использовать getFormattedCurrencyName() нельзя,
        // така как у цены рублях подпись валюты пошется за цифрой,
        // а у цены в долларах - перед
        if ($withCurrency) {
            if ($lang == 'ru') {
                $price .= ' р';
            } else {
                $price = '$' . $price;
            }
        }

        return $price;
    }

    /**
     * Возвращает обозначение валюты, согластно текущей локали пользователя
     *
     * @param string $lang, ISO2: 'ru','en'
     * @return string
     */
    public function getFormattedCurrencyName($lang)
    {
        if ($lang == 'ru') {
            $price = 'р';
        } else {
            $price = '$';
        }

        return $price;
    }

    /**
     * Возвращает денежную экономию в валюте, согластно текущей локали пользователя + обозначение валюты
     *
     * @param string $prefix
     * @param string $lang, ISO2: 'ru','en'
     *
     * @return string
     */
    public function getFormattedSafeAmount($lang, $prefix = '')
    {
        if ($this->is_free) {
            return Yii::t('site', '1 Month free');
        }

        $currency = $lang == 'ru' ? 'RUB' : 'USD';
        return  $prefix.($lang == 'en' ? '$' : '').StaticSiteTools::getI18nCurrency($this->getSaveAmount($lang), $currency, $lang, '#').($lang == 'ru' ? ' р' : '');
    }

    /**
     * @return string
     */
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
            $postfix = '';
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
        if (!$user->isAuth()) {
            return false;
        }

        if ($user->isCorporate()) {
            return true;
        }

        return false;
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

    /**
     * Определяет, показывать литарифф на странице Тарифы и цены
     *
     * @return bool
     */
    public function isDisplayOnTariffsPage() {
        return $this->is_display_on_tariffs_page === '1';
    }

    /**
     * Пользователь НЕ может продливать тарифные планы LITE и FREE LITE
     *
     * @return bool
     */
    public function isCanBeProlonged() {
        if (self::SLUG_LITE_FREE == $this->slug || self::SLUG_FREE == $this->slug) {
            return false;
        }

        return true;
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