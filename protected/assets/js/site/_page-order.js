
$(document).ready(function() {
    $("input[type='submit']").click(function(e) {
        e.preventDefault();
        if($("#payment_card:checked").length === 1) {
            proceedRobokassaPayment();
            return false;
        }
        else {
            $("#cash-month-selected").val($("#month-selected").val());
            $("#payment-form").submit();
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
});