$(document).ready(function () {

    // 1)
    $('.action-show-add-vacancy-form').click(function(){
        if ($('.locator-add-vacancy-form').hasClass('hide')) {
            $('.locator-add-vacancy-form').removeClass('hide');

            // к блоку контента применено overflow:hidden
            // но из-за него не будет видно выыпадающего списка
            $('.locator-content-box').removeClass('overflow-hidden');

            // 1.1 {
            // если убираем overflow:hidden
            // то надо задавать высоту вручную
            $('.locator-content-box').css(
                'height',
                $('.locator-profile-right-side').height() + 24 + 'px'
            );
            // 1.1 }

        } else {
            // возвращаем удалённые классы
            $('.locator-add-vacancy-form').addClass('hide');
            $('.locator-content-box').addClass('overflow-hidden');
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

                stickyFooterAndBackground();
            }
        });
    });
});