<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 12.09.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 *
 * @property string $name
 * @property string $MerchantLogin
 * @property string $Description
 * @property string $sMerchantPass1
 * @property string $sMerchantPass2
 * @property string $payment_method_view
 */

class RobokassaPaymentMethod extends CFormModel {

    public $name                = "robokassa";
    public $MerchantLogin       = null;
    public $Description         = null;
    public $sMerchantPass1      = null;
    public $sMerchantPass2      = null;
    public $payment_method_view = "//static/payment/robokassa_payment_method";

    /**
     *
     */
    public function __construct() {
        $this->MerchantLogin  = Yii::app()->params['robokassa']['MerchantLogin'];
        $this->Description    = Yii::app()->params['robokassa']['Description'];
        $this->sMerchantPass1 = Yii::app()->params['robokassa']['sMerchantPass1'];
        $this->sMerchantPass2 = Yii::app()->params['robokassa']['sMerchantPass2'];
    }

    /**
     * @param Invoice $invoice
     * @return string
     */
    public function get_form_key(Invoice $invoice) {
        return md5($this->MerchantLogin.':'.$invoice->amount.':'.$invoice->id.':'.$this->sMerchantPass1);
    }

    /**
     * @param Invoice $invoice
     * @param $backAmount
     * @return string
     */
    public function get_result_key(Invoice $invoice, $backAmount) {
        return strtoupper(md5($backAmount.':'.$invoice->id.':'.$this->sMerchantPass2));
    }

    /**
     * @param $tariff
     * @return string
     */
    private function getDescription($tariff) {
        if($this->Description === null) {
            return "Продление тарифного" . $tariff->slug . "(" . $tariff->simulations_amount . ")";
        }
        else return $this->Description;
    }

    /**
     * @param $tariff
     * @param $user
     * @param $invoice
     */
    public function setDescription($tariff, $user, $invoice) {
        $this->Description = "Продление тарифного плана ".$tariff->slug." для компании ".$user->account_corporate->ownership_type .
           " " .$user->account_corporate->company_name." на ".$invoice->month_selected ." " .
           StringTools::lastLetter($invoice->month_selected, ["месяц", "месяца", "месяцев"]) .".";
    }

    /**
     * @param Invoice $invoice
     * @param $tariff
     * @return array
     */
    public function generateJsonBackData(Invoice $invoice, $tariff) {
        $return_data = [
            "login"          => $this->MerchantLogin,
            "invoice_id"     => $invoice->id,
            "invoice_amount" => $invoice->amount,
            "description"    => $this->getDescription($tariff),
            "key"            => $this->get_form_key($invoice)
        ];

        return $return_data;
    }

}