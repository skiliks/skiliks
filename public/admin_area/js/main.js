/* global $ */
var prefix = '/admin_area/statistics/testAuth?params=';
function ci_call(url, result) {
    $.ajax({
        url:prefix + url,
        success: result
    });

}
function update_stat(selector, path) {
    selector = '.table-statistics '+selector;
    $.ajax({
        url:path,
        success: function(data){
            console.log(data)
            $(selector).find('.status').html(data.data);
            $(selector).removeClass('success');
            $(selector).removeClass('failure');
            $(selector).addClass(data.status);
        },
        dataType:  "json"
    });
}
function update_tc(selector, xml){
    selector = '.table-statistics '+selector;
    ci_call(xml, function (data) {
        var first_failure;
        var build = $(data).find('build:eq(0)');
        if ((build.attr('status') == 'FAILURE') || (build.attr('status') == 'ERROR')) {
            $(selector).removeClass('success');
            $(selector).addClass('failure');
            first_failure = $(data).find('build[status=SUCCESS]:eq(0)').prev().attr('href');
            ci_call(first_failure, function(data) {
                var changes_url = $(data).find('changes').attr('href');
                ci_call(changes_url, function(data) {
                    var usernames = {};
                    $(data).find('change').each(function() {
                        ci_call($(this).attr('href'), function(data) {
                            usernames[$(data).find('change').attr('username')] = true;
                            $(selector).find('.author').html(Object.keys(usernames).join(' or '));
                        });
                    });
                });
            });
        } else {
            $(selector).removeClass('failure');
            $(selector).addClass('success');
            first_failure = $(data).find('build[status!=SUCCESS]:eq(0)').prev().attr('href');
            ci_call(first_failure, function(data) {
                var changes_url = $(data).find('changes').attr('href');
                ci_call(changes_url, function(data) {
                    $(data).find('change').each(function() {
                        ci_call($(this).attr('href'), function(data) {
                            if ($(data).find('change').attr('username') !== undefined) {
                                $(selector).find('.author').html('Fixed by ' + $(data).find('change').attr('username'));
                            } else {
                                $(selector).find('.author').html('');
                            }
                        });
                    });
                });
            });
        }
        ci_call(build.attr('href'), function(data) {
            $(selector).find('.status').html($(data).find('statusText').text());
        });
    });

}

function runStat() {
    update_tc('.statistic-php-unit', '/httpAuth/app/rest/buildTypes/id:bt3/builds/');
    update_tc('.statistic-selenium-site', '/httpAuth/app/rest/buildTypes/id:bt6/builds/');
    update_tc('.statistic-selenium-assessment', '/httpAuth/app/rest/buildTypes/id:bt4/builds/');
    update_stat('.statistic-free-disk-space', '/admin_area/statistics/free-disk-space');
    update_stat('.statistic-order-count', '/admin_area/statistics/statistic-order-count');
    update_stat('.statistic-feedback-count', '/admin_area/statistics/statistic-feedback-count');
    update_stat('.statistic-crash-simulation', '/admin_area/statistics/statistic-crash-simulation');
    update_stat('.statistic-mail', '/admin_area/statistics/statistic-mail');
}
$(document).ready(function(){
    $(".reset-invite").click(function() {

        var simulationTimeStart = $(this).parents(".invites-row").find(".simulation_time-start").text();
        var simulationTimeEnd = $(this).parents(".invites-row").find(".simulation_time-end").text();
        var receiverUserEmail = $(this).parents(".invites-row").find(".receiverUser-email").text();
        return confirm("Вы точно хотите откатить симуляцию от "+simulationTimeStart+" для "+receiverUserEmail+" за "+simulationTimeEnd+"(дата)");

    });

    $(".action-invite-status").click(function() {

        return confirm("Вы точно хотите сменить статус?");

    });

    $(".clear_filter_button").click(function(e) {
        e.preventDefault();
        $(".clear_filter_button").next(".clear_form_field").val($(".clear_filter_button").attr("data-form-name"));
        $(".clear_filter_button").parent("form").submit();
    });

    $("#add_invites_button").click(function(e) {
        e.preventDefault();
        if(!isNaN(parseInt($("#add_invites_button").prev('input').val()))) {
            $("#add_invites_button").parent('form').attr('action', $("#add_invites_button").parent('form').attr('action')+
                $("#add_invites_button").prev('input').val());
            $("#add_invites_button").parent('form').submit();
        }
        else {
            alert("Необходимо ввести количество инвайтов");
            return false;
        }
    });

    $(".complete-invoice").click(function() {
        var clickedButton = $(this);
        if(confirm("Вы действительно подтверждаете оплату инвойса №"+clickedButton.attr("data-invoice-id")+"("+ "Заказ тарифа "+clickedButton.attr("data-tariff")+", на "+clickedButton.attr("data-months")+" месяц(ев) создан для "+clickedButton.attr("data-user-email")+")"+"?")) {
        clickedButton.addClass("disabled");
        $.getJSON( "/admin_area/completeInvoice", {invoice_id : clickedButton.attr("data-invoice-id")})
            .done(function(data) {
                clickedButton.closest("tr").find(".invoice-date-paid").html(data.paidAt);
                clickedButton.hide();
                clickedButton.parent("td").append("<span>Оплачен</span>");
            })
            .fail(function() {
                alert("В процессе обработки возникла ошибка.");
            });
        }
    });

    $(".change-comment-button").click(function() {
        var clickedButton = $(this);
        changedTextarea = clickedButton.closest("tr").find(".invoice-comment");
        clickedButton.addClass("disabled");
        changedTextarea.addClass("disabled");
        $.post( "/admin_area/invoiceComment", {invoice_id : clickedButton.attr("data-invoice-id"), invoice_comment : changedTextarea.val()})
            .done(function() {
                clickedButton.removeClass("disabled");
                clickedButton.html("Сохранено");
                setTimeout(function() {
                    clickedButton.html("Сохранить");
                    }, 1500)
            })
            .fail(function() {
                alert("В процессе обработки возникла ошибка.");
            });
    });

    $(".view-payment-log").click(function() {
        var clickedButton = $(this);
        clickedButton.addClass("disabled");
        $.getJSON( "/admin_area/getInvoiceLog", {invoice_id : clickedButton.attr("data-invoice-id")})
            .done(function(data) {
                clickedButton.removeClass("disabled");
                if(data.log != "") {
                    $("#myModalLabel").html("Логи для инвойса №"+clickedButton.attr("data-invoice-id"));
                    $("#myModalBody").html(data.log);
                    $('#myModal').modal('show');
                }
                else {
                    alert("Для данного инвойса нет логов.");
                }
            })
            .fail(function() {
                alert("В процессе обработки возникла ошибка.");
            });
    });

});