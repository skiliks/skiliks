$(document).ready(function () {
    "use strict";

    // fixSimResultsDialog {
    var fixSimResultsDialog = function() {
        console.log('fix', $('.simulation-result-popup').height());
        var heightOverhead = 300;
        $('div.content').height($('.simulation-result-popup').height() - heightOverhead + 'px');
        $('.simulation-result-popup').css('top', '50px');
        $('.ui-widget-overlay').height( $('.simulation-result-popup').height() + 150  + 'px');
    }
    // fixSimResultsDialog  }

    // render simulation details pop-up {
    $('.dashboard').append($('<div id="simulation-details-pop-up"></div>'));
    var simulation_popup = $('#simulation-details-pop-up');

    simulation_popup.dialog({
        dialogClass: 'simulation-result-popup',
        modal:     true,
        width:     940,
        minHeight: 600,
        autoOpen: false,
        resizable: false,
        open: fixSimResultsDialog
    });
    // render simulation details pop-up }

    // load simulation details pop-up data {
    $(".view-simulation-details-pop-up").click(function (event) {
        event.preventDefault();
        $.ajax({
            url: $(this).attr('data-simulation'),
            success: function (data) {
                simulation_popup.html(data);
                simulation_popup.dialog('open');

                // fixSimResultsDialog {
                $('.simulation-details .estmfooter a').click(function(){
                    fixSimResultsDialog();
                });
                $('.simulation-details .navigation a').click(function(){
                    fixSimResultsDialog();
                });
                // fixSimResultsDialog }
            }
        });
    });
    // load simulation details pop-up data }

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