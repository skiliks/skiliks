<?php

/**
 * This is the model class for table "invites".
 *
 * The followings are the available columns in table 'invites':
 * @property integer $id
 * @property string $owner_id
 * @property string $receiver_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $message
 * @property string $signature
 * @property string $code
 * @property string $vacancy_id
 * @property string $status
 * @property string $sent_time
 * @property string $updated_at
 * @property string $fullname
 * @property integer $simulation_id
 * @property integer $scenario_id
 * @property integer $tutorial_scenario_id
 * @property string $tutorial_displayed_at
 * @property string $tutorial_finished_at
 * @property integer $can_be_reloaded
 * @property boolean $is_display_simulation_results
 * @property string $stacktrace
 * @property bolean $is_crashed
 * @property string $expired_at
 *
 * The followings are the available model relations:
 * @property YumUser $ownerUser
 * @property YumUser $receiverUser
 * @property Vacancy $vacancy
 * @property Simulation $simulation
 * @property Scenario $scenario
 * @property Scenario $tutorial
 */
class Invite extends CActiveRecord
{
    const STATUS_PENDING   = 0;
    const STATUS_ACCEPTED  = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_DECLINED = 3;
    const STATUS_EXPIRED = 4;
    const STATUS_IN_PROGRESS = 5;
    const STATUS_DELETED = 6;

