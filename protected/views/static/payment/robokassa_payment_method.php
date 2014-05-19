<form id="robokassa-payment-form" action="<?= Yii::app()->params['robokassa']['url'] ?>">
    <input type="hidden" name="MerchantLogin" value="" />
    <input type="hidden" name="InvId" value="" />
    <input type="hidden" name="OutSum" value="" />
    <input type="hidden" name="Desc" value="" />
    <input type="hidden" name="SignatureValue" value="" />
    <input type="hidden" name="Culture" value="ru" />
    <input type="hidden" name="Encoding" value="utf-8" />
</form>