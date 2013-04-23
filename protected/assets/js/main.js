/*global $, console, jQuery, Cufon */
(function ($) {
    "use strict";
    $(document).ready(function () {

        // fixSimResultsDialog {
        var fixSimResultsDialog = function () {
            var heightOverhead = 300;
            $('div.content').height($('.simulation-result-popup').height() - heightOverhead + 'px');
            $('.simulation-result-popup').css('top', '50px');

            $('.ui-widget-overlay').height($('.simulation-result-popup').height() + 150 + 'px');
            if ($('.ui-widget-overlay').height() < $(document).height()) {
                $('.ui-widget-overlay').height($(document).height() + 'px');
            }
        };
        // fixSimResultsDialog  }

        // render simulation details pop-up {
        $('.dashboard').append($('<div id="simulation-details-pop-up"></div>'));
        var simulation_popup = $('#simulation-details-pop-up');

        simulation_popup.dialog({
            dialogClass: 'simulation-result-popup',
            modal:       true,
            width:       940,
            minHeight:   600,
            autoOpen:    false,
            resizable:   false,
            open:        fixSimResultsDialog
        });
        // render simulation details pop-up }

        window.showSimulationDetails = function (url) {
            $.ajax({
                url:     url,
                success: function (data) {
                    simulation_popup.html(data);
                    simulation_popup.dialog('open');

                    // fixSimResultsDialog {
                    $('.simulation-details .estmfooter a').click(function () {
                        fixSimResultsDialog();
                    });
                    $('.simulation-details .navigation a').click(function () {
                        fixSimResultsDialog();
                    });
                    // fixSimResultsDialog }
                }
            });
        };

        // load simulation details pop-up data {
        $(".view-simulation-details-pop-up").click(function (event) {
            event.preventDefault();
            window.showSimulationDetails($(this).attr('data-simulation'));
        });
        // load simulation details pop-up data }

        $("#registration_check").click(function () {
            if ($(this).hasClass('icon-check')) {
                $(this).removeClass('icon-check');
                $(this).addClass('icon-chooce');
                $('#YumUser_is_check').val('0');
                $("#registration_check span").css('display', 'block');
                if (1 === $('#registration_switch').length) {
                    $('#registration_switch').val($('#registration_switch').attr('data-next'));
                }
            } else if ($(this).hasClass('icon-chooce')) {
                $(this).removeClass('icon-chooce');
                $(this).addClass('icon-check');
                $('#YumUser_is_check').val('1');
                $("#registration_check span").css('display', 'none');
                if (1 === $('#registration_switch').length) {
                    $('#registration_switch').val($('#registration_switch').attr('data-start'));
                }
            }
            return false;
        });

        // product page, test results - sub list hide/show switcher
        $('.hassubmenu a.sub-menu-switcher').click(function () {
            if ($(this).parent().hasClass('subisopen')) {
                $(this).parent().removeClass('subisopen');
            } else {
                $(this).parent().addClass('subisopen');
            }
        });
        $('.feedback').on('click', function (e) {
            $('#feedback-dialog').dialog({
                'width': 700,
                dialogClass: 'feedbackwrap',
                title: 'Пожалуйста, расскажите нам, что мы можем сделать лучше, мы ценим ваше мнение',
                modal: true,
                resizable: false
            });
            e.stopPropagation();
        });
    });

    window.passwordRecoverySubmit = function passwordRecoverySubmit(form, data, hasError) {
        if (!hasError) {
            $.post(form.attr('action'), form.serialize(), function (res) {
                // Do stuff with your response data!
                location.reload();
            });
        }
        return false;
    };


    function authenticateValidation(form, data, hasError) {
        if (!hasError) {
            $.post(form.attr('action'), form.serialize(), function (res) {
                // Do stuff with your response data!
                //location.href = '/';
            });
        }
        return false;
    }
})(jQuery);

$(window).load(function(){
    // pop-up to inform corporate user, that trial full simulation cost 1 invite {
    $('a.invite-for-trial-full-scenario').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        console.log('TRIAL!');
        $('#start-trial-full-scenario-pop-up').dialog({
            dialogClass: 'nice-border flash-pop-up',
            modal: true,
            width: 400
        });
    });

    $('a.start-trial-full-scenario-disagree').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        $('#start-trial-full-scenario-pop-up').dialog("close");
    });

    $('a.start-trial-full-scenario-agree').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        console.log($('a.invite-for-trial-full-scenario').attr('href'));
        window.location.replace($('a.invite-for-trial-full-scenario').attr('href'));
    });
    // pop-up to inform corporate user, that trial full simulation cost 1 invite }
});

Cufon.replace('.freeacess');
Cufon.replace('.light-btn, .invite-people-form input[type="submit"], .brightblock, .lightblock, .benefits, .tarifname, ' +
    '.clients h3, .main-article article h3, #simulation-details label, .features h2, .thetitle, .tarifswrap .text16, ' +
    '.form-submit-button, .midtitle, .flash-success, .social_networks span, .main-article h3, .registration input[type=submit], ' +
    '.registration .form h1, .registration .form li, .note, .product h2, .product section h3, .product section table td h6, .team article h2, ' +
    '.team .team-list li h4, .team .team-values h3, .registration h2, .registration .form h1, .widthblock h3, .ratepercnt'

);
Cufon.replace('.main-article article ul li, .container>header nav a, .features ul li, .sbHolder a, #simulation-details label, .container>header nav a, .features .error span, ' +
    '.features p.success, .product hgroup h6, .productfeatrs td, .product table p, .product section table th, .product section h3, ' +
    '.product section table th, .product section th h5, .product .sub-menu-switcher, .productsubmenu a, .team .team-list li p, .team .team-values ul li, .team article p, ' +
    '.footer nav a, .backtotop a, .price p '
    , {fontFamily:"ProximaNova-Regular"});
