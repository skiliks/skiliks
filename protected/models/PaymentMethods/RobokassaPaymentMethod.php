<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 12.09.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */

class RobokassaPaymentMethod extends CFormModel {

    public $name = "robokassa";

    public  $MrchLogin;
    public  $Desc;
    private $sMerchantPass1;
    private $sMerchantPass2;

    public $payment_method_view = "//static/payment/robokassa_payment_method";

    public function __construct() {
        $this->MrchLogin      = Yii::app()->params['robokassa']['MrchLogin'];
        $this->Desc           = Yii::app()->params['robokassa']['Desc'];
        $this->sMerchantPass1 = Yii::app()->params['robokassa']['sMerchantPass1'];
        $this->sMerchantPass2 = Yii::app()->params['robokassa']['sMerchantPass2'];
    }

    public function get_form_key(Invoice $invoice) {
        return md5($this->MrchLogin.':'.$invoice->amount.':'.$invoice->id.':'.$this->sMerchantPass1);
    }

    public function get_result_key(Invoice $invoice, $backAmount) {
        return strtoupper(md5($backAmount.':'.$invoice->id.':'.$this->sMerchantPass2));
    }

    private function getDescription($tariff) {
        if($this->Desc === null) {
            return "Продление тарифного" . $tariff->slug . "(" . $tariff->simulations_amount . ")";
        }
        else return $this->Desc;
    }

    public function setDescription($tariff, $user, $invoice) {
        $this->Desc = "Продление тарифного плана ".$tariff->slug." для компании ".$user->account_corporate->ownership_type .
                       " " .$user->account_corporate->company_name." на ".$invoice->month_selected ." " .
                       StringTools::lastLetter($invoice->month_selected, ["месяц", "месяца", "месяцев"]) .".";
    }

    public function generateJsonBackData(Invoice $invoice, $tariff) {
        $return_data = ["login" => $this->MrchLogin, "invoice_id" => $invoice->id, "invoice_amount" => $invoice->amount,
                        "description" => $this->getDescription($tariff), "key" => $this->get_form_key($invoice)
                       ];
        return $return_data;
    }

}