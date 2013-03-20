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
    const STATUS_DECLINED = 3;

    protected static $statusText = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_DECLINED => 'Declined'
    ];

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
			'id' => 'ID',
			'inviting_user_id' => 'Inviting User',
			'invited_user_id' => 'Invited User',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'email' => 'Email',
			'message' => 'Message',
			'signature' => 'Signature',
			'code' => 'Code',
			'position_id' => 'Position',
			'status' => 'Status',
			'sent_time' => 'Sent Time',
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
                'pageSize' => 5,
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
}