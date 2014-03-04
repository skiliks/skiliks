
$(document).ready(function() {

    function validationSimulationSelected() {
        var error_simulation_selected = $('.error_simulation_selected');
        var simulation_selected = $('#simulation_selected').val();
        var payment_data = $.parseJSON($("#payment_data").text());
        var validation = new RegExp('^(0)$|^([1-9][0-9]*)$');//payment_data['minSimulationSelected']
        if(validation.test(simulation_selected) && parseInt(simulation_selected) >= parseInt(payment_data['minSimulationSelected'])) {
            error_simulation_selected.css('display', 'none');
            var price = getPriceInfo(simulation_selected, payment_data['prices']);
            $('.current-price-name').html(price['name']);
            $('.locator-order-tariff-label').html(price['name']);
            $('.current-price').html(price['in_RUB']);
            $('.order-price-total').html(parseInt((simulation_selected * price['in_RUB'])*(100-parseFloat($('.current-discount').text()))/100));
        } else {
            error_simulation_selected.css('display', '');
            error_simulation_selected.html("Вводить можно только цифры<br>Минимальное значение "+payment_data['minSimulationSelected']);

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

    $('#simulation_selected').bind('focusout', function(e) {
        validationSimulationSelected();
    });

    validationSimulationSelected();
});