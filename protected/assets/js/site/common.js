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
});

// 1)
function hideAllPopovers() {
    $(".action-display-popover").removeClass("active");
    $(".inner-popover").removeClass("active");
    $(".inner-popover").hide();
}


// 2) Определяет ширину
function getDialogWindowWidth() {
    if ($(document).width() < 1281) {
        return  940;
    } else {
        return  1146;
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


