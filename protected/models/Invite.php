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
 *
 * The followings are the available model relations:
 * @property YumUser $ownerUser
 * @property YumUser $receiverUser
 * @property Vacancy $vacancy
 */
class Invite extends CActiveRecord
{
    const STATUS_PENDING   = 0;
    const STATUS_ACCEPTED  = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_DECLINED = 3;
    const STATUS_EXPIRED = 4;

    public static $statusText = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_EXPIRED => 'Expired'
    ];

    public static $statusId = [
        'Pending'   => self::STATUS_PENDING,
        'Accepted'  => self::STATUS_ACCEPTED,
        'Completed' => self::STATUS_COMPLETED,
        'Declined'  => self::STATUS_DECLINED,
    ];

    const EXPIRED_TIME = 604800; // 7days

    /* ------------------------------------------------------------------------------------------------------------ */

    /**
     *
     */
    public function markAsSendToday()
    {
        $this->sent_time = time();
        $this->status = self::STATUS_PENDING;
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
			array('owner_id, firstname, lastname, email, status', 'required'),
			array('owner_id, receiver_id, vacancy_id, status', 'length', 'max'=>10),
			array('firstname, lastname', 'length', 'max'=>100),
			array('email, signature', 'length', 'max'=>255),
			array('code', 'length', 'max'=>50),
            array('email', 'email'),
            array('owner_id, email', 'uniqueEmail', 'message' => "Вы уже отправили инвайт на {value}"),
			array('message', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, owner_id, receiver_id, firstname, lastname, email, message, signature, code, vacancy_id, status, sent_time', 'safe', 'on'=>'search'),
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
			'receiverUser' => array(self::BELONGS_TO, 'User', 'receiver_id'),
			'ownerUser' => array(self::BELONGS_TO, 'User', 'owner_id'),
			'vacancy' => array(self::BELONGS_TO, 'Vacancy', 'vacancy_id')
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
	public function search($ownerId = null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('owner_id', $ownerId ?: $this->inviting_user_id, true);
		$criteria->compare('receiver_id', $this->receiver_id, true);
		$criteria->compare('firstname', $this->firstname, true);
		$criteria->compare('lastname', $this->lastname, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('message', $this->message, true);
		$criteria->compare('signature', $this->signature, true);
		$criteria->compare('code', $this->code, true);
		$criteria->compare('vacancy_id', $this->vacancy_id, true);
		$criteria->compare('status', $this->status, true);
		$criteria->compare('sent_time', $this->sent_time, true);

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
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 5,
                'pageVar' => 'page'
            ]
		]);
	}

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function searchByInvitedUserEmail($invitedUserEmail = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        
        $criteria->compare('id', $this->id);
        $criteria->compare('owner_id', $this->owner_id, true);
        $criteria->compare('receiver_id', $this->receiver_id, true);
        $criteria->compare('firstname', $this->firstname, true);
        $criteria->compare('lastname', $this->lastname, true);
        $criteria->compare('email', $invitedUserEmail ?: $this->email, true);
        $criteria->compare('message', $this->message, true);
        $criteria->compare('signature', $this->signature, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('vacancy_id', $this->vacancy_id, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('sent_time', $this->sent_time, true);

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
                    'status',
                    'sent_time'
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
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

    public function uniqueEmail($attribute, $params)
    {
        if(null !== self::model()->findByAttributes(['email' => $this->email, 'owner_id' => $this->owner_id])){

                $this->addError('email','Вы уже отправили инвайт на '.$this->email);

        }

    }

    public function inviteExpired() {

        $this->status = Invite::STATUS_EXPIRED;
        $this->update();
        $user = UserAccountCorporate::model()->findByAttributes(['user_id'=>$this->owner_id]);
        $user->invites_limit = $user->invites_limit + 1;
        $user->update();

    }
}