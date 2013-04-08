$(document).ready(function () {
    "use strict";

    $('.dashboard').append($('<div id="simulation-details-pop-up"></div>'));
    var simulation_popup = $('#simulation-details-pop-up');
    simulation_popup.dialog({
        modal:     true,
        width:     940,
        minHeight: 600,
        autoOpen: false
    });

    $(".view-simulation-details-pop-up").click(function (event) {
        event.preventDefault();
        $.ajax({
            dialogClass: 'simulation-result-popup',
            url:     $(this).attr('data-simulation'),
            success: function (data) {
                simulation_popup.html(data);
                simulation_popup.dialog('open');
            }
        });
    });

    $("#registration_check").click(function () {
        if($(this).hasClass('icon-check')) {
            $(this).removeClass('icon-check');
            $(this).addClass('icon-chooce');
            $('#YumUser_is_check').val('0');
            $("#registration_check span").css('display', 'block');
            if(1 === $('#registration_switch').length){
                $('#registration_switch').val($('#registration_switch').attr('data-next'));
            }
        }else if($(this).hasClass('icon-chooce')){
            $(this).removeClass('icon-chooce');
            $(this).addClass('icon-check');
            $('#YumUser_is_check').val('1');
            $("#registration_check span").css('display', 'none');
            if(1 === $('#registration_switch').length){
                $('#registration_switch').val($('#registration_switch').attr('data-start'));
            }
        }
        return false;
    });

});