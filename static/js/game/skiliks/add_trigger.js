addTrigger = {
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    
    hTemp:0,
    mTemp:0,

    setDivTop: function(val)
    {
        this.divTop = val;
    },
    setDivLeft: function(val)
    {
        this.divLeft = val;
    },
    createDiv: function()
    {
        var div = document.createElement('div');
          div.setAttribute('id', 'addTriggerMainDiv');
          div.setAttribute('class', 'addTriggerMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = "50";
          document.body.appendChild(div);
          $('#addTriggerMainDiv').css('top', this.divTop+'px');
          $('#addTriggerMainDiv').css('left',  this.divLeft+'px');
          $('#addTriggerMainDiv').css('right',  this.divLeft+'px');
          
          this.issetDiv = true;
    },
    draw: function()
    {
        if(session.getSimulationType()!='dev'){return;}
        
        //sender.addTriggerGetList();
        this.drawInterface();
    },
    drawInterface: function()
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        var html = this.html;
        $('#addTriggerMainDiv').html(html);
    },
    add: function(eventCode, delay, clearEvents, clearAssessment)
    {
        if(eventCode == ''){
            var message = 'Укажите код события';
            var lang_alert_title = 'Триггер';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }else
        if(delay == ''){
            var message = 'Укажите задержку в игровых минутах';
            var lang_alert_title = 'Триггер';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-error');
        }else{
            var clearAssessmentS = 0;
            var clearEventsS = 0;
            if(clearEvents){
                clearEventsS = 1;
            }
            if(clearAssessment){
                clearAssessmentS = 1;
            }
        
            sender.addTriggerAdd(eventCode, delay, clearEventsS, clearAssessmentS);
        }
    },
    setNewTime: function(newTimeH, newTimeM)
    {
        newTimeH = parseInt(newTimeH);
        newTimeM = parseInt(newTimeM);
        
        if(!newTimeH){newTimeH=0;}
        if(!newTimeM){newTimeM=0;}
        
        //alert(newTimeH+':'+newTimeM);
        this.hTemp = newTimeH;
        this.mTemp = newTimeM;
        sender.timerSetNewTime(newTimeH,newTimeM);
    },
    applyNewTime: function()
    {
        timer.setTimeTo(this.hTemp, this.mTemp);
    },
    html:'<form class="well"><label for="addTriggerSelect" style="width:100px;">Код события:</label><input id = "addTriggerSelect" type="text" class="span2" style="float:left;">'+
    '<label for="addTriggerDelay" style="width:200px;">&nbsp;&nbsp;&nbsp;&nbsp;Задержка(игровые минуты):</label><input id="addTriggerDelay" type="text" class="span2" value="0">'+
    '<br>'+
    '<label for="addTriggerClearEvents" style="width:200px;">Очистить очередь событий:</label><input id="addTriggerClearEvents" type="checkbox" style="float:left;">'+
    '<label for="addTriggerClearAssessment" style="width:200px; margin-left:50px;">Очистить очередь оценки:</label><input id="addTriggerClearAssessment" type="checkbox" style="float:left;">'+
    '<input type="button" onclick="addTrigger.add(document.getElementById(\'addTriggerSelect\').value,document.getElementById(\'addTriggerDelay\').value,document.getElementById(\'addTriggerClearEvents\').checked,document.getElementById(\'addTriggerClearAssessment\').checked);" value="Создать" class="btn" style="margin-top:0px; margin-left:25px;">'+
    '</form>'+
    
    '<form class="well"><label for="newTimeH" style="width:100px;">Новое время:</label><input id = "newTimeH" type="text" class="span1" style="float:left;" maxlength="2">'+
    '<label for="newTimeM" style="width:40px;">&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;</label><input id="newTimeM" type="text" class="span1" maxlength="2">'+
    '<input type="button" onclick="addTrigger.setNewTime(document.getElementById(\'newTimeH\').value,document.getElementById(\'newTimeM\').value);" value="Задать" class="btn" style="margin-top:0px; margin-left:25px;">' +
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(0,0);">0:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(10,0);">10:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(11,0);">11:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(12,0);">12:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(13,0);">13:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(14,0);">14:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(15,0);">15:00</a>' + 
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(16,0);">16:00</a>' +
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(17,0);">17:00</a>' +
    '&nbsp;&nbsp;&nbsp;<a onClick="addTrigger.setNewTime(17,50);">17:50</a>'
}