$(document).ready(function () {

    // 1)
    $('.action-show-add-vacancy-form').click(function(){
        if ($('.locator-add-vacancy-form').hasClass('hide')) {
            $('.locator-add-vacancy-form').removeClass('hide');
            $('.unstandard-content-box-height').css('height', '500px')
        } else {
            $('.locator-add-vacancy-form').addClass('hide');
        }
    });

    // 2)
    $('.action-show-status').click(function(){
        var statusData = $(this).attr('data-status');

        $('.locator-for-ui-dialog-status-data').dialog({
            autoOpen: true,
            closeOnEscape: true,
            dialogClass: "background-sky popup-form pull-content-center",
            minHeight: 50,
            modal: true,
            resizable: false,
            width: getDialogWindowWidth_2of3(),
            position: {
                my: 'center top',
                at: 'center bottom',
                of: $('header.main-content')
            },
            open: function() {
                $('.locator-for-ui-dialog-status-data').text(statusData);
                $('.locator-flash').removeClass('hide');
            }
        });
    });
});