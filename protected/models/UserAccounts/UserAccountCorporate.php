<?php

/**
 * This is the model class for table "user_account_corporate".
 *
 * The followings are the available columns in table 'user_account_corporate':
 * @property string $user_id
 * @property string $tariff_id
 * @property integer $industry_id
 * @property string $ownership_type
 * @property string $company_name
 * @property integer $invites_limit
 * @property integer $referrals_invite_limit
 * @property string $tariff_activated_at
 * @property string $tariff_expired_at
 * @property string $inn
 * @property string $cpp
 * @property string $bank_account_number
 * @property string $bic
 * @property string $preference_payment_method
 * @property string $default_invitation_mail_text
 * @property integer $is_display_referrals_popup
 * @property integer $is_display_tariff_expire_pop_up
 * @property string $expire_invite_rule
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

    const EXPIRE_INVITE_RULE_STANDARD = 'standard';

    const EXPIRE_INVITE_RULE_BY_TARIFF = 'by_tariff';

    /**
     * @return string
     */
    public function getTariffLabel()
    {
        return (null === $this->tariff) ? 'Не задан' : $this->tariff->getFormattedLabel();
    }

    /**
     * @param Tariff $tariff
     * @param bool $isSave
     */
    public function setTariff(Tariff $tariff, $isSave = false)
    {
        $this->tariff_id = $tariff->id;
        $this->tariff_activated_at = (new DateTime())->format("Y-m-d H:i:s");
        $this->tariff_expired_at = (new DateTime())->modify('+30 days')->format("Y-m-d H:i:s");
        $this->invites_limit = $tariff->simulations_amount;
        if($tariff->slug === Tariff::SLUG_FREE) {
            $this->is_display_tariff_expire_pop_up = 1;
        } else {
            $this->is_display_tariff_expire_pop_up = 0;
        }

        $tariff_plan = TariffPlan::model()->findByAttributes(['user_id'=>$this->user_id, 'status'=>TariffPlan::STATUS_ACTIVE]);
        if(null !== $tariff_plan) {
            /* @var $tariff_plan TariffPlan */
            $tariff_plan->status = TariffPlan::STATUS_EXPIRED;
            $tariff_plan->save(false);
        }

        $tariff_plan = new TariffPlan();
        $tariff_plan->user_id = $this->user_id;
        $tariff_plan->tariff_id = $this->tariff_id;
        $tariff_plan->started_at = $this->tariff_activated_at;
        $tariff_plan->finished_at = $this->tariff_expired_at;
        $tariff_plan->status = TariffPlan::STATUS_ACTIVE;
        $tariff_plan->save(false);

        if ($isSave) {

            if(false === $this->save(false)){
                throw new Exception(sprintf(
                    "Tariff #%s for account #%s was not set. ",
                    $tariff->id,
                    $this->id
                ));
            }
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

    /**
     * @param $attribute
     */
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
        $this->invites_limit++;
        $this->save(false, ['invites_limit']);
    }

    /**
     * @return bool
     */
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
    }

    /**
     * @param null $referrer_email
     */
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

    /**
     * Returns all user invites limit
     * @return int
     */
    public function getTotalAvailableInvitesLimit() {
        return $this->invites_limit + $this->referrals_invite_limit;
    }

    /**
     * banning corporate user and saves it
     */
    public function banUser() {
        $this->disableUserInviteLimit();
        $this->expireUserTariff();
        $this->disableUserInvites();
        $this->save();
    }

    /**
     * Disable user invites and if there are Simulation in progress it's interrupt it
     */
    private function disableUserInvites() {
        $invites = $this->getAllUserInvites();

        foreach($invites as $invite) {

            if($invite->status === Invite::STATUS_IN_PROGRESS) {
                $invite->simulation->interruptSimulation();
            }

            $invite->deleteInvite();
        }
    }

    /**
     * Returns all current user invites
     * @return array|CActiveRecord|mixed|null
     */
    private function getAllUserInvites() {
        return Invite::model()->findAllByAttributes(['owner_id'=>$this->user_id]);
    }

    /**
     * Setting user invite limit to zero, and logging the corporate invite moves
     */
    private function disableUserInviteLimit() {

        $initValue = $this->getTotalAvailableInvitesLimit();

        // Getting user email, that provides deleting the invites
        $providerEmail = Yii::app()->user->data()->profile->email;

        $this->invites_limit = 0;
        $this->referrals_invite_limit = 0;

        UserService::logCorporateInviteMovementAdd(
            sprintf("Аккаунт заблокирован пользователем-админом %s", $providerEmail),
            $this,
            $initValue
        );
    }

    /**
     * Setting user tariff expired yesterday
     */
    private function expireUserTariff() {
        $date = new DateTime();
        $date->add(DateInterval::createFromDateString('yesterday'));
        $this->tariff_expired_at = $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $count
     */
    public function addSimulations($count) {
        $this->invites_limit = $this->invites_limit + $count;
        $this->save(false);
    }

    /**
     * @param $value, integer, can be positive and negative
     * @param null $admin
     */
    public function changeInviteLimits($value, $admin=null) {

        $initValue = $this->getTotalAvailableInvitesLimit();
        if($value > 0) {
            $this->invites_limit += $value;
        } elseif($value < 0) {
            if($this->invites_limit >= -$value) {
                $this->invites_limit += $value;
            }else{
                $diff = $value + $this->invites_limit;
                $this->invites_limit = 0;
                if($this->referrals_invite_limit < -$diff){
                    $this->referrals_invite_limit = 0;
                }else{
                    $this->referrals_invite_limit += $diff;
                }
            }

        }
        $this->save(false);
        if(null !== $admin){
            UserService::logCorporateInviteMovementAdd(
                sprintf('Количество доступных симуляций установлено в %s в админ области, из них за рефераллов %s. '.
                ' Админ %s (емейл текущего авторизованного в админке пользователя).', $this->invites_limit, $this->referrals_invite_limit, $admin->profile->email),
                $this,
                $initValue
            );
        }

        Yii::app()->user->setFlash('success', sprintf(
            'Количество доступных симуляций для "%s %s" установнено %s.',
            $this->user->profile->firstname,
            $this->user->profile->lastname,
            $this->invites_limit
        ));

    }

    /**
     * @return null|Tariff
     */
    public function getActiveTariff() {

        $tariff_plan = $this->getActiveTariffPlan();

        if(null !== $tariff_plan){
            /* @var $tariff_plan TariffPlan */
            return $tariff_plan->tariff;
        }else{
            return null;
        }
    }

    /**
     * @return TariffPlan
     */
    public function getActiveTariffPlan() {

        return TariffPlan::model()->findByAttributes(['user_id'=>$this->user_id, 'status' => TariffPlan::STATUS_ACTIVE]);

    }

    /**
     * @return null|Tariff
     */
    public function getPendingTariff() {

        $tariff_plan = $this->getPendingTariffPlan();

        if(null !== $tariff_plan){
            /* @var $tariff_plan TariffPlan */
            return $tariff_plan->tariff;
        }else{
            return null;
        }
    }

    /**
     * @return null|TariffPlan
     */
    public function getPendingTariffPlan() {
        return TariffPlan::model()->findByAttributes(['user_id'=>$this->user_id, 'status' => TariffPlan::STATUS_PENDING]);
    }

    /**
     * @return null|TariffPlan
     */
    public function hasPendingTariffPlan() {
        $tariff_plan = TariffPlan::model()->findByAttributes(['user_id'=>$this->user_id, 'status' => TariffPlan::STATUS_PENDING]);
        return null !== $tariff_plan;
    }

    /**
     * @param Tariff $tariff
     */
    public function addPendingTariff(Tariff $tariff) {
        $active_tariff = TariffPlan::model()->findByAttributes(['user_id'=>$this->user_id, 'status'=>TariffPlan::STATUS_ACTIVE]);
        /* @var $active_tariff TariffPlan */
        $tariff_plan = new TariffPlan();
        $tariff_plan->user_id = $this->user_id;
        $tariff_plan->tariff_id = $tariff->id;
        $tariff_plan->started_at = $active_tariff->finished_at;
        $tariff_plan->finished_at = (new DateTime($active_tariff->finished_at))->modify('+30 days')->format("Y-m-d H:i:s");
        $tariff_plan->status = TariffPlan::STATUS_PENDING;
        $tariff_plan->save(false);
    }

    /**
     * @param Invoice $invoice
     * @param TariffPlan $tariff_plan
     */
    public function setInvoiceOnTariffPlan(Invoice $invoice, TariffPlan $tariff_plan) {
        $tariff_plan->invoice_id = $invoice->id;
        $tariff_plan->save(false);
    }

    /**
     * @return TariffPlan[]
     */
    public function getAllTariffPlans() {

        return TariffPlan::model()->findAllByAttributes(['user_id'=>$this->user_id]);
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
			array('industry_id' , 'numerical', 'integerOnly'=>true),
            array('industry_id' , 'required', 'on' => ['registration', 'corporate'], 'message' => Yii::t('site', 'Выберите отрасль')),
			array('user_id'     , 'length'   , 'max'=>10, 'on' => ['registration', 'corporate']),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
            array('ownership_type, company_name, invites_limit, referrals_invite_limit, tariff_expired_at', 'safe'),
            array('inn, cpp, bank_account_number, bic, preference_payment_method', 'safe'),
            array('default_invitation_mail_text, is_display_referrals_popup, is_display_tariff_expire_pop_up', 'safe'),
			array('user_id, industry_id', 'safe', 'on'=>'search'),
            array('ownership_type', 'length', 'max' => 50),
            array('company_name', 'length', 'max' => 50),
            array('company_description', 'length', 'max' => 250),
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

    public function getNumberOfPaidOrders() {
        return Invoice::model()->count("user_id = {$this->user_id} and paid_at is not null");
    }

    public function getNumberOfInvitationsSent() {
        return Invite::model()->count("owner_id = {$this->user_id}");
    }

    public function getNumberOfFullSimulationsForSelf() {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        return Invite::model()->count("scenario_id = {$scenario->id} and owner_id = {$this->user_id} and owner_id = receiver_id and status = ".Invite::STATUS_COMPLETED);
    }

    public function getNumberOfFullSimulationsForPeople() {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        return Invite::model()->count("scenario_id = {$scenario->id} and owner_id = {$this->user_id} and owner_id != receiver_id and status = ".Invite::STATUS_COMPLETED);
    }
}