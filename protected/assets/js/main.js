
/* global $, console, jQuery, Cufon, confirm */
(function ($) {
    "use strict";
    $(document).ready(function () {

        // fixSimResultsDialog {
        var fixSimResultsDialog = function () {
            var heightOverhead = 300;
            $('div.content').height($('.simulation-result-popup').height() - heightOverhead + 'px');
            $('.simulation-result-popup').css('top', '50px');

            var overlay = $('.ui-widget-overlay');
            overlay.height($('.simulation-result-popup').height() + 150 + 'px');
            if (overlay.height() < $(document).height()) {
                overlay.height($(document).height() + 'px');
            }
        };
        // fixSimResultsDialog  }

        // render simulation details pop-up {
        $('.dashboard').append($('<div id="simulation-details-pop-up"></div>'));
        var simulation_popup = $('#simulation-details-pop-up');

        simulation_popup.dialog({
            dialogClass: 'simulation-result-popup',
            modal:       true,
            width:       980,
            minHeight:   600,
            autoOpen:    false,
            resizable:   false,
            open:        fixSimResultsDialog
        });
        // render simulation details pop-up }

        window.showSimulationDetails = function (url) {
            $.ajax({
                url:     url,
                success: function (data) {
                    simulation_popup.html(data);
                    simulation_popup.dialog('open');
                    Cufon.refresh();
                    // fixSimResultsDialog {
                    $('.simulation-details .estmfooter a').click(function () {
                        fixSimResultsDialog();
                    });
                    $('.simulation-details .navigation a').click(function () {
                        fixSimResultsDialog();
                    });
                    // fixSimResultsDialog }
                }
            });
        };

        // load simulation details pop-up data {
        $(".view-simulation-details-pop-up").click(function (event) {
            event.preventDefault();
            window.showSimulationDetails($(this).attr('data-simulation'));
        });
        // load simulation details pop-up data }



        $('.terms').click(function() {
            var dHeight = $("html").height() * 0.85;
            $('.container').append($('<div id="terms-pop-up"></div>'));
            $('#terms-pop-up').dialog({
                dialogClass: 'terms-page',
                modal:       true,
                width:       980,
                minHeight:   400,
                height: dHeight,
                autoOpen:    false,
                resizable:   false
            });

            $.ajax('/static/terms', {
                success: function(data) {
                    $('#terms-pop-up').html(data).dialog('open');
                }
            });

            return false;
        });


        $("#registration_check").click(function () {
            if ($(this).hasClass('icon-check')) {
                $(this).removeClass('icon-check');
                $(this).addClass('icon-chooce');
                $('#YumUser_is_check').val('0');
                $("#registration_check").find("span").css('display', 'block');
                if (1 === $('#registration_switch').length) {
                    $('#registration_switch').val($('#registration_switch').attr('data-next'));
                }
            } else if ($(this).hasClass('icon-chooce')) {
                $(this).removeClass('icon-chooce');
                $(this).addClass('icon-check');
                $('#YumUser_is_check').val('1');
                $("#registration_check span").css('display', 'none');
                if (1 === $('#registration_switch').length) {
                    $('#registration_switch').val($('#registration_switch').attr('data-start'));
                }
            }
            return false;
        });

        // product page, test results - sub list hide/show switcher
        $('.hassubmenu a.sub-menu-switcher').click(function () {
            if ($(this).parent().hasClass('subisopen')) {
                $(this).parent().removeClass('subisopen');
            } else {
                $(this).parent().addClass('subisopen');
            }
        });
        $('a.feedback').on('click', function (e) {
            $('#feedback-dialog').dialog({
                width: 700,
                dialogClass: 'feedbackwrap',
                modal: true,
                resizable: false,
                draggable: false,
                open: function( event, ui ) { Cufon.refresh(); }
            });

            e.stopPropagation();
        });

        window.feedbackSubmit = function feedbackSubmit(form, data, hasError) {
            if (!hasError) {
                $.post(form.attr('action'), form.serialize(), function (res) {
                    // Do stuff with your response data!
                    location.reload();
                });
            }
            return false;
        };

        window.passwordRecoverySubmit = function passwordRecoverySubmit(form, data, hasError) {
            if (!hasError) {
                $.post(form.attr('action'), form.serialize(), function (res) {
                    // Do stuff with your response data!
                    location.reload();
                });
            }
            return false;
        };

        $('.sign-in-box form#login-form').submit(function(event) {
            return false;
        });

        // Ajax Validation {

        window.authenticateValidation = function authenticateValidation(form, data, hasError) {

            // clean custom message "your email is already registered, you may activate your email"
            $('.sign-in-box #yum-login-global-errors').html('');

            if (!hasError && 'undefined' == typeof data.YumUserLogin_form) {
                $.post(form.attr('action'), form.serialize(), function (res) {
                    // Do stuff with your response data!
                    location.href = '/dashboard';
                    // location.reload();
                });
            } else {
                if (data.YumUserLogin_form) {
                    $('.sign-in-box #yum-login-global-errors').html(data.YumUserLogin_form);
                }
            }

            return false;
        };

        window.addVacancyValidation = function addVacancyValidation(form, data, hasError) {
            if (!hasError) {
                window.location.href = form.attr('data-url');
            }
            return false;
        };

        window.paymentSubmit = function paymentSubmit(form, data, hasError) {
            if (!hasError) {
                $.post(form.attr('action'), form.serialize(), function (res) {
                    var result = $('<div class="order-result"/>').html(res);
                    $('.order-methods').html(result);
                });
            }
            return false;
        };

        // Ajax Validation }

        // delete vacancy {
        $('a.delete-vacancy-link').click(function(event) {
            if (confirm("Вы желаете удалить вакансию \"" + $(this).parent().parent().find('td:eq(1)').text() + "\"?")) {
                // link go ahead to delete URL
                console.log('delete');
            } else {
                event.preventDefault();
            }
        });
        // delete vacancy }

        $(window).on('resize', function () {
            Cufon.refresh();
        });

        // corporate dashboard vacancy {
        $('#_invite_people_box.php').click(function(event) {
            event.preventDefault();
        });
        // corporate dashboard vacancy }

        $('a.start-trial-full-scenario-disagree').click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            $('#start-trial-full-scenario-pop-up').dialog("close");
        });

        $('a.start-trial-full-scenario-agree').click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            window.location.replace($('a.invite-for-trial-full-scenario').attr('href'));
        });
        // pop-up to inform corporate user, that trial full simulation cost 1 invite }

        // show/hide sign-in box {
        $('.sign-in-link').click(function(event){
            event.preventDefault();
            $(".sign-in-box").dialog('open');
        });

        $('.sign-in-link-in-popup').click(function(event){
            event.preventDefault();
            $(".sign-in-box").dialog('open');
            $('.flash-message-popup.flash-message-popup-error').find('.popupclose').click();
            //$('.sign-in-link').click();
            $(".link-recovery").click();
        });
        // show/hide sign-in box }

        $('#corporate-dashboard-add-vacancy').click(function(event) {
            event.preventDefault();
            $(".form-vacancy").dialog({
                dialogClass: 'add-vacancy-popup',
                closeOnEscape: true,
                minHeight: 350,
                modal: true,
                resizable: false,
                draggable: false,
                title: '',
                width: 584,
                position: {
                    my: "left top",
                    at: "left top",
                    of: $('#corporate-invitations-list-box .items')
                }
            });
            $(".form-vacancy").dialog('open');
            Cufon.refresh();
        });

        // password recovery {
        $(".link-recovery").click(function(){
            $(".sign-in-box").dialog("close");
            $(".popup-recovery").dialog('open');
            $(".popup-recovery").dialog({
                closeOnEscape: true,
                dialogClass: 'popup-recovery-view',
                minHeight: 220,
                modal: true,
                resizable: false,
                position: {
                    my: "right top",
                    at: "right bottom",
                    of: $('#top header #static-page-links')
                },
                width: 275,
                open: function( event, ui ) { Cufon.refresh(); }
            });
            return false;
        });
        // password recovery }

        // logout {
        $(".log-out-link").click(function(){
            var lastGetState = new Date();
            if(localStorage.getItem('lastGetState') === null){
               return true;
            } else if(lastGetState.getTime() <= (parseInt(localStorage.getItem('lastGetState')) +30000)) {
                if (window.confirm("У вас есть незавершённая симуляция. Выйдя вы потеряете все данные")) {
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
        // logout }

        // set recently added vacancy selected in vacancy drop-down {
        if (null !== $.cookie('recently_added_vacancy_id')) {
            $('#Invite_vacancy_id option[value=' + $.cookie('recently_added_vacancy_id') + ']').attr('selected', 'selected');
            $.cookie('recently_added_vacancy_id', null);
        }
        // set recently added vacancy selected in vacancy drop-down }

        // add CSS classes to customize error message by form-field-name
        $(".errorMessage").each(function(){
            $(this).addClass($(this).prev("input.error").attr("id"));
        });
    });
})(jQuery);

