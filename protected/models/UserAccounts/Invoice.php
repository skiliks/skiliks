<?php

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $id
 * @property string $user_id
 * @property integer $tariff_id
 * @property string $amount
 * @property string $create_date
 * @property string $paid_at
 * @property string $payment_system
 * @property string $additional_data
 * @property string $comment
 * @property integer $month_selected
 *
 * The followings are the available model relations:
 * @property Tariff $tariff
 * @property YumUser $user
 */
class Invoice extends CActiveRecord
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
        return 'invoice';
    }

    public function getStatuses() {
        return [self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_EXPIRED, self::STATUS_REJECTED];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, tariff_id', 'required'),
            array('user_id', 'checkHavingInvites'),
            array('user_id, tariff_id', 'safe', 'on'=>'search'),
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
            'tariff' => array(self::BELONGS_TO, 'Tariff', 'tariff_id'),
            'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
        );
    }

    public function checkHavingInvites() {
        if($this->user->getInvitesLeft() > 0 && $this->id === null) {
            $this->addError('inn', Yii::t('site', 'You have invites left'));
        }
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


    /**
     *
     * @param int $user_id
     * @param int $tariff_id
     * @param int $amount
     *
     * @return int|bool new invoice id or false
     *
     * Method need for creating an invoice and storing it do db
     */

    public function createInvoice($user = null, Tariff $tariff = null, $months = null) {
        if($user !== null && $tariff !== null) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->user        = $user;
            $this->tariff      = $tariff;
            $this->user_id     = $user->id;
            $this->tariff_id   = $tariff->id;
            $this->amount      = $tariff->price * $months;
            $this->month_selected = $months;
            $this->save();
            $invoice_log = new LogPayments();
            $invoice_log->log($this, "Заказ тарифа ".$this->tariff->label." на ".$this->month_selected." месяц(ев), создан для ".$user->profile->email.".");
            return $this->id;
        }
        else return false;
    }


    /**
     * @return bool
     *
     * Method checks if invoice is complete
     */

    public function isComplete() {
        if($this->paid_at !== null) {
            return true;
        }
        else return false;
    }

    /**
     * @return string the name of payment method
     *
     * Method sets the name of the payment method to array
     */


    public function setPaymentMethod($ps = false) {
        if($ps) {
            $this->payment_system = $ps;
            $this->save();
            return true;
        }
        else return false;
    }

    /**
     * @return string additional data of payment method
     *
     * Method sets the additional data of payment method to an invoice
     */

    public function setAdditionalData($ad_data = false) {
        if($ad_data) {
            $this->additional_data = $ad_data;
            $this->save();
            return true;
        }
        else return false;
    }

    /**
     * Method add paid_date to Invoice and saves it
     */

    public function completeInvoice($isAdmin = null) {
        if(!$this->isComplete()) {

            //$date->add(new DateInterval('P'.$this->month_selected.'M')); N month
            // Setting tariff invites
            $account = $this->user->account_corporate;
            $tariff = Tariff::model()->findByAttributes(['id'=>$this->tariff_id]);
            if(0 === (int)$account->invites_limit){
                $account->setTariff($tariff, true);
            } else {
                if($account->getActiveTariff()->slug === $tariff->slug) {
                    $account->addPendingTariff($tariff);
                } else {
                    $account->setTariff($tariff, true);
                }
            }
            // Setting referral invites
            $account->referrals_invite_limit =
                UserReferral::model()->countUserRegisteredReferrals($this->user->id);

            $this->paid_at = date('Y-m-d H:i:s');

            $account->save(false);
            $this->save(false);

            $this->sendCompleteEmailToUser();

            $invoice_log = new LogPayments();
            if(!is_null($isAdmin)) {
                $invoice_log->log($this, "Статус инвойса изменен на оплаченный. Админ " . $isAdmin);
            }
            else {
                $invoice_log->log($this, "Статус инвойса изменен.");
            }
            return true;
        }
        else {
            $invoice_log = new LogPayments();
            $invoice_log->log($this, "Не удалось изменить статус инвойса - он оплачен ранее.");
            return false;
        }
    }

    public function disableInvoice($isAdmin) {
        $this->paid_at = null;
        $this->save();
        $invoice_log = new LogPayments();
        $invoice_log->log($this, "Статус инвойса изменен на \"Не оплаченный\". Админ " . $isAdmin);
        return true;
    }

    public function sendCompleteEmailToUser() {

        $inviteEmailTemplate = Yii::app()->params['emails']['completeInvoiceUserEmail'];

        // TODO Remake email to send referrer invites
        $body = Yii::app()->controller->renderPartial($inviteEmailTemplate, [
            'invoice' => $this, 'user' => $this->user, 'user_invites' => $this->user->getAccount()->invites_limit
        ], true);


        $mail = [
            'from'        => Yum::module('registration')->registrationEmail,
            'to'          => $this->user->profile->email,
            'subject'     => 'Оплата на skiliks.com',
            'body'        => $body,
            'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mailtopclean.png',
                    'cid'      => 'mail-top-clean',
                    'name'     => 'mailtopclean',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mailchair.png',
                    'cid'      => 'mail-chair',
                    'name'     => 'mailchair',
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
            $invoice_log = new LogPayments();
            $invoice_log->log($this, "Письмо об обновлении тарифного плана отправлено пользователю на " . $this->user->profile->email);
        } catch (phpmailerException $e) {
            // happens at my local PC only, Slavka
            $sent = null;
            $invoice_log = new LogPayments();
            $invoice_log->log($this, "Письмо об обновлении тарифного плана НЕ отправлено пользователю на " . $this->user->profile->email . ". Причина: " . $e->getMessage());
        }
        return $sent;
    }

}