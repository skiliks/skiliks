<?php

/**
 * This is the model class for table "user_account_corporate".
 *
 * The followings are the available columns in table 'user_account_corporate':
 * @property string $user_id
 * @property integer $industry_id
 * @property string $ownership_type
 * @property string $company_name
 * @property integer $invites_limit
 * @property integer $referrals_invite_limit
 * @property datetime $tariff_expired_at
 * @property string $inn
 * @property string $cpp
 * @property string $bank_account_number
 * @property string $bic
 * @property string $preference_payment_method
 * @property string $default_invitation_mail_text
 * @property integer $is_display_referrals_popup
 * @property integer $is_display_tariff_expire_pop_up
 *
 * The followings are the available model relations:
 * @property YumUser $user
 * @property Industry $industry
 * @property Tariff tariff
 */
class UserAccountCorporate extends CActiveRecord
{
    const PAYMENT_METHOD_INVOICE = "invoice";
    const PAYMENT_METHOD_CARD = "card";
    public function getTariffLabel()
    {
        return (null === $this->tariff) ? 'Не задан' : $this->tariff->getFormattedLabel();
    }

    /**
     * @param Tariff $tariff
     * @param bool $isSave
     */
    public function setTariff($tariff, $isSave = false)
    {
        $this->tariff_id = $tariff->id;
        $this->tariff_activated_at = (new DateTime())->format("Y-m-d H:i:s");
        $this->tariff_expired_at = (new DateTime())->modify('+30 days')->format("Y-m-d H:i:s");

        $initValue = $this->getTotalAvailableInvitesLimit();

        $this->invites_limit = $tariff->simulations_amount;

        if ($isSave) {

            if(false === $this->save(false)){
                throw new Exception("Not save Tariff");
            }

//            UserService::logCorporateInviteMovementAdd(
//                'Account setTariff and save',
//                $this->user->getAccount(),
//                $initValue
//            );

        } else {
//            UserService::logCorporateInviteMovementAdd(
//                'Account setTariff but not save (?)',
//                $this->user->getAccount(),
//                $initValue
//            );
        }


    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        if (null == $this->ownership_type && null == $this->company_name) {
            return 'Компания';
        }

        return sprintf(
            '%s %s',
            $this->ownership_type,
            $this->company_name
        );
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
			array('user_id'     , 'required', 'on' => ['insert', 'update', 'corporate']),
			array('industry_id' , 'numerical', 'integerOnly'=>true, 'on' => ['registration', 'insert', 'update', 'corporate']),
            array('industry_id' , 'required', 'on' => ['registration', 'corporate'], 'message' => Yii::t('site', 'Выберите отрасль')),
			array('user_id'     , 'length'   , 'max'=>10, 'on' => ['registration', 'corporate']),
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
			'industry_id'         => 'Отрасль',
			'company_size_id'     => Yii::t('site', 'Размер компании'),
			'company_description' => Yii::t('site', 'Описание компании'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param $attribute, attribute name
     */
    public function isCorporateEmail($attribute)
    {
        // для тестировщиков, мы вообще не проверяем емейл на корпоративность
        if (isset(Yii::app()->request->cookies['anshydjcyfhbxnfybjcbsgcusb27djxhds9dshbc7ubwbcd7034n9'])) {
            return;
        }

        if(false == UserService::isCorporateEmail($this->$attribute)) {
            $this->addError($attribute, Yii::t('site', 'Type your corporate e-mail'));
        }
    }

    public function isNotPersonalEmail($attribute)
    {
        $userPersonal = YumProfile::model()->findByAttributes(["email" => $this->$attribute]);
        if($userPersonal !== null && $userPersonal->user_id !== $this->user_id) {
            $this->addError($attribute, Yii::t('site', 'Email is already taken'));
        }
    }

    /**
     * invite STATUS_PENDING - return invite
     * invite STATUS_ACCEPTED - invite spent
     * invite STATUS_COMPLETED - invite spent
     * invite STATUS_DECLINED - invite already returned while decline
     * invite STATUS_EXPIRED - invite already returned while mark expired
     * invite STATUS_STARTED - invite spent
     *
     * @param Invite $invite
     */
    public function increaseLimit($invite)
    {
        if (Invite::STATUS_PENDING == $invite->status) {

            $initValue = $this->getTotalAvailableInvitesLimit();

            $this->invites_limit++;
            $this->save(false, ['invites_limit']);

            // TODO уточнить у славы!
//            UserService::logCorporateInviteMovementAdd(
//                'Увеличен лимит ',
//                $this->user->getAccount(),
//                $initValue
//            );
        }
    }

    public function decreaseLimit()
    {

        $initValue = $this->getTotalAvailableInvitesLimit();

        if($this->invites_limit > 0) {
            $this->invites_limit--;
            $this->save(false, ['invites_limit']);
        }
        elseif($this->referrals_invite_limit > 0) {
            $this->referrals_invite_limit--;
            $this->save(false, ['referrals_invite_limit']);
        }
        else {
            Yii::log("User doesn't have invites but tried to decrease it");
            return false;
        }

        UserService::logCorporateInviteMovementAdd(
            'increaseLimit',
            $this->user->getAccount(),
            $initValue
        );
    }

    public function getTotalAvailableInvitesLimit() {
        return $this->invites_limit + $this->referrals_invite_limit;
    }

    public function addReferralInvite($referrer_email = null) {

        $initValue = $this->getTotalAvailableInvitesLimit();
        $this->referrals_invite_limit++;
        $this->save(false, ['referrals_invite_limit']);

        UserService::logCorporateInviteMovementAdd(
            'Регистрация реферала ' . $referrer_email,
            $this,
            $initValue
        );

        $this->save();
    }
}