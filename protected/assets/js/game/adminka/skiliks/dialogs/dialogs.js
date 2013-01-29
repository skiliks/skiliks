dialogs = {
    /**
     * последняя выбранная запись грида
     **/
    lastSelectedRow: 0,
    
    /**
     * функция - запускатор, выбирает с сервера набор базовых значений
     */
    draw: function (){
        sender.dialogsSelectsRequest();
    },
    
    /**
     * ф-ция отрисовки грида
     */
    drawGrid: function (data){
        var customParams = this.params;
        //code
        customParams['colModel'][1]['editoptions']['value'] = "'"+data['code']+"'";
        customParams['colModel'][1]['surl'] = "'"+config.host.name+"index.php/dialogs/getEventsCodesHtml'";
        //ch_from
        customParams['colModel'][2]['editoptions']['value'] = "'"+data['characters']+"'";
        customParams['colModel'][2]['surl'] = "'"+config.host.name+"index.php/dialogs/getCharactersHtml'";
        //ch_from_state
        customParams['colModel'][3]['editoptions']['value'] = "'"+data['characters_states']+"'";
        customParams['colModel'][3]['surl'] = "'"+config.host.name+"index.php/dialogs/getCharactersStatesHtml'";
        //ch_to
        customParams['colModel'][4]['editoptions']['value'] = "'"+data['characters']+"'";
        customParams['colModel'][4]['surl'] = "'"+config.host.name+"index.php/dialogs/getCharactersHtml'";
        //ch_to_state
        customParams['colModel'][5]['editoptions']['value'] = "'"+data['characters_states']+"'";
        customParams['colModel'][5]['surl'] = "'"+config.host.name+"index.php/dialogs/getCharactersStatesHtml'";
        //dialog_subtype
        customParams['colModel'][6]['editoptions']['value'] = "'"+data['dialog_subtypes']+"'";
        customParams['colModel'][6]['surl'] = "'"+config.host.name+"index.php/dialogs/getDialogSubtypesHtml'";
        //event_result
        customParams['colModel'][9]['editoptions']['value'] = "'"+data['events_results']+"'";
        customParams['colModel'][9]['surl'] = "'"+config.host.name+"index.php/dialogs/getEventsResultsHtml'";
        //next_event
        customParams['colModel'][12]['editoptions']['value'] = "'"+data['next_event']+"'";
        customParams['colModel'][12]['surl'] = "'"+config.host.name+"index.php/dialogs/getNextEventHtml'";
        
        //формируем грид
        jgridController.setParams(customParams);
        var inputsParams = [
            {
                'prefix' : '_2',
                'inputType' : 'checkbox'
            }
        ];
        jgridController.addCustomInputsParams(inputsParams);
        grid = jgridController.getHTML();
        
        var html = '';
        html += grid.html;
        html += this.navButtonsHtml;
        
        world.draw(html);
        eval(grid.script);
        //делаем менюшку активной
        menuMain.setActive('dialogs');
        //скрываем нав меню
        this.hideNavButtons();
    },
    
    /**
     * показать навигационное меню снизу
     */
    showNavButtons : function (id)
    {
        this.lastSelectedRow = id;
        $("#navMenu").show();
        $("#charactersPoints").html('');
    },
    
    /**
     * спрятать навигационное меню снизу
     */
    hideNavButtons: function ()
    {
        $("#navMenu").hide();
        $("#charactersPoints").html('');
    },
    
    /**
     * функция - запускатор, выбирает с сервера набор базовых значений
     */
    showCharactersPoints: function()
    {
        sender.dialogsGetPointsRequest(this.lastSelectedRow);
    },
    
    /**
     * отрисовка характеристик персонажа
     */
    drawCharactersPoints: function(data)
    {
        $("#charactersPoints").html('');
        
        var parents = data['parents'];
        var childs = data['childs'];
        
        for (var key in parents)
        {
            var value = parents[key];
            var curHtml = $("#charactersPoints").html();
            
            var addHtml = this.charactersPointParentHtml;
            addHtml = php.str_replace('{id}', value['id'], addHtml);
            addHtml = php.str_replace('{name}', value['name'], addHtml);
            addHtml = php.str_replace('{code}', value['code'], addHtml);
            
            curHtml += addHtml;
            $("#charactersPoints").html(curHtml);
        }
        
        for (var key in childs)
        {
            var value = childs[key];
            var curHtml = $("#charactersPointParent_"+value['parent_id']).html();
            
            var flag = '';
            if(value['flag']==1){
                flag = 'checked';
            }
            
            var addHtml = this.charactersPointChildHtml;
            addHtml = php.str_replace('{id}', value['id'], addHtml);
            addHtml = php.str_replace('{code}', value['code'], addHtml);
            addHtml = php.str_replace('{name}', value['name'], addHtml);
            addHtml = php.str_replace('{flag}', flag, addHtml);
            addHtml = php.str_replace('{value}', value['add_value'], addHtml);
            
            curHtml += addHtml;
            
            $("#charactersPointParent_"+value['parent_id']).html(curHtml);
        }
        
        var curHtml = $("#charactersPoints").html();
        curHtml += this.characterPointsButtons;
        $("#charactersPoints").html(curHtml);
    },
    showCharactersPointChild: function(id)
    {
        if($("#charactersPointParent_"+id).css('display') != 'block')
        {
            $("#charactersPointParent_"+id).show();
        }else{
            $("#charactersPointParent_"+id).hide();
        }
    },
    hideCharactersPoints: function()
    {
        $("#charactersPoints").html('');
    },
    saveCharactersPoints: function()
    {
        var points = {};
        $('.cphi').each(function () {
            var pointId = this.id;
            var pointVal = $(this).val();
            var pointNumId = php.str_replace('pointInput_', '', pointId);
            var pointFlag = 0;
            if($('#pointCheckbox_'+pointNumId).attr('checked')){
                pointFlag = 1;
            }
            points[pointNumId] = {
                'flag': pointFlag,
                'value': pointVal
                };
        });
        
        sender.saveCharactersPoints(this.lastSelectedRow, points);
    },
    characterPointsButtons: '<input type="button" value ="Сохранить" onclick="dialogs.saveCharactersPoints();" class="btn"> '+
                            '<input type="button" value ="Отмена" onclick="dialogs.hideCharactersPoints();" class="btn">',
    charactersPointChildHtml: '<div class="control-group">'+
                            '<label class="control-label" for="pointCheckbox_{id}" >{code} - {name}</label>'+
                            '<div class="controls">'+
                            '<label class="checkbox">'+
                                '<input id="pointCheckbox_{id}" class="cphc" type="checkbox" value="1" {flag}>'+
                            '</label>'+
                            '<input id="pointInput_{id}" class="cphi" type="text" value ="{value}">'+
                            '</div>'+
                        '</div>',
    charactersPointParentHtml: '<div class="control-group">'+
                            '<a onclick="dialogs.showCharactersPointChild({id})"style="cursor:pointer;">{code} : {name}</a>'+
                            '<div id="charactersPointParent_{id}" style="display:none;">'+
                            '</div>'+
                        '</div>',
    navButtonsHtml: '<br><div id="navMenu">'+
                '<input type="button" value ="Отобразить влияние на Характеристики" onclick="dialogs.showCharactersPoints();" class="btn">'+
                '</div>'+
                '<br><div style="width:800px; text-align:left;">'+
                    '<form class="form-horizontal">'+
                        '<fieldset id="charactersPoints">'+
                        '</fieldset>'+
                    '</form>'+
                '</div>',
    //id 	ch_from	ch_from_state ch_to ch_to_state dialog_subtype text duration event_result branch_id next_branch
    params: {
            'navId' : 'navgrid',
            'pagerId' : 'pagernav',
            'colNames' : "'№','Код','Персонаж ОТ','Персонаж ОТ состояние','Персонаж К','Персонаж К состояние','Подтип','Текст','Длительность','Результат','Шаг','Реплика','Следующее событие','Задержка','Флаг конечной реплики'",
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
                    'width':"75",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{code-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{code-selectsurl}'"
                },
                {
                    'name':"'ch_from'",
                    'index':"'ch_from'", 
                    'width':"135",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{ch_from-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{ch_from-selectsurl}'"
                    },
                {
                    'name':"'ch_from_state'",
                    'index':"'ch_from_state'", 
                    'width':"150",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{ch_from_state-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{ch_from_state-selectsurl}'"
                    },
                {
                    'name':"'ch_to'",
                    'index':"'ch_to'", 
                    'width':"125",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{ch_to-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{ch_to-selectsurl}'"
                    },
                {
                    'name':"'ch_to_state'",
                    'index':"'ch_to_state'", 
                    'width':"150",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{ch_to_state-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{ch_to_state-selectsurl}'"
                    },
                {
                    'name':"'dialog_subtype'",
                    'index':"'dialog_subtype'", 
                    'width':"100",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{dialog_subtype-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{dialog_subtype-selectsurl}'"
                    },
                {
                    'name':"'text'",
                    'index':"'text'", 
                    'width':"500",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"textarea"',
                    'editoptions':{
                        'rows' : "2",
                        'cols' : "20",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        }
                    },
                {
                    'name':"'duration'",
                    'index':"'duration'", 
                    'width':"100",
                    'align' : '"center"',
                    'editable':'true',
                    'editoptions':{
                        'readonly' : 'false',
                        'size' : "10",
                        'maxlength': "11",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'editrules':{
                        'integer':'true'
                        }
                    },
                {
                    'name':"'event_result'",
                    'index':"'event_result'", 
                    'width':"150",
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
                    'name':"'step_number'",
                    'index':"'step_number'", 
                    'width':"75",
                    'align' : '"center"',
                    'editable':'true',
                    'formatter':"'integer'",
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
                    'name':"'replica_number'",
                    'index':"'replica_number'", 
                    'width':"75",
                    'align' : '"center"',
                    'editable':'true',
                    'formatter':"'integer'",
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
                    'name':"'next_event'",
                    'index':"'next_event'", 
                    'width':"200",
                    'align' : '"center"',
                    'editable':'true',
                    'edittype':'"select"',
                    'editoptions':{
                        'value' : "'{next_event-selectvalue}'",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'stype' : '"select"', 
                    'search':'true', 
                    'surl':"'{next_event-selectsurl}'"
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
                        'maxlength': "11",
                        'dataInit': 'function(elem) {$(elem).width(450);}'
                        },
                    'editrules':{
                        'integer':'true'
                        }
                    },
                    {
                    'name':"'is_final_replica'",
                    'index':"'is_final_replica'",  
                    'width':"180",
                    'align' : '"right"',
                    'editable':'true',
                    'edittype':'"custom"',
                    'editoptions': "{ custom_element:MultiCheckElem_2, custom_value:MultiCheckVal_2, list:'1:Да' }",
                    'search':'false'
                    }
                ],
                'sortname' : 'id',
                'caption' : 'Диалоги',
                'url' : config.host.name+'index.php/dialogs/draw',
                'editurl' : config.host.name+'index.php/dialogs/edit',
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
                    'height' : '650',
                    'reloadAfterSubmit' : 'true',
                    'closeAfterEdit' : 'true'
                },
            'addOptionsA' : {
                        'width' : '700',
                        'height' : '650',
                        'reloadAfterSubmit' : 'true',
                        'closeAfterAdd' : 'true'
                    },
            'onSelectRow' : "function(id){dialogs.showNavButtons(id);}",
            'gridComplete' : "function(){dialogs.hideNavButtons();}"
        }
}