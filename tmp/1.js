$('.workday').remove();
$('body').append('<div class="workday"><table></table><div>');
$('.workday').css('position', 'absolute');
$('.workday').css('overflow', 'hidden');
$('.workday').css('top', '50px');
$('.workday').css('left', '50px');
$('.workday').css('background', '#fff');
$('.workday').css('border', '1px solid #000');
$('.workday').css('z-index', '200');

var workday = $('.workday');
var issues = new Array();

$('.ghx-issue').each(function(i, item){
    var link = $(item).find('.ghx-key-link');
    var summary = $(item).find('.ghx-summary').text();
    var time = $(item).find('.ghx-end').text().replace('h', '');
    time = parseFloat(time);
    var user = $(item).find('.ghx-avatar img').attr('alt');

    //console.log($(item).find('.ghx-avatar'));

    if(undefined === issues[user]) {
        issues[user] = new Array();
    }

    issues[user].push({
        link: link,
        user: user,
        summary: summary,
        time: time
    });
});

var html = '';
$.each(issues, function(key, value){
    var tds = '';

});

console.log(issues);