charactersPointsTitles = {
    /**
     * функция - запускатор, выбирает с сервера набор базовых значений
     */
    draw: function (){
        sender.charactersPointsTitlesSelectsRequest();
    },
    
    drawGrid: function (data){
        var customParams = this.params;
        customParams['colModel'][1]['editoptions']['value'] = "'"+data['characters_points_titles']+"'";
        customParams['colModel'][1]['surl'] = "'"+config.host.name+"index.php/charactersPointsTitles/getCharactersPointsTitlesHtml'";
        
        jgridController.setParams(customParams);
        grid = jgridController.getHTML();
        
        world.draw(grid.html);
        eval(grid.script);
        
        menuMain.setActive('charactersPointsTitles');
    },
    params: {
            'navId' : 'navgrid',
            'pagerId' : 'pagernav',
            'colNames' : "'№','Наименование цели обучения','Номер требуемого поведения','Наименование требуемого поведения','Шкала'",
            'colModel' : [
                {
                    'name':"'id'",
                    'index':"'id'", 
                    'width':"50",
                    'align' : '"center"',
                    'formatter':"'integer'",
                    'editable':'false'
                    },
                {
                    'name':"'parent_id'",
                    'index':"'parent_id'", 
                    'width':"300",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{parent_id-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{parent_id-selectsurl}'"
                    },
                {
                    'name':"'code'",
                    'index':"'code'", 
                    'width':"300",
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
                    'name':"'scale'",
                    'index':"'scale'", 
                    'width':"100",
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
                    }
                ],
                'sortname' : 'id',
                'caption' : 'Наименования требуемых поведений',
                'url' : config.host.name+'index.php/charactersPointsTitles/draw',
                'editurl' : config.host.name+'index.php/charactersPointsTitles/edit',
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
                    'width' : '900',
                    'height' : '200',
                    'reloadAfterSubmit' : 'true',
                    'closeAfterEdit' : 'true'
                },
            'addOptionsA' : {
                        'width' : '900',
                        'height' : '200',
                        'reloadAfterSubmit' : 'true',
                        'closeAfterAdd' : 'true'
                    }
        }
}