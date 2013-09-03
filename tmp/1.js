// в JS посмотреть какой сегодня день недели


$('.workday').remove();
$('body').append('<div class="workday"><div class="workday-switcher">hide/show</div><table></table><div>');
$('.workday').css('position', 'absolute');
$('.workday').css('overflow', 'hidden');
$('.workday').css('top', '50px');
$('.workday').css('left', '50px');
$('.workday').css('background', '#fff');
$('.workday').css('border', '1px solid #000');
$('.workday').css('z-index', '200');
$('.workday table').css('border', '1px solid #999');
$('.workday-switcher').css('text-align', 'right');
$('.workday-switcher').css('width', '500px');

var workday = $('.workday');
var issues = new Array();
var tds = new Array();
var days = new Array();
var j = new Array();
var subTime = new Array();

var weekDays = ['','Вт','Ср','Чт','Пт','Сб','Вс','Пн', 'Следующий спринт'];

$('.ghx-issue').each(function(i, item){
    var title = $(item).find('.ghx-key-link').html();
    var url = $(item).find('.ghx-key-link').attr('href');
    var summary = $(item).find('.ghx-summary').text();
    var isTimeInMinutes = 0 < $(item).find('.ghx-end').text().indexOf('m');
    var time = $(item).find('.ghx-end').text().replace('h', '');
    time = parseFloat(time);
    if (isNaN(time)) { time = 0; }
    if (isTimeInMinutes) { time = (time/60).toPrecision(1); }
    var userName = $(item).find('.ghx-avatar img').attr('alt');

    if(undefined === issues[userName]) {
        issues[userName] = new Array();
        tds[userName] = '';
        j[userName] = 0;
        subTime[userName] = 0;
        days[userName] = {
            0: [],
            1: [],
            2: [],
            3: [],
            4: [],
            5: [],
            6: [],
            7: []
        };
    }

    issues[userName].push({
        title: title,
        user: userName,
        summary: summary,
        duration: time,
        url: url
    });
});

for(userName in issues) {
    $.each(issues[userName], function(key, issue){
        subTime[userName] = parseInt(subTime[userName]) + parseInt(issue.duration);
        days[userName][j[userName]].push(issue);
        if (8 <= subTime[userName]) {
            subTime[userName] = subTime[userName] - 8;
            j[userName]++;
            days[userName][j[userName]].push(issue);
        }
    });
}

var html = '';

var text = '';
$.each(weekDays, function(){
    text += '<td>' + this + '</td>';
});

html += '<tr>' + text + '<tr/>';

for(userName in days) {
    var tds = '<td>' + userName + '</td>';
    var overtime = 0;
    for(dayNo in days[userName]) {
        var text = '';
        var subtotalDuration = 0 + overtime;
        for(key in days[userName][dayNo]) {
            var issue = days[userName][dayNo][key];
            text +=  '<a href="' + issue.url + '">' + issue.title + '</a>(' + issue.duration + 'h)' + '<br/>';
            if (0 < overtime) {
                subtotalDuration = subtotalDuration + parseInt(overtime);
                overtime = 0;
            } else {
                subtotalDuration = subtotalDuration + parseInt(issue.duration);
            }
        }
        if (subtotalDuration > 8) {
            var overtime = subtotalDuration - 8;
            subtotalDuration = subtotalDuration - overtime;
        }
        text += '<strong>' + subtotalDuration + 'h</strong>';
        tds += '<td>' + text + '</td>';
    }
    html += '<tr>' + tds + '</tr>';
}

workday.find('table').append(html);

$('.workday table tr').css('border', '1px solid #999');
$('.workday table td').css('border', '1px solid #999');
$('.workday').css('height', '600px');
$('.workday').css('overflow', 'scroll');

$('.workday-switcher').click(function(){ workday.find('table').toggle(); });