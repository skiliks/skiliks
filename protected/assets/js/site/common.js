$(document).ready(function () {

    $(".log-out-link").click(function(){
        var lastGetState = new Date();
        if(localStorage.getItem('lastGetState') === null){
            return true;
        } else if(lastGetState.getTime() <= (parseInt(localStorage.getItem('lastGetState')) +30000)) {
            if (window.confirm("У вас есть незавершенная симуляция. Если вы выйдете из аккаунта, то потеряете все результаты")) {
                //window.alert("Ок");
                return true;
            } else {
                //window.alert("Тупак");
                return false;
            }
        } else {
            return true;
        }
    });
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

        if(!$(e.target).is('.action-show-product-submenu')) {
            $('.locator-product-submenu').hide();
            $('.locator-submenu-switcher').removeClass('open');
        }

        if(!$(e.target).is('.action-show-about-us-submenu')) {
            $('.locator-about-us-submenu').hide();
            $('.locator-submenu-switcher-about-us').removeClass('open');
        }
    });
    // action-display-popover }

    // 2) Стализация выпадающих списков
    // @link http://www.bulgaria-web-developers.com/projects/javascript/selectbox/
    $("select").selectbox();

    window.displayFeedbackDialog = function (e) {
        var selected = $(this).attr('data-selected');
        $('.locator-feedback-dialog').dialog({
            width: getDialogWindowWidth_2of3(),
            height: 400,
            dialogClass: 'popup-form background-image-two-lamps hide-ui-dialog-content' ,
            modal: true,
            resizable: false,
            draggable: false,
            open: function() {
                stickyFooterAndBackground();
            }
        });

        $(window).resize(function(){
            $('.locator-feedback-dialog').dialog('option', 'width', getDialogWindowWidth_2of3());
            $('.locator-feedback-dialog').dialog('option', 'position', 'center center');
        });
    };

    // 3) feedback
    $('.action-feedback').on('click', window.displayFeedbackDialog);

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
            stickyFooterAndBackground();
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

                stickyFooterAndBackground();
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
                stickyFooterAndBackground();
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

        stickyFooterAndBackground();
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

        // Исправляет смещение меню О продукте
        // смещение должно рассчитыватсья после того как в body добавлен класс width-1024
        // иначе смещение будет неправильное
        fixProductDropDown();

        // footer
        stickyFooterAndBackground();
    });

    stickyFooterAndBackground();

    // 11) Выпадающее меню О продукте
    $('header .action-show-product-submenu').click(function() {
        fixProductDropDown();

        $('header .locator-product-submenu').toggle();

        var switcher = $('header .locator-submenu-switcher');

        if (switcher.hasClass('open')) {
            switcher.removeClass('open');
        } else {
            switcher.addClass('open');
        }
    });

    $('footer .action-show-product-submenu').click(function() {
        fixProductDropDown();

        $('footer .locator-product-submenu').toggle();

        var switcher = $('footer .locator-submenu-switcher');

        if (switcher.hasClass('open')) {
            switcher.removeClass('open');
        } else {
            switcher.addClass('open');
        }
    });

    // 12.1) Выпадающее меню About us
    $('header .action-show-about-us-submenu').click(function() {
        fixAboutUsDropDown();

        $('header .locator-about-us-submenu').toggle();

        var switcher = $('header .locator-submenu-switcher-about-us');

        if (switcher.hasClass('open')) {
            switcher.removeClass('open');
        } else {
            switcher.addClass('open');
        }
    });

    $('footer .action-show-about-us-submenu').click(function() {
        fixAboutUsDropDown();

        $('footer .locator-about-us-submenu').toggle();

        var switcher = $('footer .locator-submenu-switcher-about-us');

        if (switcher.hasClass('open')) {
            switcher.removeClass('open');
        } else {
            switcher.addClass('open');
        }
    });
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
        return  900;
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

// SKILIKS-6025
// Предупреждение что аккаунт забанен
window.displayYourAccountBannedFlashMessage = function() {

    if ('undefined' == typeof window.userEmail) {
        window.userEmail = $('#YumUserLogin_username').val();
    }

    if (0 == $(".locator-account-banned").length) {
        $('body').append(
            '<div class="locator-account-banned flash-data hide">'
                + 'Ваш аккаунт заблокирован (более 10 неудачных попыток авторизации). '
                + 'Вам на почту ' + window.userEmail
                + ' отправлено письмо с инструкциями по восстановлению аккаунта. '
                + 'Если вы испытываете затруднения - свяжитесь пожалуйста со '
                + '<span class="action-feedback-banned inter-active color-146672">службой поддержки</span>.'
          + '</div>'
        );
    }

    var dialog = $(".locator-account-banned");

    dialog.dialog({
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
            $('.locator-account-banned').removeClass('hide');
            stickyFooterAndBackground();
        },
        close: function() {
            $('.locator-account-banned').addClass('hide');
        }
    });

    // autoOpen переписан нами, и теперь центритует dialog по высоте
    // а флеш-сообщения надо по высоте тавнять с низом header
    $(".locator-account-banned").dialog('open');

    $(window).resize(function(){
        $('.locator-account-banned').dialog('option', 'width', getDialogWindowWidth_2of3());
        $('.locator-account-banned').dialog('option', 'position', 'center center');
    });

    dialog.dialog("open");

    // навешиваем псевдо ссылку, для удобства пользователей
    $('.action-feedback-banned').click(function() {
        // закрываем предуареждение - чтоб  не негромождать окон
        $('.locator-account-banned').dialog('close');

        // открываем диалог обратной связи
        window.displayFeedbackDialog();

        // выбираем для жалобы правильный пункт меню, вместо пользователей
        // http://www.bulgaria-web-developers.com/projects/javascript/selectbox/
        $('#Feedback_theme').selectbox('change', 'Регистрация и авторизация', 'Регистрация и авторизация');

        // и емейл пользователя мы тоже знаем:
        $('#Feedback_email').val(window.userEmail);
    })
}

