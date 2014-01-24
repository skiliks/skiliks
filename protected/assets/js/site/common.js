$(document).ready(function () {
    // 1) отображает подстказку справа от элемента "action-display-popover" на странице
    $(".action-display-popover").click(function() {

        hideAllPopovers();

        // закрыть уже открытые status-tooltips
        $(".action-display-popover").removeClass("active");

        $(this).addClass("active");

        var popover =  $(this).find('.inner-popover');
        popover.addClass("active");

        // да - надо и класс убрать и show() вызвать
        popover.removeClass("hide");
        popover.show();

        // значение "-2" найдено экспериментально
        popover.css("margin-top", - popover.height()/2 - 8);

        if($(window).width() < 1140) {
            popover.css("margin-left", -52);
        } else {
            popover.css("margin-left", 0);
        }

        $(this).find('.inner-popover-triangle').css("top", (popover.height() - 16)/2);
    });

    $(document).click(function(e) {
        if(!$(e.target).is('.action-display-popover')) {
            hideAllPopovers();
        }
    });
    // action-display-popover }

    // 2) Стализация выпадающих списков
    // @link http://www.bulgaria-web-developers.com/projects/javascript/selectbox/
    $("select").selectbox();

    // 3) feedback
    $('.action-feedback').on('click', function (e) {
        console.log('action feedback',$('.locator-feedback-dialog'));
        var selected = $(this).attr('data-selected');
        $('.locator-feedback-dialog').dialog({
            width: getDashboardDialogWindowWidth(),
            height: 400,
            dialogClass: 'popup-form background-image-two-lamps',
            modal: true,
            resizable: false,
            draggable: false,
            open: function( event, ui ) {
                if(selected !== undefined) {
//                    $('#feedback-form').find('.sbOptions').find('li').each(function(index, element){
//                        var a = $(element).find('a');
//                        if(a.attr('rel') === selected){
//                            a.click();
//                        }
//                    });
                }
            }
        });
    });
});

// 1)
function hideAllPopovers() {
    $(".action-display-popover").removeClass("active");
    $(".inner-popover").removeClass("active");
    $(".inner-popover").hide();
}


/**
 * 2) Определяет ширину попапа не весь экран
 * @param number padding, если у попапа нестандартные отступы,
 * то и ширину надо делать меньше/больше
 *
 * @returns {number}
 */
function getDialogWindowWidth(padding) {
    if (undefined == padding) {
        padding = 0;
    };

    if ($(document).width() < 1281) {
        return  940;
    } else {
        return  1115 - padding;
    }
}

// 2.1) Определяет ширину
function getDashboardDialogWindowWidth() {
    if ($(document).width() < 1281) {
        return 576;
    } else {
        return 719;
    }
}

// 3)
function getInviteId(url){
    return parseInt(url.replace('/simulation/promo/full/', ''), 0);
}

// 4) Feedback Submit AJAX validation
window.feedbackSubmit = function feedbackSubmit(form, data, hasError) {
    if (!hasError) {
        $.post(form.attr('action'), form.serialize(), function (res) {
            // Do stuff with your response data!
            location.reload();
        });
    }
    return false;
};
