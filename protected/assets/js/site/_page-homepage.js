
$(document).ready(function() {

    // 1) проверка ОС и браузера
    window.displaySystemMismatch = function() {
        if (1 == window.isSkipBrowserCheck) {
            return;
        }
        $(".locator-system-mismatch-popup").dialog({
            closeOnEscape: true,
            dialogClass: 'background-sky',
            minHeight: 440,
            modal: true,
            resizable: false,
            width: getDialogWindowWidth(),
            draggable: false,
            open: function() {
                $(window).resize();
            }
        });
    }

    // смена ширины при изменении размеров окна браузера
    // выравнивание при изменении размеров окна браузера
    $(window).on('resize', function() {
        $('.locator-system-mismatch-popup').dialog("option", "width", getDialogWindowWidth());
        $('.locator-system-mismatch-popup').dialog("option", "position", "center");
    });

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
        console.log('displaySystemMismatch');
        window.displaySystemMismatch();
    }

    // проверка браузера
    var minSupport = {
        mozilla: 18,
        chrome: 27,
        msie: 10,
        safari: 6
    };

    if (window.httpUserAgent.indexOf('YaBrowser') != -1) {
        console.log('displaySystemMismatch');
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
        console.log('displaySystemMismatch');
        window.displaySystemMismatch();
    }
    // проверка ОС и браузера }

    // 2) Подписка
    $('#action-subscribe-form').submit(function(e) {
        hideError();
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: {'email': $('.locator-user-email-value').val()},
            success: function(response) {
                if ('undefined' !== typeof response.result || 'undefined' !== typeof response.message) {
                    if (1 === response.result) {
                        // redirect to success page
                        $('.locator-subscribe-form').html('<p class="us-success-text">Thank you! See you soon</p>');
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

    // 3) Video-popup
    $(".action-view-video").click(function() {
        //$($(".main-content .iframe-video-wrap").html()).dialog({
        $('.iframe-video').dialog({
            modal: true,
            resizable: false,
            draggable: false,
            height: 479,
            width: 850,
            dialogClass: "popup-video background-dark-blue reset-padding",
            position: {
                my: "center top",
                at: "center bottom",
                of: $('header')
            },
            open: function() {
                $('.popup-video .ui-icon-closethick').click(function() {
                    // только в IE - если видео скрыто оно не останавливается
                    // приходится перезагружать его, но без автостарта
                    $(".popup-video .iframe-video iframe").attr("src",
                        $(".iframe-video iframe").attr("src").replace('?autoplay=1',''));
                });
            }
        });

        // заставляем видео включиться
        $(".popup-video .iframe-video iframe").attr("src", $(".iframe-video iframe").attr("src") + '?autoplay=1');
    });
});

var displayError = function(msg) {
    $('.locator-errorMessage').text(msg);
    $('#action-subscribe-form').addClass('error');
    $('.locator-user-email-value').css({"border-color":"#BD2929"});
}

var hideError = function() {
    $('#action-subscribe-form').removeClass('error');
    $('.locator-errorMessage').text('');
}