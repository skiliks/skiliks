
$(document).ready(function() {
    $(".action-toggle-help-paragraph").click(function() {
        console.log('0');
        if (!$(this).children("div").is(":visible")) {
            $(this).children("div").slideDown("fast");
            $(this).css('color', '#146672');
            $(this).addClass("active");
        } else {
            $(this).children("div").slideUp("fast");
            $(this).css('color', '#555742');
            $(this).removeClass("active");
        }

        console.log('1');
        stickyFooterAndBackground();
        console.log('2');
    })
});