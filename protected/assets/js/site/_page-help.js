
$(document).ready(function() {
    $(".action-toggle-help-paragraph").click(function() {
        if (!$(this).children("div").is(":visible")) {
            $(this).children("div").slideDown("fast", stickyFooterAndBackground());
            $(this).css('color', '#146672');
            $(this).addClass("active");
        } else {
            $(this).children("div").slideUp("fast", stickyFooterAndBackground());
            $(this).css('color', '#555742');
            $(this).removeClass("active");
        }
    })
});