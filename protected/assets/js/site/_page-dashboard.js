
$(document).ready(function () {

    // 1) сокрытие двух последних колонок с ссылками-действиями: "удалить", т.п.
    $('.items').find('th:eq(7)').remove();
    $('.items').find('th:eq(6)').remove();

    // 2) меню с шестерёнкой

    // 2.1.) append pop-up sub-menu {
    if (2 < $('.items tr').length || '' != $('.items tr:eq(1) td:eq(3)').text()) { //fix for empty list
        $('.items tr').each(function(){
            $(this).find('td:eq(0)').html(
                '<span class="table-menu-switcher inter-active action-switch-menu"></span>' +
                    '<div class="table-menu" >' +
                    '</div><span class="topline"></span>'
            );
        });
    }
    // append pop-up sub-menu }

    // 2.2) Наполняем меню ссылками из последних двух колонок таблицы
    $('.table-menu-switcher').each(function(){
        // move links from last 3 TD to pop-up sub-menu
        $(this).next().append(
            $(this).parent().parent().find('td:eq(6)').html()
                + $(this).parent().parent().find('td:eq(7)').html()
        );

        // remove last 3 TD
        $(this).parent().parent().find('td:eq(7)').remove();
        $(this).parent().parent().find('td:eq(6)').remove();

        // make links (now they in pop-up sub-menu) visible
        $('.items td a').show();

    });

    // 2.3) setup sub-menu switcher behaviour
    $('.action-switch-menu').click(function(){

        var isVisible = $(this).next().is(":visible");

        // click must close all other open "small-menu"
        $('tr.selected-sk .invites-smallmenu-item').hide();
        $('tr.selected-sk').removeClass('selected-sk');

        // after removeClass('selected-sk') and hide(), function this.toggle() will always shown element,
        // so I store menu state before I hide all menus and use isVisible to determine show or hide current menu
        if (isVisible) {
            var a = $(this).next().hide();
            $(this).parent().parent().removeClass('selected-sk');
        } else {
            var a = $(this).next().toggle();
            $(this).parent().parent().toggleClass('selected-sk');
        }
    });

    // 2.4) если кликнуть не по ссылке а по фону меню --оно закроется
    $('.table-menu').click(function(){
        $(this).hide();
        $(this).parent().parent().removeClass('selected-sk');
    });

    // 2.5) если кликнуть где угодно в документе -- меню закроется
    $(document).click(function(e) {
        if(!$(e.target).is('.table-menu-switcher')) {
            var visibleElement = $(".table-menu:visible");
            visibleElement.parents("tr").removeClass('selected-sk');
            visibleElement.hide();
        }
    });

    // 3) switch assessment results render type
    $(".action-switch-assessment-results-render-type").click(function() {
        $.post("/dashboard/switchAssessmentResultsRenderType").done(function() {
            location.reload();
        })
    });

    // 4) попап перед стартом лайт симуляции в кабинетах
    $('.action-open-lite-simulation-popup').click(function(event) {

        // get URL for lite simulation
        var href = $(this).attr('data-href');

        // popup-before-start-sim lite-simulation-info-dialog
        $(".locator-lite-simulation-info-popup").dialog({
            closeOnEscape: true,
            dialogClass: 'background-sky popup-information',
            minHeight: 220,
            modal: true,
            resizable: false,
            width: getDialogWindowWidth(),
            draggable: false,
            open: function( event, ui ) {
                $('.action-start-lite-simulation-now').click(function() {
                    location.assign(href);
                });
            }
        });
        return false;
    });

    // 5) перемещаем .pager в нужное место
    $('.pager-place').html($('.grid-view .pager').html());
    $('.grid-view .pager').html('');

    // 6)
    $('.action-open-full-simulation-popup').click(function(event){

        // @todo: change to locator-invite-accept-form
        $('#invite-accept-form').dialog('close');

        var href = $(this).attr('data-href');
        event.preventDefault();
        // удлиннить окно чтоб футер был ниже нижнего края попапа
        $('.content').css('margin-bottom', '80px');

        // преверяем есть ли не завершенная фулл симуляцая самому-себе
        $.ajax({
            url: '/simulationIsStarted',
            dataType:  "json",
            data: {
                invite_id: getInviteId(href)
            },
            success:function(data) {
                var dataGlobal = data;
                if (0 < parseInt(data.count_self_to_self_invites_in_progress)) {
                    // незавершенные симуляции есть
                    // popup-before-start-sim
                    var warningPopup = $(".locator-exists-self-to-self-simulation-warning-popup");
                    warningPopup.dialog({
                        closeOnEscape: true,
                        dialogClass: 'background-sky popup-information',
                        minHeight: 220,
                        modal: true,
                        resizable: false,
                        width: getDialogWindowWidth(),
                        open: function( event, ui ) {
                            // пользователь выбирает не прерывать текущую симуляцию
                            $('.action-close-popup').click(function(){
                                warningPopup.dialog('close');
                            });

                            // пользователь выбирает начать новую симуляцию,
                            // не смотря на наличие незавершенных
                            $('.action-start-full-simulation').click(function(){
                                // закрыть текущий попап
                                warningPopup.dialog('close');

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

    // 7)
    var pre_simulation_popup = $(".locator-full-simulation-info-popup");

    // popup-before-start-sim
    pre_simulation_popup.dialog({
        closeOnEscape: true,
        autoOpen : false,
        dialogClass: 'background-sky popup-information',
        minHeight: 220,
        modal: true,
        resizable: false,
        width: getDialogWindowWidth()
    });
    console.log('pre_simulation_popup: ', pre_simulation_popup);

    function infoPopup_aboutFullSimulation(href) {
        console.log('infoPopup_aboutFullSimulation', pre_simulation_popup);
        pre_simulation_popup.dialog('open');
        $('.locator-next-step').attr('data-href', href);
    }

    // 8)
    function displaySimulationInfoPopUp (href, data) {
        // в случае если пользователь выбрал начать новую симуляцию,
        // не смотря на наличие незавершенных сам-себе
        // то предупреждение будет дублирующим,
        // а если он пытает начать фулл симуляцию по приглашению от работодателя второй раз,
        // то предупреждение нужно
        console.log(data);
        console.log(data.user_try_start_simulation_twice);
        console.log(0 == parseInt(data.count_self_to_self_invites_in_progress));
        if(data.user_try_start_simulation_twice &&
            0 == parseInt(data.count_self_to_self_invites_in_progress)) {
            // предупреждение о попытке повторного начала симуляции
            console.log(".pre-start-popup");
            $(".pre-start-popup").dialog({
                closeOnEscape: true,
                dialogClass: 'popup-before-start-sim',
                minHeight: 220,
                modal: true,
                resizable: false,
                width: getDialogWindowWidth(),
                open: function( event, ui ) {
                    $('.start-full-simulation-next').attr('data-href', href);
                }
            });
        } else {
            // информация про ключевые моменты в сценарии фулл симуляции
            // что я? где я? сотрудники, цели.
            infoPopup_aboutFullSimulation(href);
        }
    }

    // 9)
    $('.action-start-full-simulation-now').click(function(event){
        var href = $(this).attr('data-href');
        location.assign(href);
    });

    // 10)
    $('.action-add-vacancy').click(function(event) {
        event.preventDefault();
        $(".locator-form-vacancy").dialog({
            dialogClass: 'background-image-lamp popup-form',
            closeOnEscape: true,
            minHeight: 350,
            modal: true,
            resizable: false,
            draggable: false,
            title: '',
            width: getDashboardDialogWindowWidth(),
            position: {
                my: "left top",
                at: "left top",
                of: $('.locator-corporate-invitations-list-box')
            },
            open: function () {
                // This is fix render for IE10
                // z-index правильный, но затемнение отрысовывается над попапом!
                // $(window).trigger('resize');
            }
        });

        $(".form-vacancy").dialog('open');
    });

    // 11)
    window.addVacancyValidation = function addVacancyValidation(form, data, hasError) {
        console.log(form, data, hasError);
        if (!hasError) {
            window.location.href = form.attr('data-url');
        }
        return false;
    };

    // 12) показать попап второго шага Отправки приглашения
    if ($( ".message_window" )) {

        $( ".message_window" ).dialog({
            dialogClass: 'background-image-book',
            modal: true,
            resizable: false,
            draggable: false,
            width: getDashboardDialogWindowWidth(),
            height: 500,
            position: {
                my: "left top",
                at: "left top",
                of: $('.locator-corporate-invitations-list-box')
            }
        });

        // $( ".message_window").parent().addClass('nice-border cabmessage');
        $( ".message_window").dialog('open', $("#corporate-invitations-list-box").show());
    }

});





