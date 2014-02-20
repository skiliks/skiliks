
$(document).ready(function() {

    // 1) проверка ОС и браузера
    window.displaySystemMismatch = function() {
        if (1 == window.isSkipBrowserCheck) {
            return;
        }
        $(".system-mismatch-popup").dialog({
            closeOnEscape: true,
            dialogClass: 'popup-before-start-sim',
            minHeight: 220,
            modal: true,
            resizable: false,
            width:881,
            draggable: false
        });
    }

    // проверка ОС
    var os_name ="Unknown OS";
    var isUnsupportedOs = true;
    var supportedOs = ['Windows', 'MacOS'];
    var unsupportedOs = ['iPhone', 'iPad', 'iPod'];

    if (navigator.appVersion.indexOf("Win")    != -1 ) { os_name = "Windows"; }
    if (navigator.appVersion.indexOf("Mac")    != -1 ) { os_name = "MacOS";   }
    if (navigator.appVersion.indexOf("iPhone") != -1 ) { os_name = "iPhoneS"; }
    if (navigator.appVersion.indexOf("iPad")   != -1 ) { os_name = "iPad";    }
    if (navigator.appVersion.indexOf("iPod")   != -1 ) { os_name = "iPod";    }
    if (navigator.appVersion.indexOf("X11")    != -1 ) { os_name = "UNIX";    }
    if (navigator.appVersion.indexOf("Linux")  != -1 ) { os_name = "Linux";   }

    $.each(supportedOs, function(i, current_os_name) {
        if(current_os_name === os_name){
            isUnsupportedOs = false;
        }
    });

    $.each(unsupportedOs, function(i, current_os_name) {
        if(current_os_name === os_name){
            isUnsupportedOs = true;
        }
    });

    if (isUnsupportedOs) {
        window.displaySystemMismatch();
    }

    // проверка браузера
    var minSupport = {
        mozilla: 18,
        chrome: 27,
        msie: 10
    };

    if (window.httpUserAgent.indexOf('YaBrowser') != -1) {
        window.displaySystemMismatch();
    }

    var isSupportedBrowser = false;
    for (var name in minSupport) {
        if (minSupport.hasOwnProperty(name)) {
            if ($.browser[name]) {
                if (parseFloat($.browser.version) >= minSupport[name]) {
                    isSupportedBrowser = true;
                }
            }
        }
    }

    if (false == isSupportedBrowser) {
        window.displaySystemMismatch();
    }
    // проверка ОС и браузера }

    // Подписка
    $('#subscribe-form').submit(function(e) {
        hideError();
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: {'email': $('#user-email-value').val()},
            success: function(response) {
                if ('undefined' !== typeof response.result || 'undefined' !== typeof response.message) {
                    if (1 === response.result) {
                        // redirect to success page
                        $('#notify-form').html('<p class="success">Thank you! See you soon</p>');
                        //window.location.href = '/static/comingSoonSuccess/en';
                        $.cookie('_lang', 'en'); //установить значение cookie
                    } else {
                        // invalid email
                        displayError(response.message);
                    }
                } else {
                    // wrong server response format
                    displayError("No proper response from server. Please try again later.");
                }
            },
            error: function() {
                // no response from server
                displayError("No response from server. Please try again later.");
            }
        });

        // prevent default behaviour
        return true;
    });
});