// для страницы user/auth
if (true == window.yourAccountBanned) {
    window.displayYourAccountBannedFlashMessage();
}

// 5) authentication Validation
window.authenticateValidation = function authenticateValidation(form, data, hasError) {

    // аккаунт не активирован
    if (undefined != data.YumUserLogin_not_activated) {
        hasError = true;
        $('#YumUserLogin_not_activate_em_').html(data.YumUserLogin_not_activated);
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('hide');
        $('#YumUserLogin_not_activate_em_').show();
        return false;
    } else {
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('hide');
    }

    // Account banned
    if (undefined != data.YumUserLogin_form) {
        hasError = true;
        $('#YumUserLogin_not_activate_em_').html(data.YumUserLogin_form);
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('error');
        $('#YumUserLogin_not_activate_em_').parent().css('vertical-align', 'middle');
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('hide');
        $('#YumUserLogin_not_activate_em_').show();
        return false;
    } else {
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('hide');
    }

    // аккаунт забанен
    if (undefined != data.YumUserLogin_bruteforce) {
        // специальное уведомление в случае если "аккаунт забанен":
        // SKILIKS-6025 {
        window.userEmail = '' + $('#YumUserLogin_username').val();
        $('.locator-box-sign-in').dialog('close');

        window.displayYourAccountBannedFlashMessage();
        // SKILIKS-6025 }
    } else {
        $('#YumUserLogin_not_activate_em_').parent().parent().removeClass('error');
        $('#YumUserLogin_not_activate_em_').parent().parent().addClass('hide');
    }

    if (!hasError
        && 'undefined' == typeof data.YumUserLogin_form
        && 'undefined' == typeof data.YumUserLogin_bruteforce) {
        location.href = '/dashboard';
    }

    return false;
};

// 6)
window.passwordRecoverySubmit = function passwordRecoverySubmit(form, data, hasError) {

    if (undefined != data. YumPasswordRecoveryForm_bruteforce) {
        // специальное уведомление в случае если "аккаунт забанен":
        // SKILIKS-6025 {
        window.userEmail = $('#YumPasswordRecoveryForm_email').val();
        $('.locator-password-recovery').dialog('close');

        window.displayYourAccountBannedFlashMessage();
        // SKILIKS-6025 }
    } else if (!hasError) {
        location.reload();
    }

    return false;
};

// 7)
function stickyFooterAndBackground() {
    // Footer
    var bodyHeight = $('body').height();
    var windowHeight = $(window).height();
    var footerBottom = $('footer').offset().top + $('footer').height();

    var helpHeight = 0;
    if (0 < $('.question-container').length) {
        var helpHeight = $('.question-container').height();
    }

    if (footerBottom == windowHeight && 0 == helpHeight) {
        return;
    }

    console.log('helpHeight : ', helpHeight );
    console.log(footerBottom < windowHeight, footerBottom, windowHeight);

    if (footerBottom <= windowHeight && helpHeight < 400) {
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

    // это надо только для стартовой
    // в мобильных браузерах её вёрстка едет из-за широкой, не симметричной нижней картинки с героями игры
    // над футером
    $('.action-controller-index-static-pages .locator-global-container').css('width', $(window).width() + 'px');
}

// 8)
// Исправляет смещение меню "О продукте"
function fixProductDropDown() {
    if (0 < $('header .static-page-links .locator-submenu-switcher').length) {
        $('header .static-page-links .locator-product-submenu').css(
            'left',
            $('header .static-page-links .locator-submenu-switcher').offset().left
        );
    }

    // не много магических чисел:
    var offsetLeft = 220;
    var offsetTop = 126;
    if ($('body').hasClass('width-1024')) {
        offsetLeft = 193;
        offsetTop = 97;
    }
    $('footer .locator-product-submenu').css('left', $('footer .locator-submenu-switcher').offset().left - offsetLeft);
    $('footer .locator-product-submenu').css('top', offsetTop);
}

// 8)
// Исправляет смещение меню "About us"
function fixAboutUsDropDown() {
    if (0 < $('header .static-page-links .locator-submenu-switcher-about-us').length) {
        $('header .static-page-links .locator-about-us-submenu').css(
            'left',
            $('header .static-page-links .locator-submenu-switcher-about-us').offset().left
        );
    }

    // не много магических чисел:
    var offsetLeft = 127;
    var offsetTop = 91;
    if ($('body').hasClass('width-1024')) {
        offsetLeft = 76;
        offsetTop = 65;
    }
    $('footer .locator-about-us-submenu').css('left', $('footer .locator-submenu-switcher-about-us').offset().left - offsetLeft);
    $('footer .locator-about-us-submenu').css('top', offsetTop);
}

/**
 * Форматирование цены
 * http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript
 *
 * @param integer decimalLen: length of decimal
 * @param integer integerLen: length of sections
 */
Number.prototype.format = function(decimalLen, integerLen) {
    var thousandsSeparator = ' ';
    var re = '\\d(?=(\\d{' + (integerLen || 3) + '})+' + (decimalLen > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~decimalLen)).replace(new RegExp(re, 'g'), '$&' + thousandsSeparator);
};