    public static $statusText = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_COMPLETED => 'Completed', // after sim start
        self::STATUS_IN_PROGRESS => 'In Progress', // after sim start
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_EXPIRED => 'Expired',
        self::STATUS_DELETED => 'Deleted'
    ];

    public static $statusTextRus = [
        self::STATUS_PENDING => 'в ожидании',
        self::STATUS_ACCEPTED => 'принятый',
        self::STATUS_COMPLETED => 'завершенный', // after sim start
        self::STATUS_IN_PROGRESS => 'в процессе', // after sim start
        self::STATUS_DECLINED => 'отклонено',
        self::STATUS_EXPIRED => 'истекший',
        self::STATUS_DELETED => 'удален'
    ];

    public static $statusId = [
        'Pending'   => self::STATUS_PENDING,
        'Accepted'  => self::STATUS_ACCEPTED,
        'Completed' => self::STATUS_COMPLETED,
        'Declined'  => self::STATUS_DECLINED,
        'Expired'  => self::STATUS_EXPIRED,
        'InProgress'  => self::STATUS_IN_PROGRESS,
    ];

    const EXPIRED_TIME = 604800; // 7days

    /* ------------------------------------------------------------------------------------------------------------ */

    /**
     * @return string
     */
    public function getReceiverUserName()
    {
        if (null !== $this->receiverUser) {
            return $this->receiverUser->getFormattedName();
        }

        if (null !== $this->firstname || null !== $this->lastname) {
            return $this->getFullname();
        }

        return 'Ваше имя';
    }

    public function getReceiverFirstName()
    {

        return (null !== $this->receiverUser && $this->receiverUser->isActive() && $this->receiverUser->getAccountType() !== null)
               ? $this->receiverUser->profile->firstname : $this->firstname;
    }

    /**
     * Устанавливает дату до которой приглашение пожет быть принято согластно конфигу
     */
    public function setExpiredAt($days = null)
    {
        if (null === $days) {
            $days = Yii::app()->params['inviteExpired'];
        }

        $this->expired_at = date("Y-m-d H:i:s", time() + 60*60*24* $days);
    }

    /**
     * @return string
     */
    public function getVacancyLabel()
    {
        if (null !== $this->vacancy) {
            return $this->vacancy->label;
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public function getCompanyOwnershipType()
    {
        if (null !== $this->ownerUser && $this->ownerUser->isCorporate()) {
            return $this->ownerUser->getAccount()->ownership_type;
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        if (null !== $this->ownerUser && $this->ownerUser->isCorporate()) {
            return $this->ownerUser->getAccount()->company_name;
        } else {
            return null;
        }
    }

    public function isNotStarted() {
        return $this->status == self::STATUS_PENDING || $this->status == self::STATUS_ACCEPTED;
    }

    /**
     * @param YumUser $user
     * @return string
     */
    public function getFormattedScenarioSlug() {
        return $this->scenario->slug === Scenario::TYPE_LITE ? 'Демо-версия "Базовый менеджмент"' :  'Полная версия "Базовый менеджмент"';
    }

    /**
     * @param YumUser $user
     * @return bool
     */
    public function isTrialFull(YumUser $user) {
        return $user->id == $this->receiver_id
            && $user->id == $this->owner_id
            && $this->scenario->slug == Scenario::TYPE_FULL;
    }

    /**
     *
     */
    public function markAsSendToday()
    {
        $datetime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $this->sent_time  = $datetime->format("Y-m-d H:i:s");
        $this->updated_at = $datetime->format("Y-m-d H:i:s");
        if (null === $this->status) {
            $this->status = self::STATUS_PENDING;
        }
    }

    /**
     *
     */
    public function getExpiredDate()
    {
        $time = time($this->sent_time) + self::EXPIRED_TIME;
        return Yii::t('site', date('F', $time)).date(' d, Y', $time);
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return (self::STATUS_PENDING == $this->status);
    }


    /**
     * @return bool
     */
    public function isAccepted()
    {
        return $this->status == self::STATUS_ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * @todo: remove in sprint S27
     * @return bool
     */
    public function isComplete()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->status == self::STATUS_IN_PROGRESS;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return self::$statusText[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusCssClass()
    {
        $arr = [
            self::STATUS_PENDING => 'label-default',
            self::STATUS_ACCEPTED => 'label-warning',
            self::STATUS_COMPLETED => 'label-success',
            self::STATUS_DECLINED => 'label-danger',
            self::STATUS_EXPIRED => 'label-danger',
            self::STATUS_IN_PROGRESS => 'label-info',
        ];

        if (isset($arr[$this->status])) {
            return $arr[$this->status];
        }

        return '';
    }

    /**
     * @return DateTime
     */
    public function getSentTime()
    {
        return $this->sent_time;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedTime()
    {
        return new DateTime($this->updated_at, new DateTimeZone('Europe/Moscow'));
    }

    /**
     * @param YumUser $user
     * @param Scenario $scenario
     * @return Invite
     */
    public static function addFakeInvite(YumUser $user, Scenario $scenario) {
        $invite              = new Invite();
        $invite->owner_id    = $user->id;
        $invite->receiver_id = $user->id;
        $invite->firstname   = $user->profile->firstname;
        $invite->lastname    = $user->profile->lastname;
        $invite->scenario_id = $scenario->id;
        $invite->status      = Invite::STATUS_ACCEPTED;
        $invite->sent_time   = date("Y-m-d H:i:s");
        $invite->setExpiredAt();
        if($scenario->isFull()) {
            $invite->tutorial_scenario_id = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_TUTORIAL])->id;
            $invite->is_display_simulation_results = 1;
        }
        $invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $invite->email = strtolower($user->profile->email);
        $invite->save(false);

        InviteService::logAboutInviteStatus($invite, 'Добваление инвайта для прохождения симуляции '.$scenario->slug.' сам себе ');

        return $invite;
    }

    public function isAllowedToSeeResults(YumUser $user)
    {
        // просто проверка
        if (null === $user) {
            return false;
        }

        // просто проверка
        if (false === $this->isComplete()) {
            return false;
        }

        // создатель всегда может
        if ($this->owner_id == $user->id) {
            return true;
        }

        // истанная проверка - is_display_simulation_results, это главный переметр
        // при решении отображать результаты симуляции или нет
        if (1 === (int)$this->is_display_simulation_results) {
            return true;
        }

        return false;
    }

    /* ------------------------------------------------------------------------------------------------------------ */

    public function uniqueEmail($attribute, $params)
    {
        if ($this->getIsNewRecord() && null !== self::model()->findByAttributes([
            'email'    => strtolower($this->email),
            'owner_id' => $this->owner_id,
            'status'   => [self::STATUS_PENDING, self::STATUS_ACCEPTED]
        ])) {
            $this->addError('email','Приглашение уже отправлено');
        }
    }

    /**
     *
     */
    public function inviteExpired()
    {
        if (Invite::STATUS_IN_PROGRESS == $this->status && null !== $this->simulation) {
            $lastLog = LogServerRequest::model()->find([
                'order' => 'real_time DESC',
                'condition' => 'sim_id = '.$this->simulation->id
            ]);

            $last_request_time = strtotime($lastLog->real_time);
            $expired_time = strtotime('-1 hour');
            // проверяем что последний лог пришел посже чем час назад
            if ($lastLog !== null && $last_request_time > $expired_time) {
                // если последний лог пришел посже чем час назад - то инвайт не делаем просроченным
                return false;
            }
        }
        $invite_status = $this->status;
        $this->status = Invite::STATUS_EXPIRED;
        $this->save(false);

        InviteService::logAboutInviteStatus($this, 'Сменился статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($this->status));
        $account = UserAccountCorporate::model()->findByAttributes(['user_id' => $this->owner_id]);

        if (null === $account) {
            return false;
        }

        $account->invites_limit++;
        $account->save();

        return true;
    }

    /**
     * @return null|string
     */
    public function getAcceptActionTag()
    {
        if (in_array($this->status, [self::STATUS_PENDING])) {
            return sprintf(
                '<a class=\'blue-btn accept-invite\' data-accept-link=\'' .sprintf('/simulation/promo/%s/%s',
                    $this->scenario->slug,
                    $this->id) . '\' href=\'/dashboard/accept-invite/%s\'>%s</a>',
                $this->id,
                Yii::t('site', 'Принять')
            );
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getDeclineActionTag()
    {
        if (in_array($this->status, [self::STATUS_PENDING])) {
            return sprintf(
                '<a class=\'decline-link\' title=\'%1$s\' href=\'/dashboard/decline-invite/%1$s\'>%2$s</a>',
                $this->id,
                Yii::t('site', 'Отклонить')
            );
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getSoftRemoveActionTag()
    {
        if (in_array($this->status, [self::STATUS_PENDING])) {
            return sprintf(
                '<a href=\'dashboard/invite/remove/%s/soft\'>%s</a>',
                $this->id,
                Yii::t('site', 'удалить')
            );
        }

        return null;
    }

    public function getInviteLink()
    {
        return Yii::app()->createAbsoluteUrl($this->receiver_id ? '/dashboard' : '/registration/by-link/' . $this->code);
    }

    /* ------------------------------------------------------------------------------------------------------------ */

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Invite the static model class
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
		return 'invites';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('owner_id, status, scenario_id', 'required'),
            array('firstname', 'required', 'message' => Yii::t('site', 'First name is required')),
            array('lastname', 'required', 'message' => Yii::t('site', 'Last name is required')),
            array('email', 'required', 'message' => Yii::t('site', 'Email is required')),
            array('email', 'checkSendYourself'),
            array('vacancy_id', 'required', 'message' => Yii::t('site', 'Vacancy is required')),
			array('owner_id, receiver_id, vacancy_id, status', 'length', 'max'=>10),
			array('firstname, lastname', 'length', 'max'=>100),
			array('email, signature', 'length', 'max'=>255),
			array('code', 'length', 'max'=>50),
            array('email', 'email', 'message' => Yii::t('site', 'Wrong email')),
            array('owner_id, email', 'uniqueEmail', 'message' => "Приглашение уже отправлено"),
			array('message, fullname, signature, status, sent_time, updated_at', 'safe'),
			array('simulation_id, scenario_id, tutorial_scenario_id, tutorial_displayed_at', 'safe'),
			array('tutorial_finished_at, can_be_reloaded, is_display_simulation_results', 'safe'),
			array('stacktrace, is_crashed, expired_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, owner_id, receiver_id, firstname, lastname, email, message, signature, code, vacancy_id, status, sent_time', 'safe', 'on'=>'search'),
		);
	}

    public function checkSendYourself()
    {
        if ($this->ownerUser &&
            $this->ownerUser->account_corporate &&
            $this->email &&
            strtolower($this->ownerUser->profile->email) == strtolower($this->email)
        ) {
            $this->addError('email', Yii::t('site', 'Действие невозможно'));
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'receiverUser' => array(self::BELONGS_TO, 'YumUser', 'receiver_id'),
			'ownerUser'    => array(self::BELONGS_TO, 'YumUser', 'owner_id'),
			'vacancy'      => array(self::BELONGS_TO, 'Vacancy', 'vacancy_id'),
			'simulation'   => array(self::BELONGS_TO, 'Simulation', 'simulation_id'),
			'scenario'     => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
			'tutorial'     => array(self::BELONGS_TO, 'Scenario', 'tutorial_scenario_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                            => 'ID',
			'owner_id'                      => Yii::t('site', 'Owner User'),
			'receiver_id'                   => Yii::t('site', 'Receiver User'),
			'firstname'                     => Yii::t('site', 'Firstname'),
			'lastname'                      => Yii::t('site', 'Lastname'),
			'email'                         => Yii::t('site', 'Email'),
			'message'                       => Yii::t('site', 'Message'),
			'message text'                  => Yii::t('site', 'Message text'),
            'signature'                     => Yii::t('site', 'Signature'),
            'is_display_simulation_results' => Yii::t('site', 'Hide test results'),
			'code'                          => Yii::t('site', 'Code'),
			'vacancy_id'                    => Yii::t('site', 'Vacancy'),
			'status'                        => Yii::t('site', 'Status'),
			'sent_time'                     => Yii::t('site', 'Sent Time'),
			'full_name'                     => Yii::t('site', 'Full name'),
			'To'                            => Yii::t('site', 'To'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
    public function search($ownerId = null, $receiverId = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('owner_id', $ownerId ?: $this->owner_id);
        $criteria->compare('receiver_id', $receiverId ?: $this->receiver_id);
        $criteria->compare('firstname', $this->firstname);
        $criteria->compare('lastname', $this->lastname);
        $criteria->compare('email', strtolower($this->email));
        $criteria->compare('message', $this->message);
        $criteria->compare('signature', $this->signature);
        $criteria->compare('code', $this->code);
        $criteria->compare('vacancy_id', $this->vacancy_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('scenario_id', $this->scenario_id);
        $criteria->compare('sent_time', $this->sent_time);
        $criteria->mergeWith([
            'join' => 'LEFT JOIN vacancy ON vacancy.id = vacancy_id'
        ]);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'sent_time',
                'sortVar' => 'sort',
                'attributes' => [
                    'name' => [
                        'asc'  => 'CONCAT(firstname, lastname) ASC',
                        'desc' => 'CONCAT(firstname, lastname) DESC'
                    ],
                    'vacancy_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'owner_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }


    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */

    public function searchCorporateInvites($ownerId = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $full = Scenario::model()->findByAttributes(['slug'=>'full']);
        // we need only full simulation and tutorial => 2,3
        $criteria->addInCondition('scenario_id', [$full->id]);
        $criteria->addNotInCondition('status', [Invite::STATUS_DELETED]);
        $criteria->compare('id', $this->id);
        $criteria->addCondition(' (t.receiver_id != \''.$ownerId.'\' or t.receiver_id IS NULL ) or (t.receiver_id = \''.$ownerId.'\'
                                  AND t.status IN ('.self::STATUS_COMPLETED.')) ');
        $criteria->compare('owner_id', $ownerId ?: $this->owner_id);
        $criteria->compare('firstname', $this->firstname);
        $criteria->compare('lastname', $this->lastname);
        $criteria->compare('email', strtolower($this->email));
        $criteria->compare('message', $this->message);
        $criteria->compare('signature', $this->signature);
        $criteria->compare('code', $this->code);
        $criteria->compare('vacancy_id', $this->vacancy_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('scenario_id', $this->scenario_id);
        $criteria->compare('sent_time', $this->sent_time);
        $criteria->mergeWith([
            'join' => 'LEFT JOIN vacancy ON vacancy.id = vacancy_id'
        ]);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'sent_time',
                'sortVar' => 'sort',
                'attributes' => [
                    'name' => [
                        'asc'  => 'CONCAT(firstname, lastname) ASC',
                        'desc' => 'CONCAT(firstname, lastname) DESC'
                    ],
                    'vacancy_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'owner_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }


    /**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function searchNotToMe($ownerId = null, $receiverId = null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('owner_id', $ownerId ?: $this->owner_id);

            $criteria2 = new CDbCriteria;
		    $criteria2->condition = 'receiver_id IS NULL';
            $criteria2->addNotInCondition('receiver_id', [$receiverId], 'OR');

		$criteria->compare('firstname', $this->firstname);
		$criteria->compare('lastname', $this->lastname);
		$criteria->compare('email', strtolower($this->email));
		$criteria->compare('message', $this->message);
		$criteria->compare('signature', $this->signature);
		$criteria->compare('code', $this->code);
		$criteria->compare('vacancy_id', $this->vacancy_id);
		$criteria->compare('status', $this->status);
        $criteria->compare('scenario_id', $this->scenario_id);
		$criteria->compare('sent_time', $this->sent_time);
        $criteria->with = ['vacancy'];
        $criteria->mergeWith($criteria2);

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'sent_time',
                'sortVar' => 'sort',
                'attributes' => [
                    'name' => [
                        'asc'  => 'CONCAT(firstname, lastname) ASC',
                        'desc' => 'CONCAT(firstname, lastname) DESC'
                    ],
                    'vacancy_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'owner_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
		]);
	}

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function searchByInvitedUserEmail($invitedUserEmail = null, $status = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        if (null === $status) {
            $status = self::$statusId;
        }

        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $criteria=new CDbCriteria;
        
        $criteria->compare('id', $this->id);
        $criteria->compare('owner_id', $this->owner_id);
        $criteria->compare('receiver_id', $this->receiver_id);
        $criteria->compare('firstname', $this->firstname);
        $criteria->compare('lastname', $this->lastname);
        $criteria->compare('email', strtolower($invitedUserEmail) ?: strtolower($this->email));
        $criteria->compare('message', $this->message);
        $criteria->compare('signature', $this->signature);
        $criteria->compare('code', $this->code);
        $criteria->compare('vacancy_id', $this->vacancy_id);
        $criteria->compare('status', $status);
        $criteria->compare('sent_time', $this->sent_time);

        // restriction!
        $criteria->addInCondition('scenario_id', [$fullScenario->id]);

        $criteria->mergeWith([
            'join' => 'LEFT JOIN vacancy ON vacancy.id = vacancy_id LEFT JOIN user_account_corporate ON user_account_corporate.user_id = vacancy.user_id'
        ]);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'sent_time',
                'sortVar' => 'sort',
                'attributes' => [
                    'name' => [
                        'asc'  => 'CONCAT(firstname, lastname) ASC',
                        'desc' => 'CONCAT(firstname, lastname) DESC'
                    ],
                    'vacancy_id' => [
                        'asc'  => 'vacancy.label',
                        'desc' => 'vacancy.label DESC'
                    ],
                    'company' => [
                        'asc'  => 'user_account_corporate.company_name',
                        'desc' => 'user_account_corporate.company_name DESC'
                    ],
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function searchByInvitedUserEmailForOwner($invitedUserEmail = null, $isIncludeCompleted = true)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $fullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('owner_id', $this->owner_id);
        $criteria->compare('receiver_id', $this->receiver_id);
        $criteria->compare('firstname', $this->firstname);
        $criteria->compare('lastname', $this->lastname);
        $criteria->compare('email', strtolower($invitedUserEmail) ?: strtolower($this->email));
        $criteria->compare('status', Invite::STATUS_ACCEPTED);

        if ($isIncludeCompleted) {
            $criteriaForFinishedSimulations = new CDbCriteria;
            $criteriaForFinishedSimulations->compare('email', strtolower($invitedUserEmail) ?: strtolower($this->email));
            $criteriaForFinishedSimulations->compare('status', Invite::STATUS_COMPLETED);
            $criteriaForFinishedSimulations->compare('scenario_id', $fullScenario->id);

            $criteria->mergeWith($criteriaForFinishedSimulations, false);
        }

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => 'sent_time',
                'sortVar' => 'sort',
                'attributes' => [
                    'name' => [
                        'asc'  => 'CONCAT(firstname, lastname) ASC',
                        'desc' => 'CONCAT(firstname, lastname) DESC'
                    ],
                    'vacancy_id' => '',
                    'company' => [
                        'asc'  => 'user_account_corporate.company_name',
                        'desc' => 'user_account_corporate.company_name DESC'
                    ],
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }

    /**
     * @param string $code
     * @return Invite|null
     */
    public function findByCode($code)
    {
        return $this->findByAttributes([
            'code' => $code
        ]);
    }

    public function canUserSimulationStart() {

        if($this->receiver_id === null){
            return true;
        }
        if((int)$this->receiver_id === (int)$this->receiverUser->id){
            return true;
        }else{
            return false;
        }

    }

    /**
     * @return array|mixed|null
     */
    public function getOverall() {
        $assessment = AssessmentOverall::model()->findByAttributes([
            'sim_id'=>$this->simulation_id,
            'assessment_category_code' => AssessmentCategory::OVERALL
        ]);
        if(null === $assessment){
            return null;
        }else{
            return $assessment->value;
        }
    }

    /**
     * @return array|mixed|null
     */
    public function getPercentile() {
        $assessment = AssessmentOverall::model()->findByAttributes([
            'sim_id' => $this->simulation_id,
            'assessment_category_code' => AssessmentCategory::PERCENTILE
        ]);
        if(null === $assessment){
            return null;
        }else{
            return $assessment->value;
        }
    }

    public function resetInvite() {
        $invite_status = $this->status;
        $this->status = Invite::STATUS_ACCEPTED;
        $this->simulation->end = gmdate("Y-m-d H:i:s", time());
        $this->simulation->update();
        $this->simulation_id = null;
        $result = $this->save(false);

        InviteService::logAboutInviteStatus($this, 'Сменился статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($this->status));
        return $result;
    }

    /**
     * Sets invites status to deleted and saves it
     */

    public function deleteInvite() {
        $invite_status = $this->status;
        $this->status = Invite::STATUS_DELETED;
        $result = $this->save(false);

        InviteService::logAboutInviteStatus($this, 'Сменился статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($this->status));
        return $result;
    }

    public static function getStatusNameByCode($code) {
        if(empty($code)){
            return "не задано";
        }
        return self::$statusTextRus[$code];
    }

}