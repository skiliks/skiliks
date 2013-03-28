$(document).ready(function () {
    "use strict";
    $('a.feedback').click(function () {
        $('.feedback_dialog').dialog({ width: 600});
        return false;
    });
    $('.dashboard').append($('<div id="simulation-details-pop-up"></div>'));
    var simulation_popup = $('#simulation-details-pop-up');
    simulation_popup.dialog({
        modal:     true,
        width:     940,
        minHeight: 600,
        autoOpen: false
    });

    $(".view-simulation-details-pop-up").click(function (event) {
        event.preventDefault();
        $.ajax({
            url:     $(this).attr('data-simulation'),
            success: function (data) {

                simulation_popup.html(data);
                simulation_popup.dialog('open');
            }
        });
    });
});