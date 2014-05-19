<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 12.09.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 *
 * @property string $name
 *
 * @property string $inn, ИНН
 * @property string $cpp, КПП
 * @property string $account, банковский расчётный счёт
 * @property string $bic, БИК
 */

class CashPaymentMethod extends CFormModel {

    public $name = "cash";
    public $inn = null;
    public $cpp = null;
    public $account = null;
    public $bic = null;

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

    /**
     * Валидатор
     */
    public function checkInn()
    {
        $prefix = +substr($this->inn, 0, 4);
        $correct = preg_match('/^\d{10}$/', $this->inn);
        $correct = $correct && ($prefix >= 100 && $prefix <= 8399 || $prefix === 9909);

        if (!$correct) {
            $this->addError('inn', Yii::t('site', 'Wrong INN'));
        }
    }

    /**
     * Валидатор
     */
    public function checkCpp()
    {
        $prefix = +substr($this->cpp, 0, 2);
        $correct = preg_match('/^\d{9}$/', $this->cpp);
        $correct = $correct && ($prefix >= 1 && $prefix <= 83 || $prefix === 99);

        if (!$correct) {
            $this->addError('cpp', Yii::t('site', 'Wrong CPP'));
        }
    }

    /**
     * Валидатор
     */
    public function checkAccount()
    {
        $correct = preg_match('/^\d{5}(?:810|643)\d{12}$/', $this->account);
        if (!$correct) {
            $this->addError('account', Yii::t('site', 'Wrong account number'));
        }
    }

    /**
     * Валидатор
     */
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

            $bookerEmail = Yii::app()->params['emails']['bookerEmail'];
            $invoice_data = json_decode($invoice->additional_data);

            $mailOptions          = new SiteEmailOptions();
            $mailOptions->from    = Yum::module('registration')->registrationEmail;
            $mailOptions->to      = $bookerEmail;
            $mailOptions->subject = sprintf(
                'New invoice # %s от %s (домен %s.)' ,
                $invoice->id,
                $user->account_corporate->company_name,
                Yii::app()->params['server_domain_name']
            );
            $mailOptions->h1     = 'Поступил новый заказ.';
            $mailOptions->text1  = '
                <table cellspacing="20" border="1">
                    <tr>
                        <td>Номер заказа</td>
                        <td>' . $invoice->id . '</td>
                    </tr>
                
                    <tr>

                    <tr>
                        <td>Количество месяцев</td>
                        <td>' . $invoice->month_selected . '</td>
                    </tr>
                
                    <tr>
                        <td>Компания</td>
                        <td>' . $user->account_corporate->ownership_type .' ' . $user->account_corporate->company_name . '</td>
                    </tr>
                
                    <tr>
                        <td>Имя</td>
                        <td>' . $user->profile->lastname .' ' . $user->profile->firstname . '</td>
                    </tr>
                
                    <tr>
                        <td>E-mail</td>
                        <td>' . $user->profile->email . '</td>
                    </tr>
                
                    <tr>
                        <td><br/><br/>Данные для оплаты:</td>
                        <td></td>
                    </tr>
                
                    <tr>
                        <td>ИНН:</td>
                        <td>' . $invoice_data->inn . '</td>
                    </tr>
                
                    <tr>
                        <td>КПП:</td>
                        <td>' . $invoice_data->cpp . '</td>
                    </tr>
                
                    <tr>
                        <td>Расчетный счет:</td>
                        <td>' . $invoice_data->account . '</td>
                    </tr>
                
                    <tr>
                        <td>БИК:</td>
                        <td>' . $invoice_data->bic . '</td>
                    </tr>
                
                    <tr>
                        <td><br/><br/>Сумма, показанная для оплаты</td>
                        <td>' . $invoice->amount . ' руб.</td>
                    </tr>
                
                </table>
            ';

            $sent = UserService::addLongEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_DENEJNAIA);

            $invoice_log = new LogPayments();
            $invoice_log->log($invoice, "Письмо об обновлении тарифного плана отправлено пользователю на " . $bookerEmail);

            return $sent;
        }
    }
}