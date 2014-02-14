
$(document).ready(function () {

    // 1) пароль
    $('.action-toggle-show-password').click(function() {
        if ('text' == $('#YumUser_password').attr('type')) {
            $('#YumUser_password').attr('type', 'password');
            $('#YumUser_password_again').attr('type', 'password');
            $(this).removeClass('action-show-password');
            $(this).addClass('action-hide-password');
            $(this).text('Показать пароль');
        } else {
            $('#YumUser_password').attr('type', 'text');
            $('#YumUser_password_again').attr('type', 'text');
            $(this).addClass('action-show-password');
            $(this).removeClass('action-hide-password');
            $(this).text('Скрыть пароль');
        }

    });
});





