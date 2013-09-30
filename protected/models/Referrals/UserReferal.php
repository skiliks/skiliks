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
 *
 * The followings are the available model relations:
 * @property YumUser $referral
 * @property YumUser $referrer
 */
class UserReferal extends CActiveRecord
{

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
            'link' => Yii::app()->controller->createAbsoluteUrl("/register-referal/".$this->id)
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

    public function countUserReferrers($userId) {
        $criteria = new CDbCriteria();
        $criteria->compare('referrer_id', $userId);
        return UserReferal::model()->count($criteria);
    }

    public function countUserRegisteredReferrers($userId) {
        $criteria = new CDbCriteria();
        $criteria->compare('referrer_id', $userId);
        $criteria->addCondition('registered_at IS NOT NULL');
        return UserReferal::model()->count($criteria);
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

}