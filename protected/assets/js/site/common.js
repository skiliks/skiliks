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
        if(!$(e.target).is('.action-display-popover')
            && !$(e.target).is('.action-toggle-learning-goal-description-hint')) {
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

        $(window).resize(function(){
            $('.locator-feedback-dialog').dialog('option', 'width', getDialogWindowWidth_2of3());
            $('.locator-feedback-dialog').dialog('option', 'position', 'center center');
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

    $(window).resize(function(){
        $('.locator-flash').dialog('option', 'width', getDialogWindowWidth_2of3());
        $('.locator-flash').dialog('option', 'position', 'center center');
    });

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
                $(".locator-box-sign-in").addClass('overflow-hidden');
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
            $('<span class="send-vacancy-loader"></span>').insertAfter('.send-vacancy');
            $('.send-vacancy').remove();
            window.location.href = form.attr('data-url');
        }
        return false;
    };

    function addInviteCache(name, empty){
        empty = empty || false;
        var user_id =  $('#corporate-user-id').text();
        if(!localStorage['user_id_'+user_id]){
            localStorage['user_id_'+user_id] = JSON.stringify({});
        }

        var obj = JSON.parse(localStorage['user_id_'+user_id]);
        if(empty){
            obj[name] = '';
        }else{
            obj[name] = $('#'+name).val();
        }
        localStorage['user_id_'+user_id] = JSON.stringify(obj);
    }

    function getInviteCache(name){
        var user_id =  $('#corporate-user-id').text();
        if(!localStorage['user_id_'+user_id]){
            return false;
        }
        var obj = JSON.parse(localStorage['user_id_'+user_id]);
        if(obj[name]){
            $('#'+name).val(obj[name]);
        }
        return true;
    }
    getInviteCache('Invite_firstname');
    getInviteCache('Invite_lastname');
    getInviteCache('Invite_email');

    $("#Invite_firstname").bind('textchange', function(e) {
        addInviteCache('Invite_firstname');
    });
    $("#Invite_lastname").bind('textchange', function(e) {
        addInviteCache('Invite_lastname');
        //localStorage['user_id_'+user_id].Invite_lastname = $(this).val();
    });
    $("#Invite_email").bind('textchange', function(e) {
        addInviteCache('Invite_email');
        //localStorage['user_id_'+user_id].Invite_email = $(this).val();
    });
    $("#Invite_send").bind('click', function() {
        addInviteCache('Invite_firstname', true);
        addInviteCache('Invite_lastname', true);
        addInviteCache('Invite_email', true);
        //localStorage['user_id_'+user_id].Invite_firstname = "";
        //localStorage['user_id_'+user_id].Invite_lastname = "";
        //localStorage['user_id_'+user_id].Invite_email = "";

        return true;
    });

    // 10) global Background +  Footer
    window.addEventListener('resize', function(event) {
        // width-1024 class
        addWindowWidthClassToBody();

        // footer
        stickyFooterAndBackground();
    });

    stickyFooterAndBackground();
});

$(window).load(function(){
    stickyFooter();
});

// 1)
function hideAllPopovers() {
    $(".action-display-popover").removeClass("active");
    $(".inner-popover").removeClass("active");
    $(".inner-popover").addClass('hide');
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
        return 730; //766
    } else {
        return 810; //935
    }
}

// 2.6) Определяет высоту попапа "Правила прохождения симуляции"
function getDialogSimulationRulesPopupHeight() {
    // window.standardMinWindowWidth найдено экспериментально
    if (getWindowWidth() < window.standardMinWindowWidth) {
        return 850;
    } else {
        return 920;
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

// 7
function stickyFooterAndBackground() {
    // Footer
    var bodyHeight = $('body').height();
    var windowHeight = $(window).height();
    var footerBottom = $('footer').offset().top + $('footer').height()

    if (footerBottom < windowHeight) {
        $('footer').css('position', 'absolute');
    } else {
        $('footer').css('position', 'relative');
    }

    // Background
    if (1600 < $(window).width()) {
        $('body').css('background-size', $(window).width() + 'px auto');
    } else {
        $('body').css('background-size', ' 1600px auto ');
    }
}