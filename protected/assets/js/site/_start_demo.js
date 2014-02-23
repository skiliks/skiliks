

$(document).ready(function(){
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

    // смена ширины при изменении размеров окна браузера
    // выравнивание при изменении размеров окна браузера
    $(window).on('resize', function() {
        $('.locator-lite-simulation-info-popup').dialog("option", "width", getDialogWindowWidth());
        $('.locator-lite-simulation-info-popup').dialog("option", "position", "center");
    });
});