Cufon.replace('.invite-people-form input[type="submit"], .brightblock, .lightblock, .benefits, .tarifname, ' +
    '.clients h3, .main-article article h3, #simulation-details label, .features h2, .thetitle, .tarifswrap .text16, .sing-in-pop-up .ui-dialog-title, ' +
    '.form-submit-button, .midtitle, .social_networks span, .main-article h3, .registration input[type=submit], ' +
    '.registration .form h1, .registration .form li, .note, .product h2, .product section h3, .product section table td h6, .team article h2, ' +
    '.team .team-list li h4, .team .team-values h3, .registration h2, .registrationform h3, .registration .form h1, .widthblock h3, .ratepercnt, .testtime strong, ' +
    '.registration .form .row label, .register-by-link .row label, .regicon span, .register-by-link .row input[type=submit], ' +
   '.login-form h6, .login-form div input[type=submit], .dashboard aside h2, .blue-btn, .vacancy-add-form-switcher, .items th, .items td, .pager ul.yiiPager .page a, ' +
    '.vacancy-list .grid-view tr td:first-child, .features form div input[type=submit], .registrationform h3, ' +
    '.icon-choose, .testtime, .testtime strong, .benefits, .tarifswrap .text16, .value, .tarifform .value, #simulations-counter-box strong, ' +
    '.greenbtn, .cabmessage input[type="submit"], .cabmessage .ui-dialog-title, #send-invite-message-form label, .action-controller-login-auth #usercontent h2, ' +
    '.action-controller-registerByLink-static-userAuth h2.title, #invite-decline-form #form-decline-explanation input[type="submit"], section.registration-by-link .form .row input[type="submit"],' +
    '.action-controller-personal-static-simulations h1.title, .action-controller-corporate-static-simulations h1.title, .action-controller-corporate-static-simulations .grid-view table.items th,' +
    '#password-recovery-form input[type="submit"], #simulation-details-pop-up h1, .estmtileswrap h2, .estmtileswrap h2 a, .product .estmtileswrap h2, .simulation-result-popup h3,' +
    '.levellabels h3, .resulttitele, .resulttitele a, .barstitle, .total, .labeltitles h3, .labeltitles h4, .valuetitle, .resulttitele  small, .timedetail .thelabel,' +
    '.feedback #input_2, .profileform input[type="submit"], .pager ul.yiiPager .next a, .pager ul.yiiPager .previous a, .product .ratepercnt, .light-btn' +
    '.value, .tarifform .value, .light-btn, .terms-page h1, .terms-page h3, #error404-message',
    {hover: true}
);
Cufon.replace('.main-article article ul li, .container>header nav a, .features ul li, .sbHolder a, #simulation-details label, .container>header nav a, .features .error span, ' +
    '.features p.success, .flash-data, .flash-success, .flash-error, .product hgroup h6, .productfeatrs td, .product table p, .product section table th, .product section h3, ' +
    '.product section table th, .product section th h5, .product .sub-menu-switcher, .productsubmenu a, .team .team-list li p, .team .team-values ul li, .team article p, ' +
    '.footer nav a, .backtotop a, .price p, .registrationform li, .registrationform input, .register-by-link-desc, .register-by-link .row input[type=text], ' +
    '.register-by-link .row input[type=password], .register-by-link .row .cancel, .login-form label, .login-form div input[type=text],' +
    '.login-form div input[type=password], .login-form a, .invites-smallmenu-item a, .tarifform .expire-date, .tarifform small, .errorblock p, ' +
    '.chart-gauge .chart-value, .chart-bar .chart-value, .features form div input[type=text], .registrationform input[type=text], ' +
    '.registrationform input[type=password], .registrationform input[type=submit], .registrationform .errorMessageWrap .errorMessage, .cabmessage input, .cabmessage select, ' +
    '.cabmessage textarea, .cabmessage button, .feedbackwrap .ui-dialog-title, .feedback input[type="email"], .action-controller-login-auth #usercontent input[type="submit"], ' +
    '#invite-decline-form #form-decline-explanation h2, #invite-decline-form #form-decline-explanation #DeclineExplanation_reason_id' +
    'section.registration-by-link h1, section.registration-by-link .form, section.registration-by-link .form .row a.decline-link, #password-recovery-form #YumPasswordRecoveryForm_email,' +
    '.errorMessage, .simulation-details .ratepercnt, .simulation-details .navigation a, .labels a, .labels li, .labels p, .labels div, .blockvalue, .blockvalue .value, .legendtitle, .smalltitle, .smalltitle a,' +
    '.extrahours, .timevalue, .helpbuble, .feedback .form-all textarea, .feedbackwrap .ui-dialog-title, .feedback .sbHolder a, .skillstitle, .productlink,' +
    '.profileform label, .profileform  div, .form p, .form label, .items td .invites-smallmenu-item a, .estmfooter a, .sbSelector, .flash-pop-up p, .flash-pop-up a, ' +
    '.action-registration .registrationform .row input[type=submit], .thintitle, .order-status label, .order-method label, ' +
    '.method-description small, .terms-confirm, .period, .order-item h3, .feedback-dialog-title, .terms-page h2,' +
    '.terms-page p',
    {fontFamily:"ProximaNova-Regular", hover:true});
Cufon.replace('.profile-menu a', {fontFamily:"ProximaNova-Regular"});
Cufon.replace('.profile-menu .active a, .action-corporateTariff .tarifform .value, .tarifform .light-btn, #account-corporate-personal-form .row .value,' +
    '#account-personal-personal-form .row .value, .profileform input[type=submit], .inviteaction, .password-recovery-step-4, .order-methods input[type=submit], ' +
    '.tariff-name, .video-caption',
    {fontFamily:"ProximaNova-Bold", hover:true}
);
Cufon.replace('.freeacess', {hover:true});
//Cufon.replace('.light-btn', {fontFamily:"ProximaNova-Bold", hover: true});

