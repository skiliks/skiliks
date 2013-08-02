/* global $ */
var prefix = '/statistics/SeleniumTestsAuth?params=';
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



});