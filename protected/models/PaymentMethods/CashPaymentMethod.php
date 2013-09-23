<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 12.09.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */

class CashPaymentMethod extends CFormModel {

    public $name = "cash";
    public $inn;
    public $cpp;
    public $account;
    public $bic;
    public $payment_method_view = "";

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('inn', 'required', 'message' => Yii::t('site', 'INN is required')),
            array('inn', 'checkInn'),
            array('cpp', 'required', 'message' => Yii::t('site', 'CPP is required')),
            array('cpp', 'checkCpp'),
            array('account', 'required', 'message' => Yii::t('site', 'Account number is required')),
            array('account', 'checkAccount'),
            array('bic', 'required', 'message' => Yii::t('site', 'BIC is required')),
            array('bic', 'checkBic'),
            array('inn, cpp, account, bic', 'length', 'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('inn, cpp, account, bic', 'safe', 'on'=>'search'),
        );
    }

    public function checkInn()
    {
        $prefix = +substr($this->inn, 0, 4);
        $correct = preg_match('/^\d{10}$/', $this->inn);
        $correct = $correct && ($prefix >= 100 && $prefix <= 8399 || $prefix === 9909);

        if (!$correct) {
            $this->addError('inn', Yii::t('site', 'Wrong INN'));
        }
    }

    public function checkCpp()
    {
        $prefix = +substr($this->cpp, 0, 2);
        $correct = preg_match('/^\d{9}$/', $this->cpp);
        $correct = $correct && ($prefix >= 1 && $prefix <= 83 || $prefix === 99);

        if (!$correct) {
            $this->addError('cpp', Yii::t('site', 'Wrong CPP'));
        }
    }

    public function checkAccount()
    {
        $correct = preg_match('/^\d{5}(?:810|643)\d{12}$/', $this->account);
        if (!$correct) {
            $this->addError('account', Yii::t('site', 'Wrong account number'));
        }
    }

    public function checkBic()
    {
        $prefix = +substr($this->bic, 0, 2);
        $suffix = +substr($this->bic, 6, 3);
        $correct = preg_match('/^\d{9}$/', $this->bic);
        $correct = $correct && $prefix === 4 && $suffix >= 50 && $suffix <= 999;

        if (!$correct) {
            $this->addError('bic', Yii::t('site', 'Wrong BIC'));
        }
    }

    public function sendBookerEmail($invoice = null, $user = null) {
        if($user !== null && $invoice !== null) {
            $inviteEmailTemplate = Yii::app()->params['emails']['newInvoiceToBooker'];
            $bookerEmail = Yii::app()->params['emails']['bookerEmail'];

            $body = Yii::app()->controller->renderPartial($inviteEmailTemplate, [
                'invoice' => $invoice, 'user' => $user
            ], true);

            $mail = [
                'from'        => Yum::module('registration')->registrationEmail,
                'to'          => $bookerEmail,
                'subject'     => 'New invoice #'.$invoice->id . " от " . $user->account_corporate->company_name ,
                'body'        => $body,
            ];

            try {
                $sent = YumMailer::send($mail);
            } catch (phpmailerException $e) {
                // happens at my local PC only, Slavka
                $sent = null;
            }

            return $sent;
        }
    }



}