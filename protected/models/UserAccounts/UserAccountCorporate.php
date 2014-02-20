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
 * @property string $inn
 * @property string $cpp
 * @property string $bank_account_number
 * @property string $bic
 * @property string $preference_payment_method
 * @property string $default_invitation_mail_text
 * @property string $expire_invite_rule
 * @property string $site
 * @property string $description_for_sales
 * @property string $contacts_for_sales
 * @property string $status_for_sales
 * @property string $company_name_for_sales
 * @property string $industry_for_sales
 * The followings are the available model relations:
 * @property YumUser $user
 * @property Industry $industry
 */
class UserAccountCorporate extends CActiveRecord
{
    const PAYMENT_METHOD_INVOICE = "invoice";
    const PAYMENT_METHOD_CARD = "card";

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
        if($this->invites_limit > 0) {
            $this->invites_limit--;
            $this->save(false, ['invites_limit']);
        }
        else {
            Yii::log("User doesn't have invites but tried to decrease it");
            return false;
        }
    }

    /**
     * Returns all user invites limit
     * @return int
     */
    public function getTotalAvailableInvitesLimit() {
        return $this->invites_limit;
    }

    /**
     * banning corporate user and saves it
     */
    public function banUser() {
        $this->disableUserInviteLimit();
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

        UserService::logCorporateInviteMovementAdd(
            sprintf("Аккаунт заблокирован пользователем-админом %s", $providerEmail),
            $this,
            $initValue
        );
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
        $this->invites_limit += $value;
        if($this->invites_limit < 0){
            $this->invites_limit = 0;
        }

        $this->save(false);
        if(null !== $admin){
            UserService::logCorporateInviteMovementAdd(
                sprintf('Количество доступных симуляций установлено в %s в админ области. '.
                ' Админ %s (емейл текущего авторизованного в админке пользователя).', $this->invites_limit, $admin->profile->email),
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
            array('ownership_type, company_name, invites_limit', 'safe'),
            array('inn, cpp, bank_account_number, bic, preference_payment_method', 'safe'),
            array('default_invitation_mail_text', 'safe'),
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
			'industry' => array(self::BELONGS_TO, 'Industry', 'industry_id')
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
        return Invite::model()->count("owner_id = {$this->user_id} and (owner_id != receiver_id or receiver_id is null) and status != ".Invite::STATUS_DELETED);
    }

    public function getNumberOfFullSimulationsForSelf() {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        return Invite::model()->count("scenario_id = {$scenario->id} and owner_id = {$this->user_id} and owner_id = receiver_id and status = ".Invite::STATUS_COMPLETED);
    }

    public function getNumberOfFullSimulationsForPeople() {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        return Invite::model()->count("scenario_id = {$scenario->id} and owner_id = {$this->user_id} and owner_id != receiver_id and status = ".Invite::STATUS_COMPLETED);
    }

    public function getStatusForSales() {

        if(false !== strpos($this->user->profile->email, '@skiliks.com')) {
            return 'Разработчик';
        }

        if($this->user->status == YumUser::STATUS_BANNED) {
            return 'Забанен';
        }

        if($this->user->status == YumUser::STATUS_INACTIVE) {
            return 'Неактивен';
        }
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        $count = Invite::model()->count("scenario_id = {$scenario->id} and owner_id = {$this->user_id} and status = ".Invite::STATUS_COMPLETED);
        if($count == 0) {
            return 'Нет пройденных Full';
        }
        $paid = Invoice::model()->count("user_id = {$this->user_id} and paid_at is not null");

        if($paid >= 1) {
            return 'Платный';
        }

        if($count >= 1 && $paid == 0){
            return 'Бесплатный';
        }

        return '';
    }
}