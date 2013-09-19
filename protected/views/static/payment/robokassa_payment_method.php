<form id="robokassa-payment-form" action="http://test.robokassa.ru/Index.aspx">
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
        $.getJSON( "/payment/getRobokassaForm", {tariffType : "<?=$tariff->label ?>"})
            .done(function( json ) {
                $("input[name='MrchLogin']").val(json.login);
                $("input[name='InvId']").val(json.invoice_id);
                $("input[name='OutSum']").val(json.invoice_amount);
                $("input[name='Desc']").val(json.description);
                $("input[name='SignatureValue']").val(json.key);
                $("#robokassa-payment-form").submit();
                // preventing default form
                return false;
            })
            .fail(function() {
                alert("Not arbeit");
            });
    }
</script>