

$(document).ready(function(){
    $('.action-show-terms-pop-up').click(function() {
        // запрашивает лицензионное соглашение
        $.ajax('/static/terms', {
            success: function(data) {
                var dHeight = $(window).height() * 0.85;

                $('body').append($('<div id="terms-pop-up"></div>'));

                $('#terms-pop-up').dialog({
                    dialogClass: 'background-white popup-form',
                    modal:       true,
                    width:       getDialogWindowWidth(),
                    height:      dHeight,
                    autoOpen:    false,
                    resizable:   false,
                    draggable: false,
                    open: function() {
                        // блокируем скролл страницы
                        $("html").css("overflow-y", "hidden");

                        // блокируем скролл страницы: "body" + resize() нуны для safari
                        $("body").css("overflow-y", "hidden");
                        $(window).resize();
                    },
                    close: function () {
                        // блокируем скролл страницы
                        $("html").css("overflow-y", "visible");

                        // блокируем скролл страницы: "body" + resize() нуны для safari
                        $("body").css("overflow-y", "visible");
                        $(window).resize();
                    }
                });

                // смена ширины при изменении размеров окна браузера
                // смена высоты при изменении размеров окна браузера
                // центрирование при изменении размеров окна браузера
                $(window).on('resize', function() {
                    var dHeight = $(window).height() * 0.85;
                    $('#terms-pop-up').dialog("option", "height", dHeight);
                    $('#terms-pop-up').dialog("option", "width", getDialogWindowWidth());
                    $('#terms-pop-up').dialog("option", "position", "center");
                });

                $('#terms-pop-up').html(data).dialog('open');

                // $('#terms-pop-up').css("min-height","374px");

                $("#terms-pop-up").scrollTop($("#terms-pop-up h1.total").scrollTop());

                // по сути костыль: при высоте окна браузера 500рх и меньше - попап залазит выше верхнего края окна
                // но после пересчёта $(window).resize() всё ок.
                // может контент недогржается и высота неправильно измеряется?
                $(window).resize();
            }
        });
        return false;
    });
});