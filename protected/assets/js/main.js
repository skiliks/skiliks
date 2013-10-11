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
            var simStartLink = $(this).attr('data-accept-link');

            // удлиннить окно чтоб футер был ниже нижнего края попапа
            $('.content').css('margin-bottom', '600px');

            $('#invite-accept-form').dialog({
                dialogClass: 'accept-invite-warning-popup full-simulation-info-popup margin-top-popup',
                modal:       true,
                autoOpen:    true,
                resizable:   false,
                draggable:   false,
                width:       881,
                maxHeight:   600,
                position: {
                    my: "left top",
                    at: "left bottom",
                    of: $("header h1")
                },
                open: function( event, ui ) {
                    $(this).find('.accept-requirements').attr('href', link);
                    $(this).find('.start-full-simulation').attr('data-href', simStartLink);
                }
            });

            // hack {
            $('.accept-invite-warning-popup full-simulation-info-popup').css('top', '50px');
            $(window).scrollTop('.narrow-contnt');

            // hack }

            return false;
        });

        $(".registration_check").click(function () {

            if($(this).parent().hasClass('form-account-personal')) {
                if ($(this).hasClass('icon-chooce')) {
                    $(this).removeClass('icon-chooce');
                    $(this).addClass('icon-check');
                    $(this).find("span").css('display', 'none');
                    console.log($(this).parent().get());
                    $(this).parent().css('background-color', '#fee374');
                    $('.form-account-corporate').find('.registration_check').removeClass('icon-check');
                    $('.form-account-corporate').find('.registration_check').addClass('icon-chooce');
                    $('.form-account-corporate').css('background-color', '');
                    $('.form-account-corporate').find('.registration_check').find("span").css('display', 'block');
                    $("#account-type").find('input').val('personal');
                }
            }else if($(this).parent().hasClass('form-account-corporate')) {
                if ($(this).hasClass('icon-chooce')) {
                    console.log("icon-chooce");
                    $(this).removeClass('icon-chooce');
                    $(this).addClass('icon-check');
                    $(this).find("span").css('display', 'none');
                    $(this).parent().css('background-color', '#fee374');
                    $('.form-account-personal').find('.registration_check').removeClass('icon-check');
                    $('.form-account-personal').find('.registration_check').addClass('icon-chooce');
                    $('.form-account-personal').css('background-color', '');
                    $('.form-account-personal').find('.registration_check').find("span").css('display', 'block');
                    $("#account-type").find('input').val('corporate');
                }
            }else{
                throw new Error("Bad choice");
            }
            return false;
        });

        // попап перед стартом лайт симуляции при регистрации
        $('#yum-user-registration-form-activation-success').submit(function() {
            var startLiteCheckbox = $('#registration_check');

            // если пользователь выбрал играть "Демо-версию" {
            if (startLiteCheckbox.hasClass('icon-check')) {
                $(".lite-simulation-info-popup").dialog({
                    closeOnEscape: true,
                    dialogClass: 'popup-before-start-sim lite-simulation-info-dialog',
                    minHeight: 220,
                    modal: true,
                    resizable: false,
                    width:881,
                    draggable: false,
                    open: function( event, ui ) {
                        Cufon.refresh();
                        $('.start-lite-simulation-now').click(function() {
                            $('.start-lite-simulation-now').addClass('clicked');
                            $('#yum-user-registration-form-activation-success').submit();
                        });
                    }
                });
             // если пользователь выбрал играть "Демо-версию" }
            } else {
                // если пользователь выбрал НЕ играть "Демо-версию"
                return true;
            }

            if ($('.start-lite-simulation-now').hasClass('clicked')) {
                return true;
            }

            // первый раз не надо делать submit() -> мы отображаем ".lite-simulation-info-popup"
            return false;
        });

        // попап перед стартом лайт симуляции в кабинетах
        $('.start-lite-simulation-btn').click(function(event) {
            event.preventDefault();

            // get URL for lite simulation
            var href = $(this).attr('data-href');

            $(".lite-simulation-info-popup").dialog({
                closeOnEscape: true,
                dialogClass: 'popup-before-start-sim lite-simulation-info-dialog',
                minHeight: 220,
                modal: true,
                resizable: false,
                width:881,
                draggable: false,
                open: function( event, ui ) {
                    Cufon.refresh();
                    $('.start-lite-simulation-now').click(function() {
                        location.assign(href);
                    });
                }
            });
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
                $("#cash-month-selected").val($("#month-selected").val());
                $.post("/payment/invoiceSuccess", form.serialize(), function (res) {
                    window.location.href = "/payment/invoiceSuccess";
                });
            }
            return false;
        };

        window.referralRegistration = function referralRegistration(form, data, hasError) {
            if (!hasError) {
                    window.location.href = "/dashboard";
            }
            return false;
        };

        window.inviteFriend = function inviteFriend(form, data, hasError) {
            $(".sendReferralInviteSubmitButton").val("Отправить");
            if (!hasError) {
                window.location.href = "/dashboard";
            }
            else {
                var k = data.ReferralsInviteForm_emails;
                $("#ReferralsInviteForm_emails_em_").css("position", "static");
                $("#ReferralsInviteForm_emails_em_").html("<ul></ul>");
                for (var i in k) {
                    $("#ReferralsInviteForm_emails_em_ ul").append("<li>"+k[i]+"</li>");
                }
            }
            return false;
        }

        window.changeInviteReferralSubmitButton = function changeInviteReferralSubmitButton() {
            $(".sendReferralInviteSubmitButton").val("Идёт проверка данных");
            return true;
        }

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

        function infoPopup_aboutFullSimulation(href){
            $(".dashboard .full-simulation-info-popup").dialog({
                closeOnEscape: true,
                dialogClass: 'popup-before-start-sim',
                minHeight: 220,
                modal: true,
                resizable: false,
                width:881,
                open: function( event, ui ) {
                    $('.start-full-simulation-next').attr('data-href', href);
                    Cufon.refresh();
                }
            });
        }

        function getInviteId(url){
            return parseInt(url.replace('/simulation/promo/full/', ''), 0);
        }
        $('.start-full-simulation').click(function(event){

            $('#invite-accept-form').dialog('close');

            var href = $(this).attr('data-href');
            event.preventDefault();
            // удлиннить окно чтоб футер был ниже нижнего края попапа
            $('.content').css('margin-bottom', '80px');

            $.ajax({
                url:'/simulationIsStarted',
                dataType:  "json",
                data:{invite_id:getInviteId(href)},
                success:function(data) {
                    var dataGlobal = data;
                    // проверка наличия незавершенных фулл симуляций самому-себе
                    if (0 < parseInt(data.count_self_to_self_invites_in_progress)) {
                        $(".exists-self-to-self-simulation-warning-popup").dialog({
                            closeOnEscape: true,
                            dialogClass: 'popup-before-start-sim',
                            minHeight: 220,
                            modal: true,
                            resizable: false,
                            width:881,
                            open: function( event, ui ) {
                                Cufon.refresh();

                                // пользователь выбирает не прерывать текущую симуляцию
                                $('.exists-self-to-self-simulation-warning-popup .back-button').click(function(){
                                    $('.exists-self-to-self-simulation-warning-popup').dialog('close');
                                });

                                // пользователь выбирает начать новую симуляцию, не смотря на наличие незавершенных
                                $('.exists-self-to-self-simulation-warning-popup .go-ahead-button').click(function(){

                                    // закрыть текущий попап
                                    $('.exists-self-to-self-simulation-warning-popup').dialog('close');

                                    // запрос на удаление всех незавершенных фулл симуляций самому-себе
                                    $.ajax({ url: '/static/break-simulations-for-self-to-self-invites'});

                                    // отображаем свтупительный попап
                                    displaySimulationInfoPopUp(href, dataGlobal);
                                });
                            }
                        });
                    } else {
                        // незавершенных симуляций нет
                        displaySimulationInfoPopUp(href, dataGlobal);
                    }
                }
            });

            // hack {
            $('.popup-before-start-sim').css('top', '50px');
            $(window).scrollTop('body');
            // hack }

            return false;
        });

        var displaySimulationInfoPopUp =  function(href, data) {
            // в случае если пользователь выбрал начать новую симуляцию,
            // не смотря на наличие незавершенных сам-себе
            // то предупреждение будет дублирующим,
            // а если он пытает начать фулл симуляцию по приглашению от работодателя второй раз,
            // то предупреждение нужно
            if(data.user_try_start_simulation_twice &&
                0 == parseInt(data.count_self_to_self_invites_in_progress)) {
                // предупреждение о попытке повторного начала симуляции
                $(".pre-start-popup").dialog({
                    closeOnEscape: true,
                    dialogClass: 'popup-before-start-sim',
                    minHeight: 220,
                    modal: true,
                    resizable: false,
                    width:881,
                    open: function( event, ui ) {
                        $('.start-full-simulation-next').attr('data-href', href);
                        Cufon.refresh();
                    }
                });
            } else {
                // информауия про ключевые моменты в сценарии фулл симуляции
                // что я? где я? сотрудники, цели.
                infoPopup_aboutFullSimulation(href);
            }
        }

        $('.start-full-simulation-passed').click(function(event){
            event.preventDefault();
            var href = $(this).attr('data-href');
            $.ajax({url:'/userStartSecondSimulation', data:{invite_id:getInviteId(href)}});
            $(".pre-start-popup").dialog('close');
            infoPopup_aboutFullSimulation(href);
            return false;
        });

        $('.start-full-simulation-close').click(function(event){
            event.preventDefault();
            var href = $(this).attr('data-href');
            $.ajax({url:'/userRejectStartSecondSimulation', data:{invite_id:getInviteId(href)}});
            $(".pre-start-popup").dialog('close');
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

        $(".start-simulation-from-popup").click(function() {
            $("#invite-accept-form").dialog("close");
        });

        $("#month-selected").change(function() {
            $("#cash-month-selected").val($( "#month-selected option:selected").val());
        });

        $(".question-container li").click(function() {
            if(!$(this).children("div").is(":visible")) {
                $(this).children("div").slideDown("fast");
                $(this).css('color', '#146672');
                $(this).addClass("active");
                }
            else {
                $(this).children("div").slideUp("fast");
                $(this).css('color', '#555742');
                $(this).removeClass("active");
            }
            Cufon.refresh();
        })

        $(".change-simulation-result-render").click(function() {
            $.post("/dashboard/remakeRenderType", {remakeRender : "true"}).done(function() {
                location.reload();
            })
        });




        $(".showDialogRejected").click(function(e){
            e.preventDefault();
            var reason = $(this).attr('data-reject-reason');
            $(".dialogReferralRejected").dialog({
                dialogClass: 'popup-before-start-sim',
                closeOnEscape: true,
                minHeight: 20,
                modal: true,
                resizable: false,
                draggable: false,
                title: false,
                width: 544,
                position: {
                    my: "right top",
                    at: "right top",
                    of: ".referalls_list_box"
                },
                open : function() {
                    $(".reject-reason-p").html(reason);
                }
            });
            Cufon.refresh();
            $(".ui-dialog-titlebar").removeClass('ui-widget-header');
            return false;
        });

        $(".showDialogPending").click(function(e){
            e.preventDefault();
            var domain = $(this).attr('data-domain');
            $(".dialogReferralPending").dialog({
                dialogClass: 'popup-before-start-sim',
                closeOnEscape: true,
                minHeight: 20,
                modal: true,
                resizable: false,
                draggable: false,
                title: false,
                width: 544,
                position: {
                    my: "right top",
                    at: "right top",
                    of: ".referalls_list_box"
                }
            });
            $(".ui-dialog-titlebar").removeClass('ui-widget-header');
            return false;
        });

        $('a.feedback-close-other').on('click', function (e) {
            e.preventDefault();
            $(".dialogReferralRejected").dialog("close");
            var selected = $(this).attr('data-selected');
            $('#feedback-dialog').dialog({
                width: 706,
                dialogClass: 'popup-primary popup-site feedbackwrap',
                modal: true,
                resizable: false,
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


        $(".percentile-hover-toggle-span").hover(
            function() {
                setTimeout(function(){
                    if($(".percentile-hover-toggle-span" + ":hover").length > 0) {
                        $(".popover").addClass("active");
                    }
                }, 2000);
            },
            function() {
                $(".popover").removeClass("active");
            }
        );

    });
})(jQuery);



