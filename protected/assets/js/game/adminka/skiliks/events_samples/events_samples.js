eventsSamples = {
    /**
     * функция - запускатор, выбирает с сервера набор базовых значений
     */
    draw: function (){
        sender.eventsSamplesSelectsRequest();
    },
    
    /**
     * ф-ция отрисовки грида
     */
    drawGrid: function (data){
        var customParams = this.params;
        //on_ignore_result
        customParams['colModel'][3]['editoptions']['value'] = "'"+data['events_results']+"'";
        customParams['colModel'][3]['surl'] = "'"+config.host.name+"index.php/eventsSamples/getEventsResultsHtml'";
        //on_hold_logic
        customParams['colModel'][4]['editoptions']['value'] = "'"+data['events_on_hold_logic']+"'";
        customParams['colModel'][4]['surl'] = "'"+config.host.name+"index.php/eventsSamples/getEventsOnHoldLogicHtml'";
        
        //формируем грид
        jgridController.setParams(customParams);
        grid = jgridController.getHTML();
        
        var html = '';
        html += grid.html;
        
        world.draw(html);
        eval(grid.script);
        //делаем менюшку активной
        menuMain.setActive('eventsSamples');
    },
    params: {
            'navId' : 'navgrid',
            'pagerId' : 'pagernav',
            'colNames' : "'№','Код','Название','Результат при игнорировании','Поведение при удержании'",
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
                    'name':"'code'",
                    'index':"'code'", 
                    'width':"200",
                    'align' : '"center"',
                    'editable':'true',
                    'editoptions':{
                        'readonly' : 'false',
                        'size' : "10",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'editrules':{
                        'required':'true'
                        }
                    },
                {
                    'name':"'title'",
                    'index':"'title'", 
                    'width':"500",
                    'align' : '"center"',
                    'editable':'true',
                    'editoptions':{
                        'readonly' : 'false',
                        'size' : "10",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'editrules':{
                        'required':'true'
                        }
                    },
                {
                    'name':"'on_ignore_result'",
                    'index':"'on_ignore_result'", 
                    'width':"300",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{on_ignore_result-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{on_ignore_result-selectsurl}'"
                    },
                {
                    'name':"'on_hold_logic'",
                    'index':"'on_hold_logic'", 
                    'width':"300",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{on_hold_logic-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{on_hold_logic-selectsurl}'"
                    }
                ],
                'sortname' : 'id',
                'caption' : 'Примеры событий',
                'url' : config.host.name+'index.php/eventsSamples/draw',
                'editurl' : config.host.name+'index.php/eventsSamples/edit',
                'height' : '600',
                'rowNum' : '30',
                'rowList': [
                        '30',
                        '50',
                        '75',
                        '100',
                        '150'
                        ],
            'editOptionsA' : {
                    'width' : '700',
                    'height' : '250',
                    'reloadAfterSubmit' : 'true',
                    'closeAfterEdit' : 'true'
                },
            'addOptionsA' : {
                        'width' : '700',
                        'height' : '250',
                        'reloadAfterSubmit' : 'true',
                        'closeAfterAdd' : 'true'
                    }
        }
}