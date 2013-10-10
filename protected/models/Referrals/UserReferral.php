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

    const REJECT_SAME_EMAIL_TEXT = "Пользователь зарегистрировался по другому приглашению.";
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
        $criteria->compare('referrer_id', $this->referral_id);
        $criteria->compare('referral_email', $this->referral_email);
        $criteria->compare('referral_id', $this->referrer_id);
        $criteria->compare('invited_at', $this->invited_at);
        $criteria->compare('registered_at', $this->registered_at);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function sendInviteReferralEmail($referral_text = false) {

        $inviteEmailTemplate = Yii::app()->params['emails']['referrerInviteEmail'];
        $referral_text = str_replace("\r\n", "<br/>", $referral_text);

        if(strpos($referral_text, "ссылке") === false) {
            $referral_text = $referral_text . '<br/><br/><a href="'.Yii::app()->controller->createAbsoluteUrl("/register-referral/".$this->id).'">
                              Ссылка для регистрации реферала.</a>
                            ';
        } else {
            $referral_text = str_replace("ссылке", '<a href="'.Yii::app()->controller->createAbsoluteUrl("/register-referral/".$this->id).'">
                              ссылке</a>', $referral_text);
        }

        $body = Yii::app()->controller->renderPartial($inviteEmailTemplate, [
            'text' => $referral_text
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

    public function behaviors() {
        return array(
            'ERememberFiltersBehavior' => array(
                'class' => 'application.components.ERememberFiltersBehavior',
                'defaults'=>array(),           /* optional line */
                'defaultStickOnClear'=>false   /* optional line */
            ),
        );
    }

    public function searchUserReferrals($userId) {
        $criteria = new CDbCriteria();
        $criteria->compare('referral.referral_email', $this->referral_email,true);
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
                    'status' => [
                        'asc'  => 'status',
                        'desc' => 'status DESC'
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
                'pageVar' => 'page'
            ]
        ]);
    }

    public function searchReferrals() {
        $criteria = new CDbCriteria();
        $userToGet = Yii::app()->request->getParam('UserReferral', null);
        if($userToGet != null) {

            if(isset($userToGet['referrer_id']) && $userToGet['referrer_id'] !== null) {
                $criteria->join = ("JOIN profile ON profile.user_id = t.referrer_id");
                $criteria->addCondition('profile.email LIKE \'%'.$userToGet['referrer_id'].'%\'');
            }

            if(isset($userToGet['referral_email']) && $userToGet['referral_email'] !== null) {
                $this->referral_email = $userToGet['referral_email'];
                $criteria->join = ("JOIN profile ON profile.user_id = t.referral_id");
                $criteria->addCondition('profile.email LIKE \'%'.$userToGet['referral_email'].'%\'');
            }

            if(isset($userToGet['invited_at']) && $userToGet['invited_at'] != null) {
                $this->invited_at = $userToGet['invited_at'];
                $date = new DateTime($userToGet['invited_at']);
                $date_from = $date->format('Y-m-d');
                $date_to   = $date->add(new DateInterval('P1D'))->format('Y-m-d');
                $criteria->addCondition("t.invited_at > '$date_from' AND t.invited_at < '$date_to'");
            }

            if(isset($userToGet['registered_at']) && $userToGet['registered_at'] != null) {
                $this->registered_at = $userToGet['registered_at'];
                $date = new DateTime($userToGet['registered_at']);
                $date_from = $date->format('Y-m-d');
                $date_to   = $date->add(new DateInterval('P1D'))->format('Y-m-d');
                $criteria->addCondition("t.registered_at > '$date_from' AND t.registered_at < '$date_to'");
            }

            if(isset($userToGet['status']) && $userToGet['status'] !== null) {
                $criteria->compare('t.status', $userToGet['status']);
            }

            $criteria->compare('referral_email', $userToGet['referral_id'],true);
        }
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
                    'referral_id' => [
                        'asc'  => 'referral_id',
                        'desc' => 'referral_id DESC'
                    ],
                    'invited_at' => [
                        'asc'  => 'invited_at',
                        'desc' => 'invited_at DESC'
                    ],
                    'registered_at' => [
                        'asc'  => 'registered_at',
                        'desc' => 'registered_at DESC'
                    ],
                    'status' => [
                        'asc'  => 'status',
                        'desc' => 'status DESC'
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

        $referrerEmail = strtolower($this->referrer->profile->email);

        $referrerDomain = substr($referrerEmail, strpos($referrerEmail, "@"));
        $referralDomain = substr($this->referral_email, strpos($this->referral_email, "@"));
        // проверка на доменную зону старых рефералов пользователя

        $validationError = null;

        foreach($allUserReferrals as $oldReferral) {
            $oldReferralDomain = substr($oldReferral->referral_email, strpos($oldReferral->referral_email, "@"));
            if($oldReferralDomain == $referralDomain) {
                $validationError = 'Вам уже начислена 1 симуляция за приглашение пользователя из
                <span class="domainName ProximaNova-Bold">' . $oldReferralDomain. '</span>. <a data-selected="Тарифы и
                оплата" class="feedback-close-other ProximaNova-Bold" href="#">Свяжитесь с нами</a>,
                если вы приглашаете разных корпоративных пользователей в одной компании.';
                break;
            }
        }

        // проверка на одну домененую зону с пользователем
        if($referrerDomain == substr($this->referral_email, strpos($this->referral_email, "@"))) {
                $validationError = 'Вы сами являетесь сотрудником компании '. $referrerDomain . '.' . '
                <a data-selected="Тарифы и оплата" class="feedback-close-other ProximaNova-Bold" href="#">Свяжитесь с нами</a>,
                если вы приглашаете разных корпоративных пользователей в одной компании.';
        }

        // если нет ошибок - записываем апрув и добавляем "вечную" симмуляцию
        if(null === $validationError) {
            $this->status = self::STATUS_APPROVED;
            $this->referrer->getAccount()->addReferralInvite(strtolower($this->referrer->profile->email));
            $this->registered_at = date("Y-m-d H:i:s");
            return true;
        }
        else {
            $this->reject_reason = $validationError;
            $this->status = self::STATUS_REJECTED;
            return false;
        }
    }

    /**
     * Function set statuses to all referral invites with same email as Rejected
     */
    public function rejectAllWithSameEmail() {
        $this->model()->updateAll(
            ['status'        => self::STATUS_REJECTED,
             'reject_reason' => self::REJECT_SAME_EMAIL_TEXT
            ],
            ' referral_email = "' . $this->referral_email . '" AND id != '.$this->id
           );
    }
}