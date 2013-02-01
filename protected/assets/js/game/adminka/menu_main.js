menuMain = {
    get: function (){
        return this.defaultHtml;
        
    },
    setActive:function(element)
    {
        $('#ulMenuMain').children('li').each(function () {
            $(this).removeClass("active");
        });
        $('#'+element).addClass("active");
    },
    defaultHtml: '<div class="container" style="height:50px;">'+
            '<ul class="nav nav-pills" id="ulMenuMain">'+
            '<li id="charactersPointsTitles"><a href="#" onclick="charactersPointsTitles.draw()">Наименования требуемых поведений</a></li>'+
            '<!--<li id="dialogBranches"><a href="#" onclick="dialogBranches.draw()">Ветки диалога</a></li>-->'+
            '<li id="dialogs" onclick="dialogs.draw()"><a href="#">Диалоги</a></li>'+
            '<!--<li id="eventsResults" onclick="eventsResults.draw()"><a href="#">Результаты событий</a></li> -->'+
            '<li id="eventsSamples" onclick="eventsSamples.draw()"><a href="#">Примеры событий</a></li>'+
            '<!--<li id="eventsChoiсes" onclick="eventsChoiсes.draw()"><a href="#">Взаимосвязь событий</a></li>-->'+
            '<li id="logging" class="dropdown">'+
                    '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'+
                            'Логирование'+
                            '<b class="caret"></b>'+
                    '</a>'+
                    '<ul class="dropdown-menu">'+
                            '<li><a href="#" onclick="logging.Windows();return false;">Universal</a></li>'+
                            '<li><a href="#" onclick="logging.DayPlan()">Plan</a></li>'+
                            '<li><a href="#" onclick="logging.DialogPointsDetail()">Assessment - detail</a></li>'+
                            '<li><a href="#" onclick="logging.FullAggregate()">Assessment - aggregate</a></li>'+
                            '<li><a href="#" onclick="logging.MailInDetail()">Mail_Inbox - detail</a></li>'+
                            '<li><a href="#" onclick="logging.MailInAggregate()">Mail_Inbox - aggregate</a></li>'+
                            '<li><a href="#" onclick="logging.MailOutDetail()">Mail_Outbox - detail</a></li>'+
                            '<li><a href="#" onclick="logging.MailOutAggregate()">Mail_Outbox - aggregate</a></li>'+
                            '<li><a href="#" onclick="logging.Documents()">Documents</a></li>'+
                            '<li><a href="#" onclick="logging.Dialogs()">Dialogs</a></li>'+
                            '<li><a href="#" onclick="logging.LegActionsDetail()">Leg_actions - detail</a></li>'+
                            '<li><a href="#" onclick="logging.LegActionsAgregated()">Leg_actions - agregated</a></li>'+
                            '<li><a href="#" onclick="logging.ExcelAssessmentDetail()">Excel assessment - detail</a></li>'+
                    '</ul>'+
            '</li>'+	
            '</ul>'+
        '</div>'
}