var fixLogotypes = function() {
    console.log($(window).width());
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

            $('.container').append($('<div id="terms-pop-up"></div>'));
            $('#terms-pop-up').dialog({
                //minHeight:   400,
                dialogClass: 'terms-page',
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

        $('.accept-invite').click(function(e) {
            var link = $(this).attr('href');

            $('#invite-accept-form').dialog({
                dialogClass: 'accept-invite-warning-popup',
                modal:       true,
                autoOpen:    true,
                resizable:   false,
                draggable:   false,
                width:       940,
                open: function( event, ui ) {
                    $(this).find('.accept-requirements').attr('href', link);
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
                if ($('#registration_hint').length) {
                    $('#registration_hint').css('visibility', 'visible');
                }
            } else if ($(this).hasClass('icon-chooce')) {
                $(this).removeClass('icon-chooce');
                $(this).addClass('icon-check');
                $('#YumUser_is_check').val('1');
                $("#registration_check span").css('display', 'none');
                if (1 === $('#registration_switch').length) {
                    $('#registration_switch').val($('#registration_switch').attr('data-start'));
                }
                if ($('#registration_hint').length) {
                    $('#registration_hint').css('visibility', 'hidden');
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
                draggable: false,
                open: function( event, ui ) {
                    //Cufon.refresh();
                    console.log();
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
            if (confirm("Вы желаете удалить вакансию \"" + $(this).parent().parent().find('td:eq(1)').text() + "\"?")) {
                // link go ahead to delete URL
                console.log('delete');
            } else {
                event.preventDefault();
            }
        });
        // delete vacancy }

        $(window).on('resize', function () {
            console.log('resize');
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

        $('.start-full-simulation').click(function(event){
            var href = $(this).attr('data-href');
            event.preventDefault();
            $(".warning-popup").dialog({
                closeOnEscape: true,
                dialogClass: 'popup-before-start-sim',
                minHeight: 220,
                modal: true,
                resizable: false,
                open: function( event, ui ) {
                    $('.start-full-simulation-next').attr('data-href', href);
                    Cufon.refresh();
                }
            });
            return false;
        });

        $('.start-full-simulation-now').click(function(event){
            event.preventDefault();
            var href = $(this).attr('data-href');
            location.assign(href);
            return false;
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



