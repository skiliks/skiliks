
$(document).ready(function(){
    $(".question-container>li").click(function() {
        if(!$(this).children("div").is(":visible")) {
            $(this).children("div").slideDown("fast");
            $(this).css('color', '#146672');
            $(this).addClass("active");
        }
        else {
            $(this).children("div").slideUp("fast");
            $(this).css('color', '#555742');
            $(this).removeClass("active");
        }
    })
});