var fixLogotypes = function() {
    var headerLogo = $("#header-main-logo");
    var footerLogo = $("#footer-main-logo");
    // update logo size
    if ($(window).width() > 1279) {
        $("#header-main-logo").attr('src', headerLogo.attr('data-src-big'));
        $("#footer-main-logo").attr('src', footerLogo.attr('data-src-big'));
    } else {
        $("#header-main-logo").attr('src', headerLogo.attr('data-src-small'));
        $("#footer-main-logo").attr('src', footerLogo.attr('data-src-small'));
    }
};

/* global $, console, jQuery, Cufon, confirm */
(function ($) {
    "use strict";
    $(document).ready(function () {

        fixLogotypes();

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

            $('body').append($('<div id="terms-pop-up"></div>'));
            $('#terms-pop-up').dialog({
                //minHeight:   400,
                dialogClass: 'terms-page popup-site bg-white popup-no-title',
                modal:       true,
                width:       980,
                height:      dHeight,
                autoOpen:    false,
                resizable:   false,
                open: function() {
                    $("html").css("overflow-y","hidden");
                },
                close: function () {
                    $("html").css("overflow-y","visible");
                },
                draggable: false
            });

            $.ajax('/static/terms', {
                success: function(data) {
                    $('#terms-pop-up').html(data).dialog('open');
                    $('#terms-pop-up').css("min-height","374px");
                    $("#terms-pop-up").scrollTop($("#terms-pop-up h1.total").scrollTop());
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
            var selected = $(this).attr('data-selected');
            $('#feedback-dialog').dialog({
                width: 706,
                dialogClass: 'popup-primary popup-site feedbackwrap',
                modal: true,
                resizable: false,
                position: {
                    my: "center center",
                    at: "center center"
                },
                draggable: false,
                open: function( event, ui ) {
                    if(selected !== undefined) {
                        $('#feedback-form').find('.sbOptions').find('li').each(function(index, element){
                            var a = $(element).find('a');
                            if(a.attr('rel') === selected){
                                a.click();
                            }
                        });
                    }

                    Cufon.refresh();
                }
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
            if (confirm("Вы желаете удалить позицию \"" + $(this).parent().parent().find('td:eq(1)').text() + "\"?")) {
                // link go ahead to delete URL
            } else {
                event.preventDefault();
            }
        });
        // delete vacancy }

        $(window).on('resize', function () {
            Cufon.refresh();

            fixLogotypes();
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
                dialogClass: 'add-vacancy-popup popup-primary popup-site title-bold submit-primry',
                closeOnEscape: true,
                minHeight: 350,
                modal: true,
                resizable: false,
                draggable: false,
                title: '',
                width: 584,
                position: {
                    my: "center top",
                    at: "center bottom",
                    of: $('.page-header')
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

        // homepage {
        var iframesrc = $(".iframe-video iframe").attr("src");
        var iframesrcautoplay = iframesrc +'?autoplay=1';

        var popupwidth = $("header").width() * 0.9;
        var video = $(".iframe-video-wrap").html();


        $(".video").click(function(){
            $(video).dialog({
                modal: true,
                resizable: false,
                height: 354,
                width: popupwidth,
                dialogClass:"popup-video",
                position: {
                    my: "center top",
                    at: "center bottom",
                    of: $('header')
                },
                show: {
                    effect: "clip",
                    duration: 1000
                },
                hide: {
                    effect: "puff",
                    duration: 500
                }
            });
            $(".popup-video .iframe-video iframe").attr("src",iframesrcautoplay);
            $('.popup-video .ui-dialog-titlebar').remove();
            $('.popup-video').prepend('<a class="popupclose" href="javascript:void(0);"></a>');
            $('.popup-video a.popupclose').click(function() {
                $('.iframe-video').dialog('close');
                $('.popup-video a.popupclose').remove();
                $('.iframe-video').detach();
            });

        });

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
        // homepage }
    });
})(jQuery);

displayError = function(msg) {
    $('#user-email-error-box').text(msg);
    //$('#user-email-error-box').css('top', '-' + ($('#user-email-error-box').height()) + 'px');
    $('#user-email-error-box').show();
    $('#user-email-value').css({"border":"2px solid #BD2929","margin-top":"-2px"});
}

hideError = function() {
    $('#user-email-error-box').hide();
    $('#user-email-error-box').text('');
    $('#user-email-value').css({"border":"none","margin-top":"0"});
}



