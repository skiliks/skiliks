<?php

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $id
 * @property string $user_id
 * @property integer $amount
 * @property string $create_date
 * @property string $paid_at
 * @property string $payment_system
 * @property string $additional_data
 * @property string $comment
 * @property integer $month_selected
 * @property integer $simulation_selected
 *
 * The followings are the available model relations:
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

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id', 'required'),
            //array('user_id', 'checkHavingInvites'),
            array('user_id', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO, 'YumUser', 'user_id'),
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
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('payment_system',$this->payment_system,true);
        $criteria->compare('paid_at',$this->paid_at,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    /**
     * Method need for creating an invoice and storing it do db
     */
    public function createInvoice($user = null, $simulation_selected = null) {
        if($user !== null) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->user        = $user;
            $this->user_id     = $user->id;
            $this->amount      = $this->calculateAmount($simulation_selected);
            $this->simulation_selected = $simulation_selected;
            $this->save();
            $invoice_log = new LogPayments();
            $invoice_log->log($this, "Заказ");
            return $this->id;
        }
        else return false;
    }

    public function calculateAmount($simulation_selected) {

        foreach(Price::model()->findAll() as $price) {
            /* @var $price Price */
            if($price->from <= $simulation_selected && $price->to > $simulation_selected) {
                return ($price->in_RUB * $simulation_selected) * ( 100 - $this->user->account_corporate->getDiscount() ) / 100;
            }
        }

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

            $account = $this->user->account_corporate;

            $this->paid_at = date('Y-m-d H:i:s');
            $account->invites_limit += $this->simulation_selected;
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

    /**
     * Ставит в очередь писем письмо пользователю, о том что данный заказ оплачен.
     *
     * @return EmailQueue|null
     */
    public function sendCompleteEmailToUser()
    {
        $mailOptions          = new SiteEmailOptions();
        $mailOptions->from    = Yum::module('registration')->registrationEmail;
        $mailOptions->to      = $this->user->profile->email;
        $mailOptions->subject = 'Оплата на ' . Yii::app()->params['server_domain_name'];
        $mailOptions->h1      = sprintf('Приветствуем, %s!', $this->user->profile->firstname);
        $mailOptions->text1   = '
            Благодарим вас за оплату работы skiliks!<br/>, на вашем счету '
            . $this->user->getAccount()->invites_limit . ' симуляций.<br/>
        ';

        try {
            $sent = UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_FIKUS);
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