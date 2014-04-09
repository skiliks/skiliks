
$(document).ready(function() {

    $("input[type='submit']").click(function(e) {
        e.preventDefault();
        if(validationSimulationSelected()) {
            if($("#payment_card:checked").length === 1) {
                proceedRobokassaPayment();
                return false;
            }
            else {
                $("#simulation-selected").val($("#simulation_selected").val());
                $("#payment-form").submit();
            }
        }
    });


    // ordering form truncating spaces
    var phone = document.getElementById('CashPaymentMethod_account'),
        cleanPhoneNumber;

    cleanPhoneNumber= function(e) {
        e.preventDefault();
        var pastedText = '';
        if (window.clipboardData && window.clipboardData.getData) { // IE
            pastedText = window.clipboardData.getData('Text');
        } else if (e.clipboardData && e.clipboardData.getData) {
            pastedText = e.clipboardData.getData('text/plain');
        }
        this.value = pastedText.replace(/\D/g, '');
    };

    phone.onpaste = cleanPhoneNumber;

    window.paymentSubmit = function paymentSubmit(form, data, hasError) {
        if (!hasError) {
            $("#cash-month-selected").val($("#month-selected").val());
            $.post("/payment/invoiceSuccess", form.serialize(), function (res) {
                window.location.href = "/payment/invoiceSuccess";
            });
        }
        return false;
    };

    function proceedRobokassaPayment() {
        validationSimulationSelected();
        $.getJSON( "/payment/getRobokassaForm", {'simulation-selected' : $("#simulation-selected").val()})
            .done(function( json ) {
                if(json.invoice_id == null) {
                    alert("В процессе обработки возникла ошибка. Пожалуйста, свяжитесь с администрацией сайта.");
                }
                else {
                    $("input[name='MerchantLogin']").val(json.login);
                    $("input[name='InvId']").val(json.invoice_id);
                    $("input[name='OutSum']").val(json.invoice_amount);
                    console.log(json.invoice_amount);
                    $("input[name='Description']").val(json.description);
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

    function validationSimulationSelected()
    {
        $('#simulation-selected').val($('#simulation_selected').val());
        var error_simulation_selected = $('.error_simulation_selected');
        var simulation_selected = $('#simulation_selected').val();
        var payment_data = $.parseJSON($("#payment_data").text());
        var validation = new RegExp('^(0)$|^([1-9][0-9]*)$');

        if (validation.test(simulation_selected)
            && parseInt(simulation_selected) >= parseInt(payment_data['minSimulationSelected'])) {

            error_simulation_selected.parent().removeClass('error');

            var price = getPriceInfo(simulation_selected, payment_data['prices']);

            $('.current-price-name').html(price['name']);
            $('.locator-order-tariff-label').html(price['name']);
            $('.order-price-1-sim').html(parseInt(price['in_RUB']).format());

            var total = (simulation_selected * price['in_RUB']);
            var discount = ( 100 - parseFloat($('.current-discount').text()))/100;

            $('.order-price-total').html(parseInt(total).format());
            $('.order-price-total-with-discount').html(parseInt(total * discount).format());
            return true;
        } else {
            error_simulation_selected.parent().addClass('error');
            error_simulation_selected.html('Вводить можно только цифры. Минимальное значение "'
                + payment_data['minSimulationSelected'] + '"');
            return false;
        }
    }

    function getPriceInfo(count, prices) {

        var price_return = null;
        $.each(prices, function(i, price) {
            if(parseInt(price['from']) <= parseInt(count) && parseInt(price['to']) > parseInt(count)){
                price_return = price;
                return false;
            }
        });

        return price_return;
    }

    var simulation_selected = $('#simulation_selected');
    simulation_selected.bind('focusout', function(e) {
        validationSimulationSelected();
    });
    simulation_selected.bind('textchange', function(e) {

    });

    validationSimulationSelected();
});