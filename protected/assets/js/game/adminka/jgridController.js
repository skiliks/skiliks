jgridController = {
    /**
     * html код грида
     * @var string
     */
    htmlCode : "<table id=\"{navId}\"></table>"+
                "<div id=\"{pagerId}\"></div>",
    /**
     * js код грида
     * @var string
     */
    jsCode :    "var mygrid = jQuery(\"#{navId}\").jqGrid({ "+
                "url:'{url}', "+
                "datatype: \"{datatype}\","+
                "mtype: '{mtype}',"+
                "colNames:[{colNames}], "+
                "colModel:[{colModel}], "+
                "rowNum:{rowNum}, "+
                "rowList:[{rowList}],"+
                "pager: '#{pagerId}', "+
                "sortname: '{sortname}', "+
                "viewrecords: {viewrecords}, "+
                "sortorder: \"{sortorder}\", "+
                "caption:\"{caption}\", "+
                "editurl:\"{editurl}\", "+
                "height:{height},"+
                "viewsortcols: [true, 'vertical', true],"+
                "multiselect: {multiselect},"+
                "multiboxonly: {multiboxonly},"+
                "gridview:{gridview},"+
                "search:{search},"+
                "onSelectRow:{onSelectRow},"+
                "gridComplete:{gridComplete}"+
            "})"+
            ".navGrid('#{pagerId}',{navGridOptions},{editOptionsA},{addOptionsA},{delOptionsA})"+
            ".navButtonAdd(\"#{pagerId}\",{caption:\"\",title:\"Поиск\", buttonicon :'ui-icon-search',"+
            "        onClickButton:function(){"+
            "                mygrid[0].toggleToolbar()"+
            "        } "+
            "})"+
            ";"+

            "jQuery(\"#{navId}\").jqGrid('navGrid','#{pagerId}', "+
            "    {}, /*options*/ "+
            "    {editOptionsA}, /*edit options*/"+
            "    {addOptionsA}, /*add options*/"+
            "    {delOptionsA}, /*del options*/"+
            "    {} /*search options*/"+
            ");"+
            "jQuery(\"#{navId}\").jqGrid('filterToolbar', { searchOnEnter: true, enableClear: false }); "+
        
            "mygrid[0].toggleToolbar();",
        
        /**
     * опциональные параметры для чекбоксов/радио
     * @var array
     */
    customInputsParams: {},
    
    /**
     * Html для чекбоксов/радио
     * @var string 
     */
     customInputsHtml: "function MultiCheckElem{prefix}( value, options )"+
        "{"+
        "  var ctl = '';"+
        "   var ckboxAry = options.list.split(';');"+

        "   for ( var i in ckboxAry )"+
        "   {"+
        "      var item = ckboxAry[i];"+
        "      var ckboxAryDet = item.split(':');"+
        "      ctl += '<input type=\"{inputType}\" ';"+

        "      if ( value.indexOf(ckboxAryDet[1]) != -1 )"+
        "         ctl += 'checked=\"checked\" ';"+
        "      ctl += 'value=\"' + ckboxAryDet[0] + '\" class=\"' + ckboxAryDet[1] + '\"> ' + ckboxAryDet[1] + '</input><br />&nbsp;';"+
        "   }"+

        "   ctl = ctl.replace( /<br \\/>&nbsp;$/, '' );"+
        "   return ctl;"+
        "}"+

        "function MultiCheckVal{prefix}(elem, action, val)"+
        "{"+
        "/*alert('elem='+elem);"+
        "alert('action='+action);"+
        "alert('val='+val);*/"+

        "   var items = '';"+
        "   if (action == 'get')"+
        "   {"+
        "      for (var i in elem)"+
        "      {"+
        "         if (elem[i].tagName == 'INPUT' && elem[i].checked )"+
        "            items += elem[i].value + ',';"+
        "      }"+

        "      items = items.replace(/,$/, '');"+
        "   }"+
        "   else"+
        "   {"+
        "      for (var i in elem)"+
        "      {"+
        "         if (elem[i].tagName == 'INPUT')"+
        "         {"+
        "            if (val.indexOf(elem[i].className.replace(' customelement','')) == -1)"+
        "               elem[i].checked = false;"+
        "            else"+
        "               elem[i].checked = true;"+
        "         }"+
        "      }"+
        "   }"+

        "   return items;"+
        "}",

    /**
     * опции грида
     * @var array
     */
    params:{},
    paramsSample: {
        'datatype' : 'json',
        'mtype' : 'POST',
        'navId' : 'navgrid',
        'pagerId' : 'pagernav',
        'colNames' : "'№','Название'",
        'colModel' : {
            'id': {
                'name':'id',
                'index':'id', 
                'width':'25',
                'editable':'false',
                'editoptions':{
                        'readonly' : 'true',
                        'size' : '10'
                        }
                },
            'name': {
                'name':'name',
                'index':'name', 
                'width':'300',
                'editable':'true',
                'editoptions':{
                        'size' : '25'
                        }
                }
            },
            'rowNum' : '10',
            'rowList': [
                    '10',
                    '20',
                    '30',
                    '40',
                    '50'
                    ],
            'sortname' : 'id',
            'viewrecords' : 'true',
            'sortorder' : 'asc',
            'caption' : 'Категории',
            'url' : 'ajax.php',
            'editurl' : 'ajax.php',
            'height' : "300",
            'multiselect' : 'false',
            'multiboxonly' : 'false',
            'gridview' : 'true',
            'search' : 'false',
            'onSelectRow':'""',
            'gridComplete':'""',
            'navGridOptions' : {
                    'edit' : 'true',
                    'add' : 'true',
                    'del' : 'true',
                    'search' : 'false',
                    'refresh' : 'true'
                },
            'editOptionsA' : {
                    'width' : '450',
                    'height' : '410',
                    'reloadAfterSubmit' : 'true',
                    'closeAfterEdit' : 'true'
                },
            'addOptionsA' : {
                        'width' : '450',
                        'height' : '410',
                        'reloadAfterSubmit' : 'true',
                        'closeAfterAdd' : 'true'
                    },
            'delOptionsA' : {
                        'reloadAfterSubmit' : 'false'
                    }
            
    },
    /**
     * настраиваем опции грида
     * @param array $params 
     */
    setParams:function(newParams){
        this.params = this.paramsSample;
        
        for (var objectKey in newParams)
        {
            this.params[objectKey] = newParams[objectKey];
        }
    },
    
    /**
     * настраиваем опции чекбоксов/радио
     * @param array $params 
     */
    addCustomInputsParams: function(newParams){
        for (var key in newParams)
        {
            this.customInputsParams[key] = newParams[key];
        }
    },
    
    /**
     * формируем HTML на выдачу
     * @return string 
     */
    getHTML:function(){
        // сперва яваскрипт для нестандартных инпутов
        var customInputsScript = '';
        for (var key in this.customInputsParams)
        {
            var value = this.customInputsParams[key];
            
            var customInputsHtmlTemp = this.customInputsHtml;
            customInputsHtmlTemp = php.str_replace('{prefix}', value['prefix'], customInputsHtmlTemp);
            customInputsHtmlTemp = php.str_replace('{inputType}', value['inputType'], customInputsHtmlTemp);
            customInputsScript += customInputsHtmlTemp;
            
        }
        
        var html = this.htmlCode;
        var script = this.jsCode;
        
        for (var objectKey in this.params)
        {
            var object = this.params[objectKey];
            if(php.is_string(object)){
                    html = php.str_replace('{'+objectKey+'}', object, html);
                    script = php.str_replace('{'+objectKey+'}', object, script);
            }else{
                //обрабатываем массив
                var replace = this.formArray(object);
                script = php.str_replace('{'+objectKey+'}', replace, script);
            }
        }
        //return $customInputsHtml.$html;
        var result = {
            html:html,
            script:customInputsScript+script
        };
        return result;
    },
    /**
     * собираем массив в строку
     * @param array $array
     * @return string 
     */
    formArray:function(customArray){
        var string = '';
        var i = 0;
        var arrayType = 'numbered';
        var separator = '';
        
        for (var key in customArray)
        {
            var value = customArray[key];
            if(!php.is_string(value)){
                value = this.formArray(value);
            }
            
            if(i==0){
                separator = '';
            }else{
                separator = ',';
            }
            i++;
            
            if(php.is_int(key)){
                key='';
            }else{
                key+=':';
                arrayType = 'associative';
            }
            
            string+= separator+key+value+'';
        }
        if(arrayType == 'associative'){
            string = '{'+string+'}';
        }
        
        return string;
    }
}