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
 * @property string $fullname
 * @property integer $simulation_id
 * @property integer $scenario_id
 *
 * The followings are the available model relations:
 * @property YumUser $ownerUser
 * @property YumUser $receiverUser
 * @property Vacancy $vacancy
 * @property Simulation $simulation
 * @property Scenario $scenario
 */
class Invite extends CActiveRecord
{
    const STATUS_PENDING   = 0;
    const STATUS_ACCEPTED  = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_DECLINED = 3;
    const STATUS_EXPIRED = 4;
    const STATUS_STARTED = 5;

    public static $statusText = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_COMPLETED => 'Completed', // after sim start
        self::STATUS_STARTED => 'Started', // after sim start
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_EXPIRED => 'Expired'
    ];

    public static $statusId = [
        'Pending'   => self::STATUS_PENDING,
        'Accepted'  => self::STATUS_ACCEPTED,
        'Completed' => self::STATUS_COMPLETED,
        'Declined'  => self::STATUS_DECLINED,
        'Expired'  => self::STATUS_EXPIRED,
        'Started'  => self::STATUS_STARTED,
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

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    public function isNotStarted() {
        return $this->status == self::STATUS_PENDING || $this->status == self::STATUS_ACCEPTED;
    }

    /**
     * @param YumUser $user
     * @return string
     */
    public function getFormattedScenarioSlug() {
        if (null !== $this->vacancy_id) {
            return $this->scenario->slug === Scenario::TYPE_LITE ?
                Yii::t("site","Lite version") :  '"Базовый менеджмент"';
        } else {
            return $this->scenario->slug === Scenario::TYPE_LITE ?
                Yii::t("site",'Trial "Lite version"') :  'Пробная версия "Базовый менеджмент"';
        }

        return $this->scenario->slug === self::TYPE_LITE ? Yii::t("site","Lite verion") :  '"Базовый менеджмент"';
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
        $this->sent_time = time();

        if (null === $this->status) {
            $this->status = self::STATUS_PENDING;
        }
    }

    /**
     *
     */
    public function getExpiredDate()
    {
        $time = $this->sent_time + self::EXPIRED_TIME;
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
    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED;
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
     * @return DateTime
     */
    public function getSentTime()
    {
        return new DateTime('@' . (int)$this->sent_time);
    }

    /**
     * @param YumUser $user
     * @param Scenario $scenario
     * @return Invite
     */
    public static function addFakeInvite(YumUser $user, Scenario $scenario) {
        $newInvite              = new Invite();
        $newInvite->owner_id    = $user->id;
        $newInvite->receiver_id = $user->id;
        $newInvite->firstname   = $user->profile->firstname;
        $newInvite->lastname    = $user->profile->lastname;
        $newInvite->scenario_id = $scenario->id;
        $newInvite->status      = Invite::STATUS_ACCEPTED;
        $newInvite->sent_time   = time(); // @fix DB!
        $newInvite->save(true, [
            'owner_id', 'receiver_id', 'firstname', 'lastname', 'scenario_id', 'status'
        ]);

        $newInvite->email = Yii::app()->user->data()->profile->email;
        $newInvite->save(false);

        return $newInvite;
    }

    /* ------------------------------------------------------------------------------------------------------------ */

    public function uniqueEmail($attribute, $params)
    {
        if ($this->getIsNewRecord() && null !== self::model()->findByAttributes([
            'email'    => $this->email,
            'owner_id' => $this->owner_id,
            'status'   => [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_EXPIRED]
        ])) {
            $this->addError('email','Приглашение уже отправлено');
        }
    }

    /**
     *
     */
    public function inviteExpired()
    {
        $this->status = Invite::STATUS_EXPIRED;
        $this->update();

        $user = UserAccountCorporate::model()->findByAttributes(['user_id' => $this->owner_id]);
        $user->invites_limit = $user->invites_limit++;
        $user->update();
    }

    /**
     * @return null|string
     */
    public function getAcceptActionTag()
    {
        if (in_array($this->status, [self::STATUS_PENDING])) {
            return sprintf(
                '<a class=\'blue-btn\' href=\'/dashboard/accept-invite/%s\'>%s</a>',
                $this->id,
                Yii::t('site', 'Принять')
            );
            return ;
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
			array('message', 'safe'),
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
            $this->ownerUser->account_corporate->corporate_email == $this->email
        ) {
            $this->addError('email', Yii::t('site', 'You cannot send invite to yourself'));
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'owner_id'         => Yii::t('site', 'Owner User'),
			'receiver_id'      => Yii::t('site', 'Receiver User'),
			'firstname'        => Yii::t('site', 'Firstname'),
			'lastname'         => Yii::t('site', 'Lastname'),
			'email'            => Yii::t('site', 'Email'),
			'message'          => Yii::t('site', 'Message'),
			'message text'     => Yii::t('site', 'Message text'),
			'signature'        => Yii::t('site', 'Signature'),
			'code'             => Yii::t('site', 'Code'),
			'vacancy_id'       => Yii::t('site', 'Vacancy'),
			'status'           => Yii::t('site', 'Status'),
			'sent_time'        => Yii::t('site', 'Sent Time'),
			'full_name'        => Yii::t('site', 'Full name'),
			'To'               => Yii::t('site', 'To'),
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
		$criteria->compare('email', $this->email);
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
		$criteria->addNotInCondition('receiver_id', [$receiverId]);
		$criteria->compare('firstname', $this->firstname);
		$criteria->compare('lastname', $this->lastname);
		$criteria->compare('email', $this->email);
		$criteria->compare('message', $this->message);
		$criteria->compare('signature', $this->signature);
		$criteria->compare('code', $this->code);
		$criteria->compare('vacancy_id', $this->vacancy_id);
		$criteria->compare('status', $this->status);
        $criteria->compare('scenario_id', $this->scenario_id);
		$criteria->compare('sent_time', $this->sent_time);

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

        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $criteria=new CDbCriteria;
        
        $criteria->compare('id', $this->id);
        $criteria->compare('owner_id', $this->owner_id);
        $criteria->compare('receiver_id', $this->receiver_id);
        $criteria->compare('firstname', $this->firstname);
        $criteria->compare('lastname', $this->lastname);
        $criteria->compare('email', $invitedUserEmail ?: $this->email);
        $criteria->compare('message', $this->message);
        $criteria->compare('signature', $this->signature);
        $criteria->compare('code', $this->code);
        $criteria->compare('vacancy_id', $this->vacancy_id);
        $criteria->compare('status', $status);
        $criteria->compare('sent_time', $this->sent_time);

        // restriction!
        $criteria->addNotInCondition('scenario_id', [$liteScenario->id]);

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
        $liteScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('owner_id', $this->owner_id);
        $criteria->compare('receiver_id', $this->receiver_id);
        $criteria->compare('firstname', $this->firstname);
        $criteria->compare('lastname', $this->lastname);
        $criteria->compare('email', $invitedUserEmail ?: $this->email);
        $criteria->compare('status', Invite::STATUS_ACCEPTED);
        $criteria->addInCondition('scenario_id', [$fullScenario->id, $liteScenario->id]);

        if ($isIncludeCompleted) {
            $criteriaForFinishedSimulations = new CDbCriteria;
            $criteriaForFinishedSimulations->compare('email', $invitedUserEmail ?: $this->email);
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
}