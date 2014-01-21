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

    function hideAllPopovers() {
        $(".action-display-popover").removeClass("active");
        $(".inner-popover").removeClass("active");
        $(".inner-popover").hide();
    }
    // action-display-popover }
});