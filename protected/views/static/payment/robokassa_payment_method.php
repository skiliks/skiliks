<form id="robokassa-payment-form" action="<?= Yii::app()->params['robokassa']['url'] ?>">
    <input type="hidden" name="MrchLogin" value="" />
    <input type="hidden" name="InvId" value="" />
    <input type="hidden" name="OutSum" value="" />
    <input type="hidden" name="Desc" value="" />
    <input type="hidden" name="SignatureValue" value="" />
    <input type="hidden" name="Culture" value="ru" />
    <input type="hidden" name="Encoding" value="utf-8" />
</form>
<script>
    function proceedRobokassaPayment() {
        $.getJSON( "/payment/getRobokassaForm", {tariffType : "<?=$tariff->label ?>", monthSelected : $("#month-selected").val()})
            .done(function( json ) {
                if(json.invoice_id == null) {
                    alert("В процессе обработки возникла ошибка. Пожалуйста, свяжитесь с администрацией сайта.");
                }
                else {
                    $("input[name='MrchLogin']").val(json.login);
                    $("input[name='InvId']").val(json.invoice_id);
                    $("input[name='OutSum']").val(json.invoice_amount);
                    $("input[name='Desc']").val(json.description);
                    $("input[name='SignatureValue']").val(json.key);
                    $("#robokassa-payment-form").submit();
                    // preventing default form
                    return false;
                }
            })
            .fail(function() {
                alert("В процессе обработки возникла ошибка. Пожалуйста, свяжитесь с администрацией сайта.");
            });
    }
</script>