<?php

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $id
 * @property integer $referral_id
 * @property string  $referral_email
 * @property integer $referrer_id
 * @property string  $invited_at
 * @property string  $registered_at
 * @property string  $reject_reason
 * @property string  $status
 *
 * The followings are the available model relations:
 * @property YumUser $referral
 * @property YumUser $referrer
 */
class UserReferral extends CActiveRecord
{
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Invoice the static model class
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
        return 'user_referral';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('referral_email', 'CEmailValidator', 'message' => Yii::t('site', 'Wrong email')),
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
            'referral'       => array(self::BELONGS_TO, 'YumUser', 'referral_id'),
            'referrer'       => array(self::BELONGS_TO, 'YumUser', 'referrer_id'),
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
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('tariff_id',$this->tariff_id);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('payment_system',$this->payment_system,true);
        $criteria->compare('paid_at',$this->paid_at,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function sendInviteReferralEmail() {

        $inviteEmailTemplate = Yii::app()->params['emails']['referrerInviteEmail'];

        $body = Yii::app()->controller->renderPartial($inviteEmailTemplate, [
            'link' => Yii::app()->controller->createAbsoluteUrl("/register-referral/".$this->id)
        ], true);


        $mail = [
            'from'        => Yum::module('registration')->registrationEmail,
            'to'          => $this->referral_email,
            'subject'     => 'Приглашение зарегистрироваться на skiliks.com',
            'body'        => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top.png',
                    'cid'      => 'mail-top',
                    'name'     => 'mailtop',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top-2.png',
                    'cid'      => 'mail-top-2',
                    'name'     => 'mailtop2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-1.png',
                    'cid'      => 'mail-right-1',
                    'name'     => 'mailright1',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-2.png',
                    'cid'      => 'mail-right-2',
                    'name'     => 'mailright2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-3.png',
                    'cid'      => 'mail-right-3',
                    'name'     => 'mailright3',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ],
        ];

        try {
            $sent = MailHelper::addMailToQueue($mail);
        } catch (phpmailerException $e) {
            // happens at my local PC only, Slavka
            $sent = null;
        }
        return $sent;
    }

    public function countUserReferrals($userId) {
        $criteria = new CDbCriteria();
        $criteria->compare('referrer_id', $userId);
        return UserReferral::model()->count($criteria);
    }

    public function countUserRegisteredReferrals($userId) {
        $criteria = new CDbCriteria();
        $criteria->compare('referrer_id', $userId);
        $criteria->addCondition('registered_at IS NOT NULL');
        return UserReferral::model()->count($criteria);
    }

    public function searchUserReferrals($userId) {
        $criteria = new CDbCriteria();
        $criteria->compare('referrer_id', $userId);
        $criteria->compare('referral_email', $this->referral_email);
        $criteria->compare('referral_id', $this->referrer_id);
        $criteria->compare('invited_at', $this->invited_at);
        $criteria->compare('registered_at', $this->registered_at);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder'=>'id DESC',
                'sortVar' => 'sort',
                'attributes' => [
                    'referral_email' => [
                        'asc'  => 'referral_email',
                        'desc' => 'referral_email DESC'
                    ],
                    'referrer_id' => [
                        'asc'  => 'referrer_id',
                        'desc' => 'referrer_id DESC'
                    ],
                    'invited_at' => [
                        'asc'  => 'invited_at',
                        'desc' => 'invited_at DESC'
                    ],
                    'registered_at' => [
                        'asc'  => 'registered_at',
                        'desc' => 'registered_at DESC'
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return self::STATUS_APPROVED == $this->status;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return self::STATUS_REJECTED == $this->status;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return self::STATUS_PENDING == $this->status;
    }

    /**
     * Проверяет можно ди начислять +1 симуляцию за реферала.
     * Если по рефералу уже есть отказ или одобнение возарвщвет false.
     * Если реферал находится в статусе pending - выполняет валидацию, начисление + указание даты ренгистрации
     * если нет - указывает причину отказа.
     *
     * @return bool
     */
    public function approveReferral()
    {
        // пользователь уже одобрен
        if (false == $this->isPending()) {
            return false;
        }

        // Начало валидации
        // получаем е-мейл реффера и всех его зарегестрированных рефераллов
        $criteria = new CDbCriteria();
        $criteria->compare('referrer_id', $this->referrer->id);
        $criteria->addCondition('referral_id IS NOT NULL');
        $allUserReferrals = UserReferral::model()->findAll($criteria);

        $referrerEmail = $this->referrer->profile->email;

        $referrerDomain = substr($referrerEmail, strpos($referrerEmail, "@"));
        $referralDomain = substr($this->referral_email, strpos($this->referral_email, "@"));
        // проверка на доменную зону старых рефералов пользователя

        $validationError = null;

        foreach($allUserReferrals as $oldReferral) {
            $oldReferralDomain = substr($oldReferral->referral_email, strpos($oldReferral->referral_email, "@"));
            if($oldReferralDomain == $referralDomain) {
                $validationError = "У вас уже есть реферал из компании ". substr($oldReferralDomain,1). '.';
                break;
            }
        }

        // проверка на одну домененую зону с пользователем
        if($referrerDomain == substr($this->referral_email, strpos($this->referral_email, "@"))) {
            $validationError = "Вы сами являетесь сотрудником компании ". substr($referrerDomain,1). '.';
        }

        // если нет ошибок - записываем апрув и добавляем "вечную" симмуляцию
        if(null === $validationError) {
            $this->status = self::STATUS_APPROVED;
            $this->referrer->getAccount()->addReferralInvite($this->referrer->profile->email);
            $this->registered_at = date("Y-m-d H:i:s");
            return true;
        }
        else {
            $this->reject_reason = $validationError;
            $this->status = self::STATUS_REJECTED;
            return false;
        }
    }
}