/*eventsChoiсes = {
    draw: function (){
        sender.eventsChoicesSelectsRequest();
    },
    
    drawGrid: function (data){
        var customParams = this.params;
        customParams['colModel'][1]['editoptions']['value'] = "'"+data['events_samples']+"'";
        customParams['colModel'][1]['surl'] = "'"+config.host.name+"index.php/eventsChoices/getEventsSamplesHtml'";
        //on_ignore_result
        customParams['colModel'][2]['editoptions']['value'] = "'"+data['events_results']+"'";
        customParams['colModel'][2]['surl'] = "'"+config.host.name+"index.php/eventsChoices/getEventsResultsHtml'";
        //on_hold_logic
        customParams['colModel'][4]['editoptions']['value'] = "'"+data['events_samples']+"'";
        customParams['colModel'][4]['surl'] = "'"+config.host.name+"index.php/eventsChoices/getEventsSamplesHtml'";
        
        //формируем грид
        jgridController.setParams(customParams);
        grid = jgridController.getHTML();
        
        var html = '';
        html += grid.html;
        
        world.draw(html);
        eval(grid.script);
        //делаем менюшку активной
        menuMain.setActive('eventsChoiсes');
    },
    //id 	event_id 	event_result 	delay 	dstEventId
    params: {
            'navId' : 'navgrid',
            'pagerId' : 'pagernav',
            'colNames' : "'№','Событие','Результат','Задержка','Следующее событие'",
            'colModel' : [
                {
                    'name':"'id'",
                    'index':"'id'", 
                    'width':"25",
                    'align' : '"center"',
                    'formatter':"'integer'",
                    'editable':'false'
                    },
                {
                    'name':"'event_id'",
                    'index':"'event_id'", 
                    'width':"400",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{event_id-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{event_id-selectsurl}'"
                    },
                {
                    'name':"'event_result'",
                    'index':"'event_result'", 
                    'width':"250",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{event_result-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{event_result-selectsurl}'"
                    },
                {
                    'name':"'delay'",
                    'index':"'delay'", 
                    'width':"100",
                    'align' : '"center"',
                    'editable':'true',
                    'editoptions':{
                        'readonly' : 'false',
                        'size' : "10",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'editrules':{
                        'integer':'true'
                        }
                    },
                {
                    'name':"'dstEventId'",
                    'index':"'dstEventId'", 
                    'width':"400",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{dstEventId-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{dstEventId-selectsurl}'"
                    }
                ],
                'sortname' : 'id',
                'caption' : 'Взаимосвязь событий',
                'url' : config.host.name+'index.php/eventsChoices/draw',
                'editurl' : config.host.name+'index.php/eventsChoices/edit',
                'height' : '300',
            'editOptionsA' : {
                    'width' : '700',
                    'height' : '220',
                    'reloadAfterSubmit' : 'true',
                    'closeAfterEdit' : 'true'
                },
            'addOptionsA' : {
                        'width' : '700',
                        'height' : '220',
                        'reloadAfterSubmit' : 'true',
                        'closeAfterAdd' : 'true'
                    }
        }
}*/