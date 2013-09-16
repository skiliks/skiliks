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

}