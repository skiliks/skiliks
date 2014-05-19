/**
 * Данный код применяется и на сайте и в админке
 */

$(document).ready(function () {
    // 1) добавить HTML для попапа с результатами симуляции
    var simulation_popup = $('.locator-simulation-details-popup');

    if ('undefined' == typeof simulation_popup.html() ) {
        /* из locator-simulation-details-popup создаётся потом окно ui-dialog */
        $('body').append('<div class="locator-simulation-details-popup overflow-hidden"></div>');
        simulation_popup = $('.locator-simulation-details-popup');
    }

    // 2) инициализация диалога
    simulation_popup.dialog({
        dialogClass: 'background-sky-blue simulation-result-popup',
        modal:       true,
        width:       getDialogSimulationDetailsPopupWidth(),
        height:      getDialogSimulationDetailsPopupHeight(),
        autoOpen:    false,
        resizable:   false,
        open: function() {
            stickyFooterAndBackground();
        }
    });

    $(window).resize(function(){
        $('.locator-simulation-details-popup').dialog("option", "width", getDialogSimulationDetailsPopupWidth());
        $('.locator-simulation-details-popup').dialog("option", "height", getDialogSimulationDetailsPopupHeight());
        $('.locator-simulation-details-popup').dialog("option", "position", "center");

        winHeigth = $(window).height();
        dialogHeight = $('.simulation-result-popup').height();
        if (winHeigth < dialogHeight + 110) {
            $('.simulation-result-popup').css('top', '50px');
        }

        $('body').scrollTop(0);
    })

    /**
     * @param string url, '/simulation/ХХХ/details'
     */
    window.showSimulationDetails = function (url) {
        $.ajax({
            url:     url,
            success: function (data) {
                simulation_popup.html(data);
                simulation_popup.dialog('open');

                $(window).resize();
                $(window).scrollTop('body');
            }
        });
    };

    // 3) Реакция на клик по рейтингу (звёздами или процентилю)
    $(".action-show-simulation-details-popup").click(function (event) {
        event.preventDefault();
        window.showSimulationDetails($(this).attr('data-simulation'));
    });

    // 4) открытие окна с результатами автоматически
    // (происходит после завершения симуляции)
    if (!isNaN(parseFloat(window.display_results_for)) && isFinite(window.display_results_for)) {
        showSimulationDetails('/simulation/' + window.display_results_for + '/details');
    }
});