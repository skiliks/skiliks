/**
 * Данный код применяется и на сайте и в админке
 */

$(document).ready(function () {

    var simulation_popup = $('.locator-simulation-details-popup');

    simulation_popup.dialog({
        dialogClass: 'background-sky-blue simulation-result-popup',
        modal:       true,
        width:       getDialogSimulationDetailsPopupWidth(),
        height:      935,
        autoOpen:    false,
        resizable:   false
    });

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
});