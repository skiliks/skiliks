/**
 * Данный код применяется и на сайте и в админке
 */

$(document).ready(function () {

    var simulation_popup = $('.locator-simulation-details-popup');

    if ('undefined' == typeof simulation_popup.html() ) {
        /* из locator-simulation-details-popup создаётся потом окно ui-dialog */
        $('body').append('<div class="locator-simulation-details-popup overflow-hidden"></div>');
        simulation_popup = $('.locator-simulation-details-popup');
    }

    simulation_popup.dialog({
        dialogClass: 'background-sky-blue simulation-result-popup',
        modal:       true,
        width:       getDialogSimulationDetailsPopupWidth(),
        height:      935,
        autoOpen:    false,
        resizable:   false
    });

    $(window).resize(function(){
        $('.locator-simulation-details-popup').dialog("option", "width", getDialogSimulationDetailsPopupWidth());
        $('.locator-simulation-details-popup').dialog("option", "position", "center");
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
            }
        });
    };

    $(".action-show-simulation-details-popup").click(function (event) {
        event.preventDefault();
        window.showSimulationDetails($(this).attr('data-simulation'));
    });

    if (!isNaN(parseFloat(window.display_results_for)) && isFinite(window.display_results_for)) {
        showSimulationDetails('/simulation/' + window.display_results_for + '/details');
    }
});