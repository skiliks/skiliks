<?php

/**
 * This is the model class for table "invites".
 *
 * The followings are the available columns in table 'invites':
 * @property integer $id
 * @property string $inviting_user_id
 * @property string $invited_user_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $message
 * @property string $signature
 * @property string $code
 * @property string $position_id
 * @property string $status
 * @property string $sent_time
 * @property string $fullname
 *
 * The followings are the available model relations:
 * @property YumUser $invitedUser
 * @property YumUser $invitingUser
 * @property Position $position
 */
class Invite extends CActiveRecord
{
    const STATUS_PENDING   = 0;
    const STATUS_ACCEPTED  = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_DECLINED  = 3;

    public static $statusText = [
        self::STATUS_PENDING   => 'Pending',
        self::STATUS_ACCEPTED  => 'Accepted',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_DECLINED  => 'Declined'
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
        return Yii::t('site', date('F', ($this->sent_time + self::EXPIRED_TIME))).date(' d, Y', ($this->sent_time + self::EXPIRED_TIME));
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return (self::$statusId[self::STATUS_PENDING] == $this->status);
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
			array('inviting_user_id, firstname, lastname, email, status', 'required'),
			array('inviting_user_id, invited_user_id, position_id, status', 'length', 'max'=>10),
			array('firstname, lastname', 'length', 'max'=>100),
			array('email, signature', 'length', 'max'=>255),
			array('code', 'length', 'max'=>50),
            array('email', 'email'),
            array('inviting_user_id, email', 'uniqueEmail', 'message' => "Вы уже отправили инвайт на {value}"),
			array('message', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, inviting_user_id, invited_user_id, firstname, lastname, email, message, signature, code, position_id, status, sent_time', 'safe', 'on'=>'search'),
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
			'invitedUser' => array(self::BELONGS_TO, 'User', 'invited_user_id'),
			'invitingUser' => array(self::BELONGS_TO, 'User', 'inviting_user_id'),
			'position' => array(self::BELONGS_TO, 'Position', 'position_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'inviting_user_id' => Yii::t('site', 'Inviting User'),
			'invited_user_id'  => Yii::t('site', 'Invited User'),
			'firstname'        => Yii::t('site', 'Firstname'),
			'lastname'         => Yii::t('site', 'Lastname'),
			'email'            => Yii::t('site', 'Email'),
			'message'          => Yii::t('site', 'Message'),
			'message text'     => Yii::t('site', 'Message text'),
			'signature'        => Yii::t('site', 'Signature'),
			'code'             => Yii::t('site', 'Code'),
			'position_id'      => Yii::t('site', 'Position'),
			'status'           => Yii::t('site', 'Status'),
			'sent_time'        => Yii::t('site', 'Sent Time'),
			'full_name'        => Yii::t('site', 'Full name'),
			'To'               => Yii::t('site', 'To'),
            'signature'        => Yii::t('site', 'Signature'),
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

		$criteria->compare('id',$this->id);
		$criteria->compare('inviting_user_id',$ownerId ?: $this->inviting_user_id,true);
		$criteria->compare('invited_user_id',$this->invited_user_id,true);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('signature',$this->signature,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('position_id',$this->position_id,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('sent_time',$this->sent_time,true);

        $criteria->mergeWith([
            'join' => 'LEFT JOIN position ON position.id = position_id'
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
                    'position_id' => [
                        'asc'  => 'position.label',
                        'desc' => 'position.label DESC'
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
        if(null !== self::model()->findByAttributes(['email' => $this->email, 'inviting_user_id' => $this->inviting_user_id])){

                $this->addError('email','Вы уже отправили инвайт на '.$this->email);

        }

    }
}