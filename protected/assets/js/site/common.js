$(document).ready(function () {
    // 1) отображает подстказку справа от элемента "action-display-popover" на странице
    $(".action-display-popover").click(function() {

        hideAllPopovers();

        // закрыть уже открытые status-tooltips
        $(".action-display-popover").removeClass("active");

        $(this).addClass("active");

        var popover =  $(this).find('.inner-popover');
        popover.addClass("active");

        // да - надо и класс убрать и show() вызвать
        popover.removeClass("hide");
        popover.show();

        // значение "-8" найдено экспериментально
        // если отнять 8, то подсказка выравнивается по высоте
        popover.css("margin-top", - popover.height()/2 - 8);

        if($(window).width() < 1140) {
            popover.css("margin-left", -22);
        } else {
            popover.css("margin-left", 0);
        }

        var top = ((popover.height() - 16)/2);
        popover.find('.locator-popover-triangle-left').css("top", top  + 'px');
    });

    $(document).click(function(e) {
        if(!$(e.target).is('.action-display-popover')) {
            hideAllPopovers();
        }
    });
    // action-display-popover }

    // 2) Стализация выпадающих списков
    // @link http://www.bulgaria-web-developers.com/projects/javascript/selectbox/
    $("select").selectbox();

    // 3) feedback
    $('.action-feedback').on('click', function (e) {
        var selected = $(this).attr('data-selected');
        $('.locator-feedback-dialog').dialog({
            // autoOpen : true,
            width: getDialogWindowWidth_2of3(),
            height: 400,
            dialogClass: 'popup-form background-image-two-lamps hide-ui-dialog-content' ,
            modal: true,
            resizable: false,
            draggable: false
        });
    });

    // 4) flash messages
    $('.locator-flash').dialog({
        autoOpen: false,
        closeOnEscape: true,
        dialogClass: "background-sky popup-form pull-content-center",
        minHeight: 50,
        modal: true,
        resizable: false,
        width: getDialogWindowWidth_2of3(),
        position: {
            my: 'center top',
            at: 'center bottom',
            of: $('header.main-content')
        },
        open: function() {
            $('.locator-flash').removeClass('hide');
        }
    });

    // autoOpen переписан нами, и теперь центритует dialog по высоте
    // а флеш-сообщения надо по высоте тавнять с низом header
    $('.locator-flash').dialog('open');

    // 5) sign-in
    $('.action-sign-in').click(function(event){
        event.preventDefault(event);

        var dialog = $(".locator-box-sign-in");

        dialog.dialog({
            /*autoOpen: false,*/
            closeOnEscape: true,
            dialogClass: 'popup-auth background-dark-blue',
            minHeight: 220,
            modal: true,
            position: {
                use: true,
                my: "right top",
                at: "right bottom",
                of: $('.locator-button-sign-in')
            },
            resizable: false,
            width: getDialogAuthWindowWidth(),
            open: function() {
                // из-за фокуса на данный input не видно текста placeholder
                $('#YumUserLogin_username').blur();
            }
        });

         dialog.dialog("open");

        return false;
    });

    // 6) Восстановление пароля
    $('.action-password-recovery').click(function(event){
        $(".locator-box-sign-in").dialog("close");

        var dialog = $(".locator-password-recovery");

        dialog.dialog({
            /*autoOpen: false,*/
            closeOnEscape: true,
            dialogClass: 'popup-auth background-dark-blue',
            minHeight: 220,
            modal: true,
            position: {
                use: true,
                my: "right top",
                at: "right bottom",
                of: $('.locator-button-sign-in')
            },
            resizable: false,
            width: getDialogAuthWindowWidth(),
            open: function() {
                // из-за фокуса на данный input не видно текста placeholder
                $('#YumPasswordRecoveryForm_email').blur();
            }
        });

        dialog.dialog("open");
    });

    // 7)
    $('.locator-password-recovery-success').dialog({
        /*autoOpen: false,*/
        closeOnEscape: true,
        dialogClass: 'popup-auth background-dark-blue',
        minHeight: 220,
        modal: true,
        position: {
            use: true,
            my: "right top",
            at: "right bottom",
            of: $('.locator-button-sign-in')
        },
        resizable: false,
        width: getDialogAuthWindowWidth()
    });

    $('.locator-password-recovery-success').dialog("open");

    // 8) код для стандартного переключателя hide/unhide
    $('.action-toggle-hide').click(function(){
        if ($(this).hasClass('hide')) {
            $(this).removeClass('hide');
        } else {
            $(this).addClass('hide');
        }
    });

    // 9 )
    window.addVacancyValidation = function addVacancyValidation(form, data, hasError) {
        if (!hasError) {
            window.location.href = form.attr('data-url');
        }
        return false;
    };
    $("#Invite_firstname").val(localStorage.Invite_firstname?localStorage.Invite_firstname:'');
    $("#Invite_lastname").val(localStorage.Invite_lastname?localStorage.Invite_lastname:'');
    $("#Invite_email").val(localStorage.Invite_email?localStorage.Invite_email:'');

    $("#Invite_firstname").bind('textchange', function(e) {
        localStorage.Invite_firstname = $(this).val();
    });
    $("#Invite_lastname").bind('textchange', function(e) {
        localStorage.Invite_lastname = $(this).val();
    });
    $("#Invite_email").bind('textchange', function(e) {
        localStorage.Invite_email = $(this).val();
    });
    $("#Invite_send").bind('click', function() {
        localStorage.Invite_firstname = "";
        localStorage.Invite_lastname = "";
        localStorage.Invite_email = "";

        return true;
    });

    // 10)
    window.addEventListener('resize', function(event){
        if (1600 < $(window).width()) {
            $('body').css('background-size', $(window).width() + 'px auto ');
        } else {
            $('body').css('background-size', ' 1600px auto ');
        }

        addWindowWidthClassToBody();
    });
});

