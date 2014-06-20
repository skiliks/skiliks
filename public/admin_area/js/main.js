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
    update_stat('.user-blocked-authorization', '/admin_area/statistics/user-blocked-authorization');
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
        if(confirm(
            "Вы действительно подтверждаете оплату инвойса №"
            + clickedButton.attr("data-invoice-id")
            + " (Заказано "
            + clickedButton.attr("data-simulations")
            + " симуляций на сумму "
            + clickedButton.attr("data-amount")
            + " руб. для аккаунта "
            + clickedButton.attr("data-user-email")+")?"
        )) {
            clickedButton.addClass("disabled");
            $.getJSON( "/admin_area/completeInvoice", {invoice_id : clickedButton.attr("data-invoice-id")})
                .done(function(data) {
                    clickedButton.closest("tr").find(".invoice-date-paid").html(data.paidAt);
                    clickedButton.hide();
                    clickedButton.removeClass("disabled");
                    clickedButton.parent("td").find(".disable-invoice").toggle();
                })
                .fail(function() {
                    alert("В процессе обработки возникла ошибка.");
                });
        }
    });

    $(".disable-invoice").click(function() {
        var clickedButton = $(this);
        if(confirm(
            "Вы действительно хотите отменить оплату инвойса №"
            + clickedButton.attr("data-invoice-id")
            + " (Заказано "
            + clickedButton.attr("data-simulations")
            + " симуляций на сумму "
            + clickedButton.attr("data-amount")
            + " руб. для аккаунта "
            + clickedButton.attr("data-user-email")+")?")
        ) {
            clickedButton.addClass("disabled");
            $.getJSON( "/admin_area/disableInvoice", {invoice_id : clickedButton.attr("data-invoice-id")})
                .done(function(data) {
                    clickedButton.closest("tr").find(".invoice-date-paid").html("Не оплачен");
                    clickedButton.hide();
                    clickedButton.removeClass("disabled");
                    clickedButton.parent("td").find(".complete-invoice").toggle();
                })
                .fail(function() {
                    alert("В процессе обработки возникла ошибка.");
                });
        }
    });

    $(".change-invoice-comment-action").click(function() {
        var clickedButton = $(this);
        changedTextarea = clickedButton.closest("tr").find(".invoice-comment");
        clickedButton.addClass("disabled");
        changedTextarea.addClass("disabled");

        $.post( "/admin_area/invoiceComment", {
            invoice_id : clickedButton.attr("data-invoice-id"),
            invoice_comment : changedTextarea.val()
        }).done(function() {
            clickedButton.html("Сохранено");
            clickedButton.addClass('btn-success');
            setTimeout(function() {
                clickedButton.html("Сохранить");
                clickedButton.removeClass('btn-success');
                clickedButton.removeClass("disabled");
                }, 1500)
        }) .fail(function() {
            alert("В процессе обработки возникла ошибка.");
        });
    });

    $('.change-invoice-price-action').click(function() {
        var clickedButton = $(this);
        changedTextarea = clickedButton.closest("tr").find(".invoice-price");
        clickedButton.addClass("disabled");
        changedTextarea.addClass("disabled");

        $.post( "/admin_area/invoicePriceUpdate", {
            invoice_id : clickedButton.attr("data-invoice-id"),
            invoice_amount : changedTextarea.val()
        }).done(function() {
                clickedButton.html("Сохранено");
                clickedButton.addClass('btn-success');
                setTimeout(function() {
                    clickedButton.html("Изменить");
                    clickedButton.removeClass('btn-success');
                    clickedButton.removeClass("disabled");
                }, 2000)
            }) .fail(function() {
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

    $(".btn-check-all").click(function() {
        $(this).parents("tr").find("input[type='checkbox']").prop('checked', true);
        return false;
    });

    $(".btn-uncheck-all").click(function() {
        $(this).parents("tr").find("input[type='checkbox']").prop('checked', false);
        return false;
    });

    $(".disable-filters").click(function() {
        window.location.href = "/admin_area/orders?disable_filters=true";
        return false;
    })

    $(".disable-all-filters").click(function() {
        window.location.href = "/admin_area/" + $(this).attr("data-href");
        return false;
    })

    $(".ban-corporate-user").click(function() {
        if(confirm("Вы точно хотите разбанить аккаунт " + $(this).attr("data-email"))) {
        $.post("/admin_area/ban_user/" + $(this).attr("data-id")+'/ban').
            done(function() {
                    window.location.reload();
                });
        }
    });

    $(".unban-corporate-user").click(function() {
        if(confirm("Вы точно хотите забанить аккаунт " + $(this).attr("data-email"))) {
            $.post("/admin_area/ban_user/" + $(this).attr("data-id")+'/unban').
                done(function() {
                    window.location.reload();
                });
        }
    });

    $('.action-toggle-is-test').click(function(){
        $.post('/admin_area/order/' + $(this).attr("data-invoice-id") + '/toggle-is-test/').
            done(function() {
                window.location.reload();
            });
    });

    $('.feedback-edit-button').click(function() {
        var feedback_comment = $(this).parents('tr').find('.feedback-comment');
        var message = feedback_comment.text();
        feedback_comment.html('<textarea>' + message + '</textarea>');
        $(this).parent().html('<a class="btn btn-success feedback-save-button">Сохранить</a>');
    });

    $('.feedback-save-button').live('click', function() {
        console.log('cac');
        var feedback_comment = $(this).parents('tr').find('.feedback-comment');
        var message = feedback_comment.find('textarea').val();
        console.log(message);
        var id = feedback_comment.data('feedback-id');
        $.post( "/admin_area/feedbacks", {message : message, id:id, is_ajax:'yes'}, function( data ) {
            window.location.assign('/admin_area/feedbacks');
        });
    });

    // ----

    window.changeEmailValidation = function addVacancyValidation(form, data, hasError) {

        if (true == data.isValid) {
            alert('Емейл сохранён.');
            window.location.reload();
        } else {
            error = JSON.parse(data.errors);
            alert(error['emails']);
        }

        return false;
    };

    // ----

    window.changeWhiteListValidation = function addVacancyValidation(form, data, hasError) {

        if (true == data.isValid) {
            alert('Белый список обновлён.');
        } else {
            error = JSON.parse(data.errors);
            alert(error['emails_white_list']);
        }

        return false;
    };
});