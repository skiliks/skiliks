
$(document).ready(function () {

    // 1) сокрытие двух последних колонок с ссылками-действиями: "удалить", т.п.
    $('.items').find('th:eq(7)').remove();
    $('.items').find('th:eq(6)').remove();

    // 2) меню с шестерёнкой

    // 2.1.) append pop-up sub-menu {
    if ($('body').hasClass('action-controller-corporate-static-dashboard')) {
        if (2 < $('.items tr').length || '' != $('.items tr:eq(1) td:eq(3)').text()) { //fix for empty list
            $('.items tr').each(function(){
                $(this).find('td:eq(0)').html(
                    '<span class="table-menu-switcher inter-active action-switch-menu"></span>' +
                        '<div class="table-menu locator-table-menu action-close-table-menu" >' +
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
    }

    // 2.3) setup sub-menu switcher behaviour
    $('.action-switch-menu').click(function(){

        var isVisible = $(this).next().is(":visible");

        // click must close all other open "small-menu"
        $('.locator-table-menu').hide();
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
    $('.action-close-table-menu').click(function(){
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

    // 5) перемещаем .pager в нужное место
    $('.locator-pager-place').html($('.locator-contrast-table .pager').html());
    $('.locator-contrast-table .pager').html('');

    // 6) initActionOpenFullSimulationPopUp
    function initActionOpenFullSimulationPopUp() {
        $('.action-open-full-simulation-popup').click(function(event){

            $('.locator-invite-accept-popup').dialog('close');

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
                        initWarningAboutSimulationInProgress(href, dataGlobal);
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
    }

    initActionOpenFullSimulationPopUp();

    function initWarningAboutSimulationInProgress(href, dataGlobal) {
        var href = href;
        var dataGlobal = dataGlobal;
        var warningPopup = $(".locator-exists-simulation-in-progress-warning-popup");
        warningPopup.dialog({
            closeOnEscape: true,
            dialogClass: 'background-sky popup-information',
            minHeight: 220,
            modal: true,
            resizable: false,
            width: getDialogWindowWidth(),
            open: function( event, ui ) {
                // пользователь выбирает не прерывать текущую симуляцию
                $('.action-close-popup').click(function() {
                    warningPopup.dialog('close');
                });

                // пользователь выбирает начать новую симуляцию,
                // не смотря на наличие незавершенных
                $('.action-start-full-simulation').click(function(){
                    // закрыть текущий попап
                    warningPopup.dialog('close');

                    // запрос на удаление всех незавершенных фулл симуляций самому-себе
                    $.ajax({ url: '/static/break-simulations-for-self-to-self-invites'});

                    // отображаем вступительный попап {

                    // закрываем предупреждение
                    warningPopup.dialog('close');

                    // переключаем флаг-предупреждения
                    // теперь должно быдет открыться окно с информацией об игре
                    dataGlobal.user_try_start_simulation_twice = false;

                    displaySimulationInfoPopUp(href, dataGlobal);
                    // отображаем вступительный попап }
                });
            }
        });
    }

    // 7) popup-before-start-sim
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

    function infoPopup_aboutFullSimulation(href) {
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
        if(data.user_try_start_simulation_twice &&
            0 == parseInt(data.count_self_to_self_invites_in_progress)) {
            // предупреждение о попытке повторного начала симуляции
            initWarningAboutSimulationInProgress(href, data);
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
            minHeight: 300,
            modal: true,
            resizable: false,
            draggable: false,
            extraCloseClass: 'close-form-add-vacancy',
            title: '',
            width: getDialogWindowWidth_2of3(),
            position: {
                use: true,
                my: "left top",
                at: "left top",
                of: $('.locator-corporate-invitations-list-box')
            }
        });

        // смена ширины при изменении размеров окна браузера
        // выравнивание при изменении размеров окна браузера
        $(window).on('resize', function() {
            $('.locator-form-vacancy').dialog("option", "width", getDialogWindowWidth_2of3());
            $('.locator-form-vacancy').dialog("option", "position", {
                use: true,
                my: "right top",
                at: "right top",
                of: $('.locator-corporate-invitations-list-box')
            });
        });

        $(".form-vacancy").dialog('open');
    });

    // 11) переместил в common.js

    // 12) показать попап второго шага Отправки приглашения
    if ($(".locator-form-invite-step-2")) {

        $(".locator-form-invite-step-2").dialog({
            dialogClass: 'background-image-book popup-form',
            modal: true,
            resizable: false,
            draggable: false,
            width: getDialogWindowWidth_2of3(),
            height: 530,
            position: {
                use: true,
                my: "right top",
                at: "right top",
                of: $('.locator-corporate-invitations-list-box')
            }
        });

        // смена ширины при изменении размеров окна браузера
        // выравнивание при изменении размеров окна браузера
        $(window).on('resize', function() {
            var newWidth = getDialogWindowWidth_2of3();
            $('.locator-form-invite-step-2').dialog("option", "width", newWidth);
            $('.locator-form-invite-step-2').dialog("option", "position", {
                use: true,
                my: "right top",
                at: "right top",
                of: $('.locator-corporate-invitations-list-box')
            });

            // в разных браузерах  в getDialogWindowWidth_2of3()
            // и @media screen and (max-width: 1279px)
            // момент смены размера разный
            // - тут это просвляется для input и textarea
            // потому что им нельзя задать размен в %
            $('#Invite_fullname').width(newWidth - 100);
            $('Invite_message').width(newWidth - 100);
        });

        $(window).resize();

        $( ".locator-form-invite-step-2").dialog('open');
    }

    // 13)
    $('.action-accept-invite').click(function(e) {

        // У пользователя. вероятно будет несколько непринятых приглашений
        // а попап у нас один.
        // Чтоб передать в попап данные какое именно приглашение пользователь принимает,
        // используем переменную buttonAccept - это ссылка на кнопку
        // у кнопки уже имеются атрибуты data-link-start-now и data-link-start-later
        var buttonAccept = $(this);

        // accept-invite-warning-popup full-simulation-info-popup margin-top-popup
        $('.locator-invite-accept-popup').dialog({
            dialogClass: 'popup-information background-sky locator-invite-accept-popup',
            modal:       true,
            autoOpen:    true,
            resizable:   false,
            draggable:   false,
            width:       getDialogWindowWidth(),
            height:   950,
            position: {
                my: "left top",
                at: "left top",
                of: $(".action-feedback")
            },
            open: function( event, ui ) {
                $(this).find('.locator-start-later').attr(
                    'href',
                    buttonAccept.attr('data-link-start-later')
                );

                $(this).find('.locator-start-now').attr(
                    'data-href',
                    buttonAccept.attr('data-link-start-now')
                );
            },
            close: function() {
                // странный баг - только для этого, длинного, окна
                // каждый раз открывается на одно окно больше: 1,2,3 ...
                $('.locator-invite-accept-popup').dialog("destroy");
            }
        });

        // выходящий за размеры окна попап создаёт пустоту равную его высоте под футером
        // + липнет к верхнему краю окна и не реагирует на top (если position: relative)
        // если position: absolute - то с футером всё ок и попапом можно управлять
        $('.locator-invite-accept-popup').css('margin-top', '50px');

        // hack }

        return false;
    });

    // 15 )
    $(".action-display-assessment-results-type-hint").hover(
        function() {
            setTimeout(function() {
                if($(".locator-assessment-results-type-switcher" + ":hover").length > 0) {
                    // есть баг:
                    // или я не могу найти какой код применяет .hide() к этому элементу
                    // или это браузер делает свою оптимизацию и назначает style="display: none;"
                    $(".locator-hint-assessment-results-type-switcher").removeAttr('style');
                    $(".locator-hint-assessment-results-type-switcher").removeClass("hide");
                }
            }, 2000);
        },
        function() {
            $(".locator-hint-assessment-results-type-switcher").addClass("hide");
        }
    );

    // 16) В выпадающем списке вакансий выбирает ту - которая была только что додавлена
    // в контроллере где происходит добавлени вакансии провтавляется куки 'recently_added_vacancy_id'
    if (null !== $.cookie('recently_added_vacancy_id')) {
        $('#Invite_vacancy_id option[value=' + $.cookie('recently_added_vacancy_id') + ']').attr('selected', 'selected');
        $.cookie('recently_added_vacancy_id', null);
    }
});