// 1)
function hideAllPopovers() {
    $(".action-display-popover").removeClass("active");
    $(".inner-popover").removeClass("active");
    $(".inner-popover").hide();
}


/**
 * 2) Определяет ширину попапа не весь экран
 * @param number padding, если у попапа нестандартные отступы,
 * то и ширину надо делать меньше/больше
 *
 * @returns {number}
 */
function getDialogWindowWidth(padding) {
    if (undefined == padding) {
        padding = 0;
    };

    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return  940;
    } else {
        return  1115 - padding;
    }
}

// 2.1) Определяет ширину стандартные "2/3 ширины сайта"
function getDialogWindowWidth_2of3() {
    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return 585;
    } else {
        return 719;
    }
}

// 2.2) Определяет ширину стандартные "2/3 ширины сайта"
function getDialogWindowWidth_2of3_wide() {
    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return 601;
    } else {
        return 735;
    }
}

// 2.3) Определяет ширину окон автиризации и автовостановления пароля
function getDialogAuthWindowWidth() {
    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return 283;
    } else {
        return 283;
    }
}

// 2.4) Определяет ширину попапа с оценкой за симуляцию
function getDialogSimulationDetailsPopupWidth() {
    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return 822;
    } else {
        return 1003;
    }
}

// 2.5) Определяет высоту попапа с оценкой за симуляцию
function getDialogSimulationDetailsPopupHeight() {
    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return 766;
    } else {
        return 935;
    }
}

// 3)
function getInviteId(url){
    return parseInt(url.replace('/simulation/promo/full/', ''), 0);
}

// 4) Feedback Submit AJAX validation
window.feedbackSubmit = function feedbackSubmit(form, data, hasError) {
    if (!hasError) {
        $.post(form.attr('action'), form.serialize(), function (res) {
            // Do stuff with your response data!
            location.reload();
        });
    }
    return false;
};

// 5) authentication Validation
window.authenticateValidation = function authenticateValidation(form, data, hasError) {

    // аккаунт не активирован
    if (undefined != data.YumUserLogin_not_activated) {
        hasError = true;
        $('#YumUserLogin_not_activate_em_').html(data.YumUserLogin_not_activated);
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('hide');
        $('#YumUserLogin_not_activate_em_').show();
    } else {
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('hide');
    }

    // аккаунт забанен
    if (undefined != data.YumUserLogin_form) {
        hasError = true;
        $('#YumUserLogin_not_activate_em_').html(data.YumUserLogin_form);
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('error');
        $('#YumUserLogin_not_activate_em_').parent().css('vertical-align', 'middle');
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('hide');
        $('#YumUserLogin_not_activate_em_').show();
    } else {
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('hide');
    }

    if (!hasError && 'undefined' == typeof data.YumUserLogin_form) {
        location.href = '/dashboard';
    }

    return false;
};

// 6)
window.passwordRecoverySubmit = function passwordRecoverySubmit(form, data, hasError) {
    if (!hasError) {
        location.reload();
    }

    return false;
};