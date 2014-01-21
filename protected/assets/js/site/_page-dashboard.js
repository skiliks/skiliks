
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
    $('.action-start-lite-simulation').click(function(event) {
        event.preventDefault('.action-start-lite-simulation');
        console.log(".start-lite-simulation-btn");
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
                $('.start-lite-simulation-now').click(function() {
                    location.assign(href);
                });
            }
        });
        return false;
    });

    // 5) перемещаем .pager в нужное место
    $('.pager-place').html($('.grid-view .pager').html());
    $('.grid-view .pager').html('');

});