events = {
    issetDiv: false,
    divTop: 50,
    divLeft: 50,
    eventsArray:{},

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
          div.setAttribute('id', 'eventsMainDiv');
          div.setAttribute('class', 'eventsMainDiv');
          div.style.position = "absolute";
          div.style.zIndex = "50";
          document.body.appendChild(div);
          $('#eventsMainDiv').css('top', this.divTop+'px');
          $('#eventsMainDiv').css('left',  this.divLeft+'px');
          $('#eventsMainDiv').css('right',  this.divLeft+'px');
          
          this.issetDiv = true;
    },
    launch: function(id, command)
    {
        var text = this.eventsArray[id];
        if(command==1){
            var message = text;
            var lang_alert_title = 'Запуск задачи';
            var lang_confirmed = 'Ок';
            messages.dw_alert(message, lang_alert_title, lang_confirmed, 'alert-success');
        }else if(command==3){
            dayPlan.taskdayPlanToSelect = id;
            dayPlan.draw();
        }
        
        this.hide();
        delete this.eventsArray[id];
    },
    draw: function(params)
    {
        if(!this.issetDiv){
            this.createDiv();
        }
        
        this.eventsArray[params['id']] = params['text'];
        var message = this.curTaskMessageHtml;
        
        message = php.str_replace('{onclick3}',  'onclick="events.launch(\''+params['id']+'\',1)"', message);
        message = php.str_replace('{onclick1}',  'onclick="events.launch(\''+params['id']+'\',2)"', message);
        message = php.str_replace('{onclick2}',  'onclick="events.launch(\''+params['id']+'\',3)"', message);
        
        messages.showCustomSystemMessage(message);
        /*var dialog = '<dialog>';
        dialog += '<dt>'+params['text'];
        //ответы
        dialog += '<dd>-<a onclick="events.launch(\''+params['id']+'\',1)">Сделать сейчас</a>';
        dialog += '<dd>-<a onclick="events.launch(\''+params['id']+'\',2)">Игнорировать</a>';
        dialog += '<dd>-<a onclick="events.launch(\''+params['id']+'\',3)">Перепланировать</a>';
        
        dialog += '</dialog>';
        
        var html = this.html;
        html = php.str_replace('{dialog}', dialog, html);
        
        $('#eventsMainDiv').show();
        $('#eventsMainDiv').html(html);*/
    },
    hide: function()
    {
        //$('#eventsMainDiv').hide();
        messages.hideCustomSystemMessage();
    },
    html:'<form class="well">{dialog}</form>',
    curTaskMessageHtml:'<div class="planner-popup">'+
        	'<div class="planner-popup-tit"><img alt="" src="img/planner/type-current-task.png"></div>'+
        	
        	'<p class="planner-popup-text">'+
        		'Согласовать с производственным отделом новую отчетную форму.'+
        	'</p>'+
        	
        	'<table class="planner-popup-btn">'+
        		'<tbody><tr>'+
        			'<td>'+
        				'<div>'+
        					'<div {onclick1}>ИГНОРИРОВАТЬ</div>'+
        				'</div>'+
        			'</td>'+
        			'<td>'+
        				'<div>'+
        					'<div {onclick2}>перепланировать</div>'+
        				'</div>'+
        			'</td>'+
        			'<td>'+
        				'<div>'+
        					'<div {onclick3}>сделать сейчас</div>'+
        				'</div>'+
        			'</td>'+
        		'</tr>'+
        	'</tbody></table>'+
        '</div>'